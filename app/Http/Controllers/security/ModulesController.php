<?php

namespace App\Http\Controllers\security;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleValidate;
use App\Http\Requests\SubmoduleValidate;
use App\Models\Module;
use App\Models\Submodule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class ModulesController extends Controller {
    
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:modulo_acceder')->only('index');
		$this->middleware('permission:modulo_ver')->only('list', 'listReports', 'listOfDiagnosticsByReportId', 'showModule', 'showSubmodule');
		$this->middleware('permission:modulo_crear')->only('storeModule', 'storeSubmodule');
		$this->middleware('permission:modulo_borrar')->only('destroyModule', 'destroySubmodule');
    }

    public function index(): View {
        $md = Module::where('deleted_at', NULL)->get();
        return view('security.modules.index', compact('md'));
    }

    public function list(): JsonResponse {
        $resultsM   = Module::with('submodules')->get();
        $data       = $resultsM->map(function($item, $key) {
            $submodulesList = '<table class="table table-sm">';
            foreach ($item->submodules as $sm) {
                $submodulesList .= sprintf(
                    '<tr>
                        <td><span class="badge badge-success">%s</span>&nbsp;%s</td>
                        <td>
                            <button class="btn btn-sm btn-warning update-row-submodule btn-sm" value="%s"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn btn-sm btn-danger delete-submodule btn-sm" value="%s"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>',
                    $sm->icono,
                    $sm->descripcion,
                    $sm->id,
                    $sm->id,
                );
            }
            $submodulesList .= '</table>';
            return [
                $key + 1,
                $item->descripcion,
                sprintf('<button type="button" class="btn btn-sm btn-info btn-md">%s</button>', $item->icono),
                $submodulesList,
                $item->created_at->format('Y-m-d H:i:s'),
                sprintf(
                    '<button type="button" class="btn btn-sm btn-warning update-row-module btn-md" value="%s">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-module btn-md" value="%s">
                        <i class="bi bi-trash"></i>
                    </button>',
                    $item->id,
                    $item->id,
                    $item->id
                )
            ];
        });
        return response()->json([
            "sEcho"					    => 1,
            "iTotalRecords"			    => $data->count(),
            "iTotalDisplayRecords"	    => $data->count(),
            "aaData"				    => $data,
        ]);
    }
    
    public function storeModule(ModuleValidate $request): JsonResponse {
        $validated  = $request->validated();
        $result     = Module::updateOrCreate(['id' => $request->input('moduleId')], $validated);
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? ($result->wasChanged() ? 'El módulo ha sido actualizado' : 'Se ha añadido un nuevo módulo') : 'Recargue la página he intente de nuevo',
        ], 200);
    }

    public function storeSubmodule(SubmoduleValidate $request): JsonResponse {
        $validated  = $request->validated();
        $result     = Submodule::updateOrCreate(['id' => $request->input('submoduleId')], $validated);
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? ($result->wasChanged() ? 'El registro ha sido actualizado' : 'Se ha añadido un nuevo registro') : 'Recargue la página he intente de nuevo',
        ], 200);
    }

    public function showModule(int $id): JsonResponse {
        return response()->json(Module::findOrFail($id), 200);
    }

    public function showSubmodule(int $id): JsonResponse {
        return response()->json(Submodule::findOrFail($id), 200);
    }

    public function destroyModule(int $id): JsonResponse {
        $resultM    = Module::findOrFail($id);
        $resultSM   = Submodule::where('module_id', $id)->get();
        if($resultSM->count() > 0){
            if($resultM->delete() && $resultSM->delete())
                $results = [
                    'status'    => (bool) $resultM, $resultSM,
                    'type'      => $resultM && $resultSM ? 'success' : 'error',
                    'message'   => $resultM && $resultSM ? 'El módulo y sus submódulos fueron eliminados' : 'No se pudo eliminar'
                ];
        }else{
            if($resultM->delete())
                $results = [
                    'status'    => (bool) $resultM,
                    'type'      => $resultM ? 'success' : 'error',
                    'message'   => $resultM ? 'El módulo fue eliminado' : 'No se pudo eliminar el módulo'
                ];
        }

        return response()->json($results, 200);
    }

    public function destroySubmodule(int $id): JsonResponse {
        $result = Submodule::findOrFail($id);
        $result->delete();
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'message'   => $result ? 'El módulo fue eliminado' : 'No se pudo eliminar el módulo'
        ], 200);
    }
}
