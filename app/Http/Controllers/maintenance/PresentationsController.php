<?php

namespace App\Http\Controllers\maintenance;

use App\Http\Controllers\Controller;
use App\Http\Requests\PresentationValidate;
use App\Http\Resources\PresentationResource;
use App\Models\Presentation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class PresentationsController extends Controller {
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:presentacion_acceder')->only('index');
        $this->middleware('permission:presentacion_ver')->only('list', 'show');
        $this->middleware('permission:presentacion_crear')->only('store');
        $this->middleware('permission:presentacion_borrar')->only('destroy');
    }

    public function index(): View {
        return view('maintenance.presentations.index');
    }

    public function list(): JsonResponse {
        $results    = Presentation::all();
        $data       = $results->map(function ($item, $index) {
            $buttons = '';
            $user = auth()->user();
            if ($user->can('presentacion_actualizar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-warning update-row btn-md" value="%s" title="Editar">
                        <i class="bi bi-pencil-square"></i>
                    </button>&nbsp;',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            if ($user->can('presentacion_borrar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-presentation btn-md" value="%s" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            return [
                $index + 1,
                $item->descripcion,
                $item->aka,
                $item->created_at->format('Y-m-d H:i:s'),
                $buttons ?: 'No hay acciones disponibles',
            ];
        });

      	return response()->json([
 			"sEcho"					    => 1,
 			"iTotalRecords"			    => $data->count(),
 			"iTotalDisplayRecords"	    => $data->count(),
 			"aaData"				    => $data,
 		]);
    }

    public function store(PresentationValidate $request): JsonResponse {
        $validated  = $request->validated();
        $result     = Presentation::updateOrCreate(['id' => $request->id], $validated);
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? ($result->wasChanged() ? 'La presentación ha sido actualizada' : 'Se ha añadido una nueva presentación') : 'Recargue la página, algo salió mal',
        ], 200);
    }

    public function show(Presentation $pre): JsonResponse {
        return response()->json(PresentationResource::make($pre), 200);
    }

    public function destroy(Presentation $pre): JsonResponse {
        $pre->delete();
        return response()->json([
            'status'    => (bool) $pre,
            'type'      => $pre ? 'success' : 'error',
            'messages'  => $pre ? 'Eliminado exitosamente' : 'Recargue la página, algo salió mal',
        ], 200);
    }
}
