<?php

namespace App\Http\Controllers\security;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesController extends Controller {

    public function __construct() {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:rol_acceder')->only('index');
        $this->middleware('permission:rol_ver')->only('list', 'show', 'getPermissions', 'getUsers');
        $this->middleware('permission:rol_crear')->only('store');
        $this->middleware('permission:rol_actualizar')->only('store', 'syncPermissions');
        $this->middleware('permission:rol_borrar')->only('destroy');
    }

    /**
     * Vista principal del submodulo de roles.
     */
    public function index(): View {
        return view('security.roles.index');
    }

    /**
     * Listado de roles para DataTable.
     */
    public function list(): JsonResponse {
        $roles = Role::withCount(['permissions', 'users'])->get();

        $currentUser = auth()->user();
        $data = $roles->map(function ($role, $index) use ($currentUser) {
            $buttons = '';

            if ($currentUser->can('rol_actualizar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-warning btn-edit-role mr-1" 
                        data-id="%s" title="Editar rol">
                        <i class="bi bi-pencil-square"></i>
                    </button>',
                    $role->id
                );
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-info btn-assign-permissions mr-1" 
                        data-id="%s" data-name="%s" title="Asignar permisos">
                        <i class="bi bi-key-fill"></i>
                    </button>',
                    $role->id,
                    htmlspecialchars($role->name, ENT_QUOTES, 'UTF-8')
                );
            }

            $buttons .= sprintf(
                '<button type="button" class="btn btn-sm btn-secondary btn-view-users mr-1" 
                    data-id="%s" data-name="%s" title="Ver usuarios">
                    <i class="bi bi-people-fill"></i>
                </button>',
                $role->id,
                htmlspecialchars($role->name, ENT_QUOTES, 'UTF-8')
            );

            if ($currentUser->can('rol_borrar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger btn-delete-role" 
                        data-id="%s" title="Eliminar rol">
                        <i class="bi bi-trash"></i>
                    </button>',
                    $role->id
                );
            }

            return [
                $index + 1,
                htmlspecialchars($role->name, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($role->guard_name, ENT_QUOTES, 'UTF-8'),
                sprintf('<span class="badge badge-info">%s</span>', $role->permissions_count),
                sprintf('<span class="badge badge-success">%s</span>', $role->users_count),
                $role->created_at->format('Y-m-d H:i'),
                $buttons ?: '<span class="text-muted">Sin acciones</span>',
            ];
        });

        return response()->json([
            'sEcho'                => 1,
            'iTotalRecords'        => $data->count(),
            'iTotalDisplayRecords' => $data->count(),
            'aaData'               => $data,
        ]);
    }

    /**
     * Retorna datos de un rol (para editar en modal).
     */
    public function show(int $id): JsonResponse {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }

    /**
     * Retorna permisos disponibles y asignados al rol (para modal dual-panel).
     */
    public function getPermissions(int $id): JsonResponse {
        $role               = Role::findOrFail($id);
        $assignedIds        = $role->permissions->pluck('id');
        $allPermissions     = Permission::orderBy('name')->get();

        $assigned   = $allPermissions->whereIn('id', $assignedIds)->values();
        $available  = $allPermissions->whereNotIn('id', $assignedIds)->values();

        return response()->json([
            'role'      => $role,
            'assigned'  => $assigned,
            'available' => $available,
        ]);
    }

    /**
     * Retorna usuarios que tienen el rol (para modal de usuarios).
     */
    public function getUsers(int $id): JsonResponse {
        $role  = Role::findOrFail($id);
        $users = User::role($role->name)->get(['id', 'name', 'email']);

        return response()->json([
            'role'  => $role->name,
            'users' => $users,
        ]);
    }

    /**
     * Crear o actualizar un rol.
     */
    public function store(Request $request): JsonResponse {
        $request->validate([
            'name'       => 'required|string|max:125',
            'guard_name' => 'required|string|max:125',
        ]);

        $roleId = $request->input('roleId');
        $result = Role::updateOrCreate(
            ['id' => $roleId],
            [
                'name'       => $request->input('name'),
                'guard_name' => $request->input('guard_name'),
            ]
        );

        return response()->json([
            'status'   => (bool) $result,
            'type'     => $result ? 'success' : 'error',
            'messages' => $result
                ? ($result->wasChanged() ? 'El rol ha sido actualizado' : 'Se ha añadido un nuevo rol')
                : 'Recargue la página e intente de nuevo',
        ]);
    }

    /**
     * Sincroniza los permisos de un rol.
     */
    public function syncPermissions(Request $request, int $id): JsonResponse {
        $request->validate([
            'permissions' => 'nullable|string',
        ]);

        $role        = Role::findOrFail($id);
        $permIds     = $request->permissions ? explode(',', $request->permissions) : [];
        $permissions = Permission::whereIn('id', $permIds)->get();

        $role->syncPermissions($permissions);

        // Limpiar caché de Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'status'   => true,
            'type'     => 'success',
            'messages' => 'Los permisos del rol han sido actualizados correctamente',
        ]);
    }

    /**
     * Eliminar un rol.
     */
    public function destroy(int $id): JsonResponse {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'status'  => true,
            'type'    => 'success',
            'message' => 'El rol fue eliminado correctamente',
        ]);
    }
}
