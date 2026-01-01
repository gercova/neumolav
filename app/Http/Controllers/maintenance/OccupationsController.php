<?php

namespace App\Http\Controllers\maintenance;

use App\Http\Controllers\Controller;
use App\Http\Requests\OccupationValidate;
use App\Http\Resources\OccupationResource;
use App\Models\Occupation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OccupationsController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:ocupacion_acceder')->only('index');
        $this->middleware('permission:ocupacion_ver')->only('list', 'show');
        $this->middleware('permission:ocupacion_crear')->only('store');
        $this->middleware('permission:ocupacion_borrar')->only('destroy');
    }

    public function index(): View {
        return view('maintenance.occupations.index');
    }

    public function list(): JsonResponse {
        $results 	= Occupation::all();
        $data       = $results->map(function ($item, $index) {
            $buttons = '';
            $user = auth()->user();
            if ($user->can('ocupacion_actualizar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-warning update-row btn-md" value="%s" title="Editar">
                        <i class="bi bi-pencil-square"></i>
                    </button> ',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            if ($user->can('ocupacion_borrar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-occupation btn-md" value="%s" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }

            return [
                $index + 1,
                $item->descripcion,
                $item->created_at->format('Y-m-d H:i:s'),
                $buttons ?: 'No hay acciones disponibles',
            ];
        });

        return response()->json([
            "sEcho"					=> 1,
            "iTotalRecords"			=> $data->count(),
            "iTotalDisplayRecords"	=> $data->count(),
            "aaData"				=> $data,
       ]);
    }

    public function store(OccupationValidate $request): JsonResponse {
        $validated  = $request->validated();
        $result     = Occupation::updateOrCreate(['id' => $request->id], $validated);
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? ($result->wasChanged() ? 'La ocupación ha sido actualizada' : 'Se ha añadido una nueva ocupación') : 'Recargue la página he intente de nuevo',
        ], 200);
    }

    public function search(Request $request): JsonResponse {
        $term           = $request->input('term');
        $occupations    = Occupation::where('descripcion', 'like', "%$term%")->get();
        return response()->json($occupations);
    }

    public function show(Occupation $oc): JsonResponse {
        return response()->json(OccupationResource::make($oc), 200);
    }

    public function destroy(Occupation $oc): JsonResponse {
        $oc->delete();
        return response()->json([
            'status'    => (bool) $oc,
            'type'      => $oc ? 'success' : 'error',
            'messages'  => $oc ? 'La ocupación ha sido eliminada' : 'Recargue la página, algo salió mal',
        ], 200);
    }
}
