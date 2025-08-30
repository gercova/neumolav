<?php

namespace App\Http\Controllers\security;

use App\Http\Controllers\Controller;
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
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersController extends Controller {
    
    public function __construct() {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:usuario_acceder')->only('index');
        $this->middleware('permission:usuario_ver')->only('show');
        $this->middleware('permission:usuario_crear')->only('add', 'store', 'storePermission');
        $this->middleware('permission:usuario_actualizar')->only('edit', 'store', 'storePermission');
        $this->middleware('permission:usuario_borrar')->only('destroy');
    }

    public function index(): View {
        return view('security.users.index');
    }

    public function add(): View {
        $rl = Role::all();
        $es = Specialty::all();
        return view('security.users.add', compact('rl', 'es'));
    }

    public function edit(int $id): View {
        $rl = Role::all();
        $es = Specialty::all();
        $ur = User::getUserByRole($id);
        $us = User::findOrFail($id);
        return view('security.users.edit', compact('rl', 'es', 'ur', 'us'));
    }

    /*public function permissions(int $id): View {
        $user = User::findOrFail($id);
        // Permisos que el usuario no tiene (ni directos ni por roles)
        $availablePermissions = Permission::whereDoesntHave('users', function($query) use ($user) {
            $query->where('users.id', $user->id);
        })->whereDoesntHave('roles', function($query) use ($user) {
            $query->whereIn('roles.id', $user->roles->pluck('id'));
        })->get();
        // Permisos asignados directamente al usuario
        $directPermissions = $user->permissions;
        // Permisos que el usuario tiene a través de su rol
        $rolePermissions = $user->getPermissionsViaRoles();
        
        return view('security.users.role', compact('user', 'availablePermissions', 'directPermissions', 'rolePermissions'));
    }*/

    public function permissions(int $id): View {
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

    public function assignRole($userId, $roleName): JsonResponse {
        $user = User::find($userId);
        if(!$user) return response()->json(['error' => 'Usuario no encontrado'], 404);
        $user->assignRole($roleName);
        return response()->json(['message' => "Rol '{$roleName}' asignado al usuario correctamente"]);
    }

    public function list(): JsonResponse {
        $results    = DB::table('view_user_roles_last_login')->get();
        $data       = $results->map(function ($item, $index) {
            $currentUser = auth()->user();
            $buttons = '';
            if ($currentUser->hasRole('administrador')) {
                $rolesRoute = route('security.users.role', ['id' => $item->id]);
                $buttons .= sprintf(
                    '<a href="%s" class="btn btn-sm btn-info assign-roles btn-md" title="Asignar roles">
                        <i class="bi bi-person-gear"></i>
                    </a>&nbsp;',
                    htmlspecialchars($rolesRoute, ENT_QUOTES, 'UTF-8')
                );
            }
            if ($currentUser->can('usuario_actualizar')) {
                $editRoute = route('security.users.edit', ['id' => $item->id]);
                $buttons .= sprintf(
                    '<a href="%s" class="btn btn-sm btn-warning update-row btn-md">
                        <i class="bi bi-pencil-square"></i>
                    </a>&nbsp;',
                    htmlspecialchars($editRoute, ENT_QUOTES, 'UTF-8')
                );
            }
            if ($currentUser->can('usuario_borrar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-user btn-md" value="%s" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            return [
                $index + 1,
                $item->name,
                $item->email,
                sprintf(
                    '<span class="badge badge-success">%s</span>',
                    $item->rol
                ),
                $item->last_login ?: 'Nunca',
                $buttons ?: '<span class="text-muted">No autorizado</span>'
            ];
        });

        return response()->json([
            'sEcho'                 => 1,
            'iTotalRecords'         => $data->count(),
            'iTotalDisplayRecords'  => $data->count(),
            'aaData'                => $data,
        ]);
    }

    public function store(UserValidate $request): JsonResponse {
        $validated  = $request->validated();
        $id         = $request->input('userId');
        // Procesar la imagen si existe
        $imagePath  = null;
        if ($request->hasFile('avatar')) {
            // Eliminar imagen anterior si estamos actualizando
            if ($id) {
                $user = User::find($id);
                if ($user && $user->avatar) Storage::delete($user->avatar);
            }
            // Guardar nueva imagen
            // $imagePath = $request->file('avatar')->store('public/users');
            // $imagePath = copy($image->getRealPath(), $destinationPath . '/' . $imageName);
            // Obtener el archivo
            $image = $request->file('avatar');
            // Obtener nombre original y extensión
            $originalName = $image->getClientOriginalName();
            $extension = $image->getClientOriginalExtension();
            // Limpiar el nombre del archivo (opcional, para seguridad)
            $cleanName = $this->cleanFileName($originalName);
            // Subir imagen manteniendo nombre original
            $path = $image->storeAs('users', $cleanName, 'public');
            // Obtener URL pública
            //$url = Storage::disk('public')->url($path);

        }

        $data           = array_merge($validated, [
            'username'  => $nickename = $this->createNickname($validated['name']),
            'email'     => $nickename.'@'.Enterprise::findOrFail(1)->pagina_web,
            'password'  => $request->password ? Hash::make($request->password) : Hash::make('password'),
            'avatar'    => $path ? $path : ($id ? User::find($id)->avatar : null)
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
                    'role_id'       => $request->input('role_id'),
                    'model_type'    => 'App\Models\User',
                    'model_id'      => $result->id
                ]);
            }
        
            DB::commit();
            return response()->json([
                'status'    => (bool) $result,
                'type'      => $result ? 'success' : 'error',
                'messages'  => $result ? ($result->wasChanged() ? 'El usuario ha sido actualizado' : 'Se ha añadido un nuevo usuario') : 'Recargue la página y vuelva a intentarlo',
                'avatar'    => $imagePath ? Storage::url($imagePath) : ($result->avatar ? Storage::url($result->avatar) : null),
                'route'     => route('security.users.home'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($imagePath) Storage::delete($imagePath);
            return response()->json([
                'status'    => false,
                'type'      => 'error',
                'messages'  => $e->getMessage(),
            ], 500);
        }
    }

    public function storePassword(ResetPasswordValidate $request, User $user): JsonResponse {
        $validated = $request->validated();
        $user->password = Hash::make($validated['password']);
        $user->save();

        /*dd(
            $user,
            $validated
        );*/

        return response()->json([
            'status'    => true,
            'type'      => 'success',
            'message'   => 'La contraseña ha sido actualizada',
            'route'     => route('security.users.home'),
        ], 200);
    }

    private function cleanFileName($filename) {
        // Remover caracteres especiales y espacios
        $clean = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename); 
        // Limitar longitud del nombre
        $clean = substr($clean, 0, 100);
        return $clean;
    }

    public function storePermission(User $user, Request $request): JsonResponse {
        DB::beginTransaction();
        try {
            $request->validate([
                'permissions' => 'nullable|string'
            ]);

            $permissionIds  = $request->permissions ? explode(',', $request->permissions) : [];
            $permissions    = Permission::whereIn('id', $permissionIds)->get();
            
            $user->syncPermissions($permissions);
            DB::commit();

            return response()->json([
                'status'    => true,
                'type'      => 'success',
                'message'   => 'Los permisos del usuario han sido actualizados',
                'route'     => route('security.users.home'),
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'type'      => 'error',
                'message'   => 'Error al actualizar permisos: ' . $e->getMessage(),
            ], 500);
        }
    }

    /*public function assignRolePermission(Request $request): JsonResponse {
        $user = User::findOrFail($request->id_usuario);
        // Asignar roles
        if ($request->has('roles')) $user->syncRoles($request->roles);
        // Asignar permisos
        if ($request->has('permissions')) $user->syncPermissions($request->permissions);

        return response()->json(['message' => 'Roles y permisos asignados correctamente']);
    }*/
    
    public function createNickname($name): string {
        $nickname   = '';
        $count      = mb_substr_count($name, ' ');
        $positionW  = explode(' ', $name);
    
        if ($count == 1) { 
            $w = substr($positionW[0], 0, -3); 
            $nickname = $w . $positionW[1];
        } elseif ($count == 2) {
            $nickname = $positionW[0][0] . $positionW[1] . $positionW[2][0];
        } elseif ($count == 3) {
            $nickname = $positionW[0][0] . $positionW[1] . $positionW[2] . $positionW[3][0];
        } elseif ($count == 4) {
            $w = substr($positionW[4], 0, -1);
            $nickname = $positionW[0][0] . $positionW[3] . $w;
        } else {
            // Caso por defecto: usar el primer nombre completo
            $nickname = strtolower(str_replace(' ', '', $name));
        }
        // Eliminar caracteres no alfanuméricos
        $nickname = preg_replace('/[^a-zA-Z0-9]/', '', $nickname);
        if(User::where('username', $nickname)->count() > 0) {
            $nickname .= rand(1, 99);
        }
    
        return strtolower($nickname);
    }

    /*public function createUsername($name): string {
        // $nickname = $this->createSummaryName($name);
        // $count    = User::where('username', 'like', '%' . $nickname . '%')->count();
        // return $count > 0 ? $nickname . $count : $nickname;
    }*/

    public function show($id): JsonResponse {
        return response()->json(User::findOrFail($id), 200);
    }

    public function destroy($id): JsonResponse {
        $result = User::findOrFail($id);
        $result->delete();
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? 'El usuario fue eliminado' : 'No se pudo eliminar el usuario'
        ], 200);
    }
}
