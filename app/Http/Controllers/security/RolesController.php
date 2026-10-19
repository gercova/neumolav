<?php

namespace App\Http\Controllers\security;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleValidate;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:rol_acceder')->only('index');
        $this->middleware('permission:rol_ver')->only('list', 'show', 'getPermissions', 'getUsers');
        $this->middleware('permission:rol_crear|rol_actualizar')->only('store');
        $this->middleware('permission:rol_actualizar')->only('syncPermissions');
        $this->middleware('permission:rol_borrar')->only('destroy');
    }

    /**
     * Vista principal del submodulo de roles.
     */
    public function index(): View
    {
        return view('security.roles.index');
    }

    /**
     * Listado de roles con soporte SPA (paginación, filtros) y compatibilidad DataTables.
     */
    public function list(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $query = Role::withCount(['permissions', 'users'])->orderBy('id', 'desc');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('guard_name', 'LIKE', "%{$search}%");
            });
        }

        $user = auth()->user();
        $permissions = [
            'create' => $user ? $user->can('rol_crear') : false,
            'update' => $user ? $user->can('rol_actualizar') : false,
            'delete' => $user ? $user->can('rol_borrar') : false,
        ];

        $pageSize = (int) $request->input('pageSize', $request->input('jtPageSize', 0));
        if ($pageSize > 0) {
            $page = (int) $request->input('page', 1);
            if ($request->has('jtStartIndex') && ! $request->has('page')) {
                $page = (int) floor($request->input('jtStartIndex') / $pageSize) + 1;
            }

            $paginator = $query->paginate($pageSize, ['*'], 'page', $page);
            $formattedItems = collect($paginator->items())->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'guard_name' => $role->guard_name,
                    'permissions_count' => $role->permissions_count,
                    'users_count' => $role->users_count,
                    'created_at' => $role->created_at ? $role->created_at->format('Y-m-d H:i') : null,
                    'updated_at' => $role->updated_at ? $role->updated_at->format('Y-m-d H:i') : null,
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
        $formattedItems = $results->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permissions_count' => $role->permissions_count,
                'users_count' => $role->users_count,
                'created_at' => $role->created_at ? $role->created_at->format('Y-m-d H:i') : null,
                'updated_at' => $role->updated_at ? $role->updated_at->format('Y-m-d H:i') : null,
            ];
        });

        $aaData = $results->map(function ($role, $index) use ($user) {
            $buttons = '';

            if ($user && $user->can('rol_actualizar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-warning btn-edit-role mr-1" data-id="%s" title="Editar rol"><i class="bi bi-pencil-square"></i></button>',
                    htmlspecialchars((string) $role->id, ENT_QUOTES, 'UTF-8')
                );
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-info btn-assign-permissions mr-1" data-id="%s" data-name="%s" title="Asignar permisos"><i class="bi bi-key-fill"></i></button>',
                    htmlspecialchars((string) $role->id, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars((string) $role->name, ENT_QUOTES, 'UTF-8')
                );
            }

            $buttons .= sprintf(
                '<button type="button" class="btn btn-sm btn-secondary btn-view-users mr-1" data-id="%s" data-name="%s" title="Ver usuarios"><i class="bi bi-people-fill"></i></button>',
                htmlspecialchars((string) $role->id, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string) $role->name, ENT_QUOTES, 'UTF-8')
            );

            if ($user && $user->can('rol_borrar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger btn-delete-role" data-id="%s" title="Eliminar rol"><i class="bi bi-trash"></i></button>',
                    htmlspecialchars((string) $role->id, ENT_QUOTES, 'UTF-8')
                );
            }

            return [
                $index + 1,
                htmlspecialchars((string) $role->name, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string) $role->guard_name, ENT_QUOTES, 'UTF-8'),
                sprintf('<span class="badge badge-info">%s</span>', $role->permissions_count),
                sprintf('<span class="badge badge-success">%s</span>', $role->users_count),
                $role->created_at ? $role->created_at->format('Y-m-d H:i') : '',
                $buttons ?: '<span class="text-muted">Sin acciones</span>',
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

    /**
     * Retorna datos de un rol (para editar en modal).
     */
    public function show(int $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        return response()->json($role, 200);
    }

    /**
     * Retorna permisos disponibles y asignados al rol (para modal dual-panel).
     */
    public function getPermissions(int $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $assignedIds = $role->permissions->pluck('id');
        $allPermissions = Permission::orderBy('name')->get(['id', 'name', 'guard_name', 'description']);
        $assigned = $allPermissions->whereIn('id', $assignedIds)->values();
        $available = $allPermissions->whereNotIn('id', $assignedIds)->values();

        return response()->json([
            'role' => $role,
            'assigned' => $assigned,
            'available' => $available,
        ], 200);
    }

    /**
     * Retorna usuarios que tienen el rol (para modal de usuarios).
     */
    public function getUsers(int $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $users = User::role($role->name)->get(['id', 'name', 'email']);

        return response()->json([
            'role' => $role->name,
            'users' => $users,
        ], 200);
    }

    /**
     * Crear o actualizar un rol.
     */
    public function store(RoleValidate $request): JsonResponse
    {
        $validated = $request->validated();
        $id = $request->input('id') ?: $request->input('roleId');

        if (! empty($id)) {
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $validated['name'],
                'guard_name' => $validated['guard_name'],
            ]);

            return response()->json([
                'status' => true,
                'type' => 'success',
                'messages' => 'El rol ha sido actualizado',
                'message' => 'El rol ha sido actualizado',
            ], 200);
        }

        $result = Role::create([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'],
        ]);

        return response()->json([
            'status' => (bool) $result,
            'type' => $result ? 'success' : 'error',
            'messages' => $result ? 'Se ha añadido un nuevo rol' : 'Recargue la página e intente de nuevo',
            'message' => $result ? 'Se ha añadido un nuevo rol' : 'Recargue la página e intente de nuevo',
        ], 200);
    }

    /**
     * Sincroniza los permisos de un rol.
     */
    public function syncPermissions(Request $request, int $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        $permInput = $request->input('permissions');
        if (is_array($permInput)) {
            $permIds = $permInput;
        } elseif (is_string($permInput) && strlen(trim($permInput)) > 0) {
            $permIds = explode(',', $permInput);
        } else {
            $permIds = [];
        }

        $permissions = Permission::whereIn('id', $permIds)->get();
        $role->syncPermissions($permissions);

        // Limpiar caché de Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'status' => true,
            'type' => 'success',
            'messages' => 'Los permisos del rol han sido actualizados correctamente',
            'message' => 'Los permisos del rol han sido actualizados correctamente',
        ], 200);
    }

    /**
     * Eliminar un rol.
     */
    public function destroy(int $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'status' => true,
            'type' => 'success',
            'messages' => 'El rol fue eliminado correctamente',
            'message' => 'El rol fue eliminado correctamente',
        ], 200);
    }
}
