<?php

namespace App\Http\Controllers\security;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateValidate;
use App\Http\Requests\ResetPasswordValidate;
use App\Http\Requests\UserValidate;
use App\Models\Enterprise;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:usuario_acceder')->only('index');
        $this->middleware('permission:usuario_ver')->only('show', 'list');
        $this->middleware('permission:usuario_crear|usuario_actualizar')->only('store');
        $this->middleware('permission:usuario_crear')->only('add');
        $this->middleware('permission:usuario_actualizar')->only('edit', 'storePermission', 'storePassword');
        $this->middleware('permission:usuario_borrar')->only('destroy');
    }

    public function index(): View
    {
        $roles = Role::select('id', 'name')->orderBy('name')->get();
        $specialties = Specialty::select('id', 'descripcion')->orderBy('descripcion')->get();

        return view('security.users.index', compact('roles', 'specialties'));
    }

    public function profile(): View
    {
        $user = auth()->user();
        $es = Specialty::all();

        return view('security.users.profile', compact('user', 'es'));
    }

    public function updateProfile(ProfileUpdateValidate $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::findOrFail(auth()->id());
        $path = null;
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $image = $request->file('avatar');
            $cleanName = $this->cleanFileName($image->getClientOriginalName());
            $path = $image->storeAs('users', $cleanName, 'public');
        }

        $signaturePath = null;
        if ($request->hasFile('firma_digital')) {
            if ($user->firma_digital) {
                Storage::disk('public')->delete($user->firma_digital);
            }
            $sigFile = $request->file('firma_digital');
            $cleanSigName = time().'_'.$this->cleanFileName($sigFile->getClientOriginalName());
            $signaturePath = $sigFile->storeAs('signatures', $cleanSigName, 'public');
        }

        $updateData = [
            'name' => $validated['name'],
            'biografia' => $validated['biografia'] ?? null,
            'cmp' => $validated['cmp'] ?? null,
            'rne' => $validated['rne'] ?? null,
        ];
        if (! empty($validated['specialty'])) {
            $updateData['specialty'] = $validated['specialty'];
        }
        if ($path) {
            $updateData['avatar'] = $path;
        }
        if ($signaturePath) {
            $updateData['firma_digital'] = $signaturePath;
        }

        $user->update($updateData);

        return response()->json([
            'status' => true,
            'type' => 'success',
            'messages' => 'Perfil y credenciales actualizados correctamente',
            'signature_url' => $user->digital_signature_url,
            'avatar_url' => $user->profile_photo_url,
            'route' => route('profile.edit'),
        ], 200);
    }

    public function add(): View
    {
        $rl = Role::all();
        $es = Specialty::all();

        return view('security.users.add', compact('rl', 'es'));
    }

    public function edit(int $id): View
    {
        $rl = Role::all();
        $es = Specialty::all();
        $ur = User::getUserByRole($id);
        $us = User::findOrFail($id);

        return view('security.users.edit', compact('rl', 'es', 'ur', 'us'));
    }

    public function permissions(int $id): View
    {
        $user = User::with(['permissions', 'roles.permissions'])->findOrFail($id);
        // Obtener todos los permisos del sistema
        $allPermissions = Permission::all();
        // Permisos directos del usuario
        $directPermissions = $user->permissions;
        // Permisos heredados de roles
        $rolePermissions = $user->getPermissionsViaRoles();
        // Combinar todos los permisos asignados (directos y por roles)
        $allAssignedPermissions = $directPermissions->merge($rolePermissions)->unique('id');
        // Permisos disponibles (no asignados ni directos ni por roles)
        $availablePermissions = $allPermissions->diff($allAssignedPermissions);

        return view('security.users.role', compact('user', 'availablePermissions', 'directPermissions', 'rolePermissions', 'allAssignedPermissions'));
    }

    public function assignRole(int $userId, string $roleName): JsonResponse
    {
        $user = User::find($userId);
        if (! $user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $user->assignRole($roleName);

        return response()->json(['message' => "Rol '{$roleName}' asignado al usuario correctamente"]);
    }

    public function list(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role');
        $query = User::with(['roles', 'lastLogin'])->orderBy('id', 'desc');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('username', 'LIKE', "%{$search}%")
                    ->orWhereHas('roles', function ($rq) use ($search) {
                        $rq->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if (! empty($roleFilter)) {
            $query->whereHas('roles', function ($rq) use ($roleFilter) {
                $rq->where('name', $roleFilter);
            });
        }

        $currentUser = auth()->user();
        $permissions = [
            'create' => $currentUser ? $currentUser->can('usuario_crear') : false,
            'update' => $currentUser ? $currentUser->can('usuario_actualizar') : false,
            'delete' => $currentUser ? $currentUser->can('usuario_borrar') : false,
            'assign_roles' => $currentUser ? $currentUser->hasRole('administrador') : false,
        ];

        $pageSize = (int) $request->input('pageSize', $request->input('jtPageSize', 0));
        if ($pageSize > 0) {
            $page = (int) $request->input('page', 1);
            if ($request->has('jtStartIndex') && ! $request->has('page')) {
                $page = (int) floor($request->input('jtStartIndex') / $pageSize) + 1;
            }

            $paginator = $query->paginate($pageSize, ['*'], 'page', $page);
            $formattedItems = collect($paginator->items())->map(function ($item) {
                $primaryRole = $item->roles->first();

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'username' => $item->username,
                    'email' => $item->email,
                    'role_name' => $primaryRole?->name ?? 'Sin rol',
                    'role_id' => $primaryRole?->id ?? null,
                    'avatar_url' => $item->profile_photo_url,
                    'last_login' => $item->lastLogin?->last_login ?? 'Nunca',
                    'created_at' => $item->created_at ? $item->created_at->format('Y-m-d H:i') : null,
                    'updated_at' => $item->updated_at ? $item->updated_at->format('Y-m-d H:i') : null,
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $formattedItems,
                'total' => $paginator->total(),
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'pageSize' => $paginator->perPage(),
                'permissions' => $permissions,
                'Result' => 'OK',
                'TotalRecordCount' => $paginator->total(),
                'Records' => $formattedItems,
            ]);
        }

        $results = $query->get();
        $formattedItems = $results->map(function ($item) {
            $primaryRole = $item->roles->first();

            return [
                'id' => $item->id,
                'name' => $item->name,
                'username' => $item->username,
                'email' => $item->email,
                'role_name' => $primaryRole?->name ?? 'Sin rol',
                'role_id' => $primaryRole?->id ?? null,
                'avatar_url' => $item->profile_photo_url,
                'last_login' => $item->lastLogin?->last_login ?? 'Nunca',
                'created_at' => $item->created_at ? $item->created_at->format('Y-m-d H:i') : null,
                'updated_at' => $item->updated_at ? $item->updated_at->format('Y-m-d H:i') : null,
            ];
        });

        $aaData = $results->map(function ($item, $index) use ($currentUser) {
            $buttons = '';
            if ($currentUser && $currentUser->hasRole('administrador')) {
                $rolesRoute = route('security.users.role', ['id' => $item->id]);
                $buttons .= sprintf(
                    '<a href="%s" class="btn btn-sm btn-info assign-roles btn-md" title="Asignar roles"><i class="bi bi-person-gear"></i></a>&nbsp;',
                    htmlspecialchars($rolesRoute, ENT_QUOTES, 'UTF-8')
                );
            }
            if ($currentUser && $currentUser->can('usuario_actualizar')) {
                $editRoute = route('security.users.edit', ['id' => $item->id]);
                $buttons .= sprintf(
                    '<a href="%s" class="btn btn-sm btn-warning update-row btn-md" title="Editar"><i class="bi bi-pencil-square"></i></a>&nbsp;',
                    htmlspecialchars($editRoute, ENT_QUOTES, 'UTF-8')
                );
            }
            if ($currentUser && $currentUser->can('usuario_borrar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-user btn-md" value="%s" title="Eliminar"><i class="bi bi-trash"></i></button>',
                    htmlspecialchars((string) $item->id, ENT_QUOTES, 'UTF-8')
                );
            }

            $primaryRole = $item->roles->first();
            $lastLoginStr = $item->lastLogin?->last_login ?? 'Nunca';

            return [
                $index + 1,
                htmlspecialchars($item->name, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($item->email, ENT_QUOTES, 'UTF-8'),
                sprintf('<span class="badge badge-success">%s</span>', htmlspecialchars($primaryRole?->name ?? 'Sin rol', ENT_QUOTES, 'UTF-8')),
                $lastLoginStr,
                $buttons ?: '<span class="text-muted">No autorizado</span>',
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $formattedItems,
            'total' => $results->count(),
            'permissions' => $permissions,
            'sEcho' => 1,
            'iTotalRecords' => $results->count(),
            'iTotalDisplayRecords' => $results->count(),
            'aaData' => $aaData,
            'Result' => 'OK',
            'TotalRecordCount' => $results->count(),
            'Records' => $formattedItems,
        ]);
    }

    public function store(UserValidate $request): JsonResponse
    {
        $validated = $request->validated();
        $id = $request->input('userId');
        // Procesar la imagen si existe
        $path = null;
        $imagePath = null;
        if ($request->hasFile('avatar')) {
            // Eliminar imagen anterior si estamos actualizando
            if ($id) {
                $user = User::find($id);
                if ($user && $user->avatar) {
                    Storage::delete($user->avatar);
                }
            }
            // Obtener el archivo
            $image = $request->file('avatar');
            // Obtener nombre original y extensión
            $originalName = $image->getClientOriginalName();
            $extension = $image->getClientOriginalExtension();
            // Limpiar el nombre del archivo (opcional, para seguridad)
            $cleanName = $this->cleanFileName($originalName);
            // Subir imagen manteniendo nombre original
            $path = $image->storeAs('users', $cleanName, 'public');
        }

        $signaturePath = null;
        if ($request->hasFile('firma_digital')) {
            if ($id) {
                $user = User::find($id);
                if ($user && $user->firma_digital) {
                    Storage::disk('public')->delete($user->firma_digital);
                }
            }
            $sigFile = $request->file('firma_digital');
            $cleanSigName = time().'_'.$this->cleanFileName($sigFile->getClientOriginalName());
            $signaturePath = $sigFile->storeAs('signatures', $cleanSigName, 'public');
        }

        $existingUser = $id ? User::find($id) : null;
        $nickname = $existingUser ? $existingUser->username : $this->createNickname($validated['name']);
        $enterprise = Enterprise::find(1);
        $domain = $enterprise?->pagina_web ?: 'neumolav.com';
        $email = $existingUser ? $existingUser->email : $nickname.'@'.$domain;
        $data = array_merge($validated, [
            'username' => $nickname,
            'email' => $email,
            'password' => $request->password ? Hash::make($request->password) : ($existingUser ? $existingUser->password : Hash::make('password')),
            'avatar' => $path ? $path : ($existingUser ? $existingUser->avatar : null),
            'firma_digital' => $signaturePath ? $signaturePath : ($existingUser ? $existingUser->firma_digital : null),
        ]);

        DB::beginTransaction();
        try {
            $result = User::updateOrCreate(['id' => $id], $data);
            // Actualizar el rol del usuario si se proporciona en la solicitud
            if ($request->has('role_id')) {
                // Eliminar todos los roles actuales (asumiendo que un usuario solo tiene un rol)
                DB::table('model_has_roles')->where('model_id', $result->id)->delete();
                // Asignar el nuevo rol
                DB::table('model_has_roles')->insert([
                    'role_id' => $request->input('role_id'),
                    'model_type' => 'App\Models\User',
                    'model_id' => $result->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => (bool) $result,
                'type' => $result ? 'success' : 'error',
                'messages' => $result ? ($result->wasChanged() ? 'El usuario ha sido actualizado' : 'Se ha añadido un nuevo usuario') : 'Recargue la página y vuelva a intentarlo',
                'avatar' => $path ? Storage::url($path) : ($result->avatar ? Storage::url($result->avatar) : null),
                'route' => route('security.users.home'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($imagePath) {
                Storage::delete($imagePath);
            }

            return response()->json([
                'status' => false,
                'type' => 'error',
                'messages' => $e->getMessage(),
            ], 500);
        }
    }

    public function storePassword(ResetPasswordValidate $request, User $user): JsonResponse
    {
        $validated = $request->validated();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json([
            'status' => true,
            'type' => 'success',
            'message' => 'La contraseña ha sido actualizada',
            'route' => route('security.users.home'),
        ], 200);
    }

    private function cleanFileName(string $filename): string
    {
        // Remover caracteres especiales y espacios
        $clean = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        // Limitar longitud del nombre
        $clean = substr($clean, 0, 100);

        return $clean;
    }

    public function storePermission(User $user, Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'permissions' => 'nullable|string',
            ]);

            $permissionIds = $request->permissions ? explode(',', $request->permissions) : [];
            $permissions = Permission::whereIn('id', $permissionIds)->get();
            $user->syncPermissions($permissions);
            DB::commit();

            return response()->json([
                'status' => true,
                'type' => 'success',
                'message' => 'Los permisos del usuario han sido actualizados',
                'route' => route('security.users.home'),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => 'Error al actualizar permisos: '.$e->getMessage(),
            ], 500);
        }
    }

    public function createNickname(string $name): string
    {
        $nickname = '';
        $count = mb_substr_count($name, ' ');
        $positionW = explode(' ', $name);

        if ($count == 1) {
            $w = substr($positionW[0], 0, -3);
            $nickname = $w.$positionW[1];
        } elseif ($count == 2) {
            $nickname = $positionW[0][0].$positionW[1].$positionW[2][0];
        } elseif ($count == 3) {
            $nickname = $positionW[0][0].$positionW[1].$positionW[2].$positionW[3][0];
        } elseif ($count == 4) {
            $w = substr($positionW[4], 0, -1);
            $nickname = $positionW[0][0].$positionW[3].$w;
        } else {
            // Caso por defecto: usar el primer nombre completo
            $nickname = strtolower(str_replace(' ', '', $name));
        }
        // Eliminar caracteres no alfanuméricos
        $nickname = preg_replace('/[^a-zA-Z0-9]/', '', $nickname);
        if (User::where('username', $nickname)->count() > 1) {
            $nickname .= rand(1, 99);
        }

        return strtolower($nickname);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::with(['roles', 'lastLogin'])->findOrFail($id);
        $primaryRole = $user->roles->first();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'biografia' => $user->biografia,
            'specialty' => $user->specialty,
            'cmp' => $user->cmp,
            'rne' => $user->rne,
            'role_id' => $primaryRole?->id,
            'role_name' => $primaryRole?->name ?? 'Sin rol',
            'avatar_url' => $user->profile_photo_url,
            'last_login' => $user->lastLogin?->last_login ?? 'Nunca',
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        if (auth()->id() == $id) {
            return response()->json([
                'status' => false,
                'type' => 'error',
                'messages' => 'No puede eliminar su propia cuenta de usuario',
                'message' => 'No puede eliminar su propia cuenta de usuario',
            ], 422);
        }

        $result = User::findOrFail($id);
        $result->delete();

        return response()->json([
            'status' => true,
            'type' => 'success',
            'messages' => 'El usuario fue eliminado correctamente',
            'message' => 'El usuario fue eliminado correctamente',
        ], 200);
    }
}
