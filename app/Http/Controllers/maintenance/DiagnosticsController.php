<?php

namespace App\Http\Controllers\maintenance;

use App\Http\Controllers\Controller;
use App\Http\Requests\DiagnosticValidate;
use App\Models\Diagnostic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiagnosticsController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:diagnostico_acceder')->only('index');
        $this->middleware('permission:diagnostico_crear')->only('store');
        $this->middleware('permission:diagnostico_ver')->only('list', 'show');
        $this->middleware('permission:diagnostico_borrar')->only('destroy');
    }

    public function index(): View {
        return view('maintenance.diagnostics.index');
    }

    public function list(): JsonResponse {
        $results 	= Diagnostic::all();
        $data       = $results->map(function ($item, $index) {
            $user   = auth()->user();
            $buttons = '';
            if($user->can('diagnostico_actualizar')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-warning update-row btn-md" value="%s">
                        <i class="bi bi-pencil-square"></i>
                    </button>&nbsp;',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            if($user->can('diagnostico_borrar')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-diagnostic btn-md" value="%s">
                        <i class="bi bi-trash"></i>
                    </button>',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }

            return [
                $index + 1,
                $item->descripcion,
                $item->created_at->format('Y-m-d H:i:s'),
                $buttons ?: 'No hay acciones disponibles'
            ];
        });

        return response()->json([
            "sEcho"					=> 1,
            "iTotalRecords"			=> $data->count(),
            "iTotalDisplayRecords"	=> $data->count(),
            "aaData"				=> $data,
       ]);
    }

    public function store(DiagnosticValidate $request): JsonResponse {
        $validated  = $request->validated();
        $result     = Diagnostic::updateOrCreate(['id' => $request->id], $validated);
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? ($result->wasChanged() ? 'El Diagnóstico ha sido actualizado' : 'Se ha añadido un nuevo diagnóstico') : 'Recargue la página, algo salió mal',
        ], 200);
    }

    public function show($id): JsonResponse {
        return response()->json(Diagnostic::findOrFail($id), 200);
    }

    public function search(Request $request): JsonResponse {
        $results = Diagnostic::selectRaw('id, UPPER(descripcion) label')->where('descripcion', 'LIKE', '%'.$request->input('q').'%')->get()->toArray();
        return response()->json($results, 200);
    }

    public function advancedSearch(Request $request){
        $query = $request->query('q');
        $keywords = Diagnostic::where('descripcion', 'LIKE', "%{$query}%")
            ->pluck('descripcion') // Solo obtener el nombre
            ->toArray();
        return response()->json($keywords);
    }

    public function destroy($id): JsonResponse {
        $result = Diagnostic::findOrFail($id);
        $result->delete();
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? 'El diagnóstico fue eliminado' : 'Recargue la página, algo salió mal',
        ], 200);
    }
}