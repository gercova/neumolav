<?php

namespace App\Http\Controllers\security;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleValidate;
use App\Http\Requests\SubmoduleValidate;
use App\Models\Module;
use App\Models\Submodule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class ModulesController extends Controller {

    public function __construct() {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:modulo_acceder')->only('index');
        $this->middleware('permission:modulo_ver')->only('list', 'showModule', 'showSubmodule', 'getModulePermissions', 'getSubmodulePermissions');
        $this->middleware('permission:modulo_crear')->only('storeModule', 'storeSubmodule');
        $this->middleware('permission:modulo_actualizar')->only('syncModulePermissions', 'syncSubmodulePermissions');
        $this->middleware('permission:modulo_borrar')->only('destroyModule', 'destroySubmodule');
    }

    public function index(): View {
        $md = Module::where('deleted_at', NULL)->get();
        return view('security.modules.index', compact('md'));
    }

    public function list(): JsonResponse {
        $resultsM = Module::with('submodules', 'permissions')->get();
        $data     = $resultsM->map(function ($item, $key) {
            // Build submodules inner table
            $submodulesList = '<table class="table table-sm mb-0">';
            foreach ($item->submodules as $sm) {
                $submodulesList .= sprintf(
                    '<tr>
                        <td><span class="badge badge-success">%s</span>&nbsp;%s</td>
                        <td class="text-right">
                            <button class="btn btn-sm btn-info btn-submodule-permissions mr-1" value="%s" data-name="%s" title="Permisos"><i class="bi bi-key"></i></button>
                            <button class="btn btn-sm btn-warning update-row-submodule" value="%s"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn btn-sm btn-danger delete-submodule" value="%s"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>',
                    e($sm->icono),
                    e($sm->descripcion),
                    $sm->id,
                    e($sm->descripcion),
                    $sm->id,
                    $sm->id,
                );
            }
            $submodulesList .= '</table>';

            $assignedCount  = $item->permissions->count();
            $permBadge      = $assignedCount > 0
                ? sprintf('<span class="badge badge-success">%d asignados</span>', $assignedCount)
                : '<span class="badge badge-secondary">Sin permisos</span>';

            return [
                $key + 1,
                e($item->descripcion),
                sprintf('<button type="button" class="btn btn-sm btn-info btn-md">%s</button>', $item->icono),
                $submodulesList,
                $permBadge,
                $item->created_at->format('Y-m-d H:i:s'),
                sprintf(
                    '<button type="button" class="btn btn-sm btn-info btn-module-permissions btn-md mr-1" value="%s" data-name="%s" title="Permisos"><i class="bi bi-key"></i></button>
                     <button type="button" class="btn btn-sm btn-warning update-row-module btn-md" value="%s"><i class="bi bi-pencil-square"></i></button>
                     <button type="button" class="btn btn-sm btn-danger delete-module btn-md" value="%s"><i class="bi bi-trash"></i></button>',
                    $item->id,
                    e($item->descripcion),
                    $item->id,
                    $item->id,
                )
            ];
        });

        return response()->json([
            'sEcho'                => 1,
            'iTotalRecords'        => $data->count(),
            'iTotalDisplayRecords' => $data->count(),
            'aaData'               => $data,
        ]);
    }

    public function storeModule(ModuleValidate $request): JsonResponse {
        $validated = $request->validated();
        $result    = Module::updateOrCreate(['id' => $request->input('moduleId')], $validated);
        return response()->json([
            'status'   => (bool) $result,
            'type'     => $result ? 'success' : 'error',
            'messages' => $result ? ($result->wasChanged() ? 'El módulo ha sido actualizado' : 'Se ha añadido un nuevo módulo') : 'Recargue la página he intente de nuevo',
        ], 200);
    }

    public function storeSubmodule(SubmoduleValidate $request): JsonResponse {
        $validated = $request->validated();
        $result    = Submodule::updateOrCreate(['id' => $request->input('submoduleId')], $validated);
        return response()->json([
            'status'   => (bool) $result,
            'type'     => $result ? 'success' : 'error',
            'messages' => $result ? ($result->wasChanged() ? 'El registro ha sido actualizado' : 'Se ha añadido un nuevo registro') : 'Recargue la página he intente de nuevo',
        ], 200);
    }

    public function showModule(int $id): JsonResponse {
        return response()->json(Module::findOrFail($id), 200);
    }

    public function showSubmodule(int $id): JsonResponse {
        return response()->json(Submodule::findOrFail($id), 200);
    }

    public function destroyModule(int $id): JsonResponse {
        $resultM  = Module::findOrFail($id);
        $resultSM = Submodule::where('module_id', $id)->get();
        if ($resultSM->count() > 0) {
            if ($resultM->delete() && $resultSM->each->delete()) {
                $results = [
                    'status'  => true,
                    'type'    => 'success',
                    'message' => 'El módulo y sus submódulos fueron eliminados',
                ];
            }
        } else {
            if ($resultM->delete()) {
                $results = [
                    'status'  => true,
                    'type'    => 'success',
                    'message' => 'El módulo fue eliminado',
                ];
            }
        }

        return response()->json($results ?? ['status' => false, 'type' => 'error', 'message' => 'No se pudo eliminar'], 200);
    }

    public function destroySubmodule(int $id): JsonResponse {
        $result = Submodule::findOrFail($id);
        $result->delete();
        return response()->json([
            'status'  => (bool) $result,
            'type'    => $result ? 'success' : 'error',
            'message' => $result ? 'El submódulo fue eliminado' : 'No se pudo eliminar el submódulo',
        ], 200);
    }

    // ─── Permission management ─────────────────────────────────────────────────

    /**
     * Returns the assigned and available permissions for a given module.
     */
    public function getModulePermissions(int $id): JsonResponse {
        $module   = Module::with('permissions')->findOrFail($id);
        $assigned = $module->permissions->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'description' => $p->description]);
        $assignedIds = $module->permissions->pluck('id');
        $available = Permission::whereNotIn('id', $assignedIds)
            ->get()
            ->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'description' => $p->description]);

        return response()->json([
            'status'    => true,
            'module'    => ['id' => $module->id, 'descripcion' => $module->descripcion],
            'assigned'  => $assigned,
            'available' => $available,
        ]);
    }

    /**
     * Syncs the permissions for a given module (replaces all assigned permissions).
     */
    public function syncModulePermissions(Request $request, int $id): JsonResponse {
        $module = Module::findOrFail($id);
        $ids    = $request->input('permissions', []);

        // Validate all IDs exist
        $validIds = Permission::whereIn('id', $ids)->pluck('id')->toArray();
        $module->permissions()->sync($validIds);

        return response()->json([
            'status'   => true,
            'type'     => 'success',
            'messages' => 'Permisos del módulo actualizados correctamente.',
        ]);
    }

    /**
     * Returns the assigned and available permissions for a given sub-module,
     * scoped to permissions matching the submodule's `nombre` prefix pattern.
     * e.g. nombre="historias" → matches "historias", "historia_*"
     */
    public function getSubmodulePermissions(int $id): JsonResponse {
        $submodule = Submodule::with('modulo')->findOrFail($id);
        $nombre    = $submodule->nombre; // e.g. "historias"

        // Build singular prefix: strip trailing 's' for CRUD permissions (historia_*)
        $singular  = rtrim($nombre, 's'); // "historia"

        // Get all permissions relevant to this submodule
        $scopedPerms = Permission::where('name', $nombre)
            ->orWhere('name', 'like', $singular . '_%')
            ->get();

        // Get already-assigned permissions for this submodule's parent module
        $module      = $submodule->modulo;
        $moduleAssignedIds = $module ? $module->permissions()->pluck('permissions.id')->toArray() : [];

        $assigned  = $scopedPerms->filter(fn($p) => in_array($p->id, $moduleAssignedIds))
            ->values()
            ->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'description' => $p->description]);

        $available = $scopedPerms->filter(fn($p) => !in_array($p->id, $moduleAssignedIds))
            ->values()
            ->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'description' => $p->description]);

        return response()->json([
            'status'     => true,
            'submodule'  => ['id' => $submodule->id, 'descripcion' => $submodule->descripcion, 'nombre' => $nombre],
            'module'     => $module ? ['id' => $module->id, 'descripcion' => $module->descripcion] : null,
            'assigned'   => $assigned,
            'available'  => $available,
        ]);
    }

    /**
     * Syncs the scoped permissions for a submodule onto its parent module's pivot,
     * leaving other (out-of-scope) permissions on the module untouched.
     */
    public function syncSubmodulePermissions(Request $request, int $id): JsonResponse {
        $submodule = Submodule::with('modulo')->findOrFail($id);
        $nombre    = $submodule->nombre;
        $singular  = rtrim($nombre, 's');
        $module    = $submodule->modulo;

        if (!$module) {
            return response()->json(['status' => false, 'type' => 'error', 'messages' => 'Este submódulo no tiene un módulo padre asignado.'], 422);
        }

        // IDs to assign for this submodule scope
        $newScopeIds = array_map('intval', $request->input('permissions', []));

        // All permission IDs in this scope
        $scopeIds = Permission::where('name', $nombre)
            ->orWhere('name', 'like', $singular . '_%')
            ->pluck('id')
            ->toArray();

        // Current module pivot IDs outside this scope (preserve them)
        $currentIds     = $module->permissions()->pluck('permissions.id')->toArray();
        $outOfScopeIds  = array_values(array_diff($currentIds, $scopeIds));

        // Merge: out-of-scope (preserved) + new scoped selection
        $finalIds = array_unique(array_merge($outOfScopeIds, $newScopeIds));
        $module->permissions()->sync($finalIds);

        return response()->json([
            'status'   => true,
            'type'     => 'success',
            'messages' => 'Permisos del submódulo actualizados correctamente.',
        ]);
    }
}
