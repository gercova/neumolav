<?php

namespace App\Http\Controllers\maintenance;

use App\Http\Controllers\Controller;
use App\Http\Requests\DrugValidate;
use App\Http\Resources\DrugResource;
use App\Models\Category;
use App\Models\Drug;
use App\Models\Presentation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrugsController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:farmaco_acceder')->only('index');
        $this->middleware('permission:farmaco_ver')->only('list', 'show');
        $this->middleware('permission:farmaco_crear')->only('store');
        $this->middleware('permission:farmaco_borrar')->only('destroy');
    }

    public function index(): View {
        $ct = Category::get();
        $dp = Presentation::get();
        return view('maintenance.drugs.index', compact('ct', 'dp'));
    }

    public function list(): JsonResponse {
        $results    = DB::table('view_active_drugs')->get();
        $data       = $results->map(function ($item, $index) {
            $user   = auth()->user();
            $buttons = '';
            if($user->can('farmaco_actualizar')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-warning update-row btn-md" value="%s">
                        <i class="bi bi-pencil-square"></i>
                    </button>&nbsp;',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            if($user->can('farmaco_borrar')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-drug btn-md" value="%s">
                        <i class="bi bi-trash"></i>
                    </button>',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            return [
                $index + 1, // Índice
                $item->category,
                $item->descripcion,
                $item->presentation,
                $item->created_at,
                $buttons ?: 'No hay acciones disponibles'
            ];
        });

        return response()->json([
            "sEcho"					    => 1,
            "iTotalRecords"			    => $data->count(),
            "iTotalDisplayRecords"	    => $data->count(),
            "aaData"				    => $data ?? [],
        ]);
    }

    public function store(DrugValidate $request): JsonResponse {
        $validated  = $request->validated();
        $result     = Drug::updateOrCreate(['id' => $request->id], $validated);
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? ($result->wasChanged() ? 'El fármaco ha sido actualizado' : 'Se ha añadido un nuevo fármaco') : 'Recargue la página, algo salió mal',
        ], 200);
    }

    public function show(Drug $drug): JsonResponse {
        return response()->json(DrugResource::make($drug), 200);
    }

    public function search(Request $request): JsonResponse {
        $drugs = Drug::where('descripcion', 'like', '%'.$request->input('q').'%')
            ->selectRaw('id, UPPER(descripcion) text')
            ->limit(5)
            ->get()
            ->toArray();
        return response()->json($drugs);
    }

    public function destroy(Drug $drug): JsonResponse {
        $drug->delete();
        return response()->json([
            'status'    => (bool) $drug,
            'type'      => $drug ? 'success' : 'error',
            'messages'  => $drug ? 'El fármaco fue eliminado' : 'Recargue la página, algo salió mal',
        ], 200);
    }
}
