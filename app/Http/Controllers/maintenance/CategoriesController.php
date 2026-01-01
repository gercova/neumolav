<?php

namespace App\Http\Controllers\maintenance;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryValidate;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class CategoriesController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:categoria_acceder')->only('index');
        $this->middleware('permission:categoria_crear')->only('store');
        $this->middleware('permission:categoria_ver')->only('list', 'show');
        $this->middleware('permission:categoria_borrar')->only('destroy');
    }

    public function index(): View {
        return view('maintenance.categories.index');
    }

    public function list(): JsonResponse {
        $results    = Category::all();
        $data       = $results->map(function ($item, $index) {
            $user   = auth()->user();
            $buttons = '';
            if($user->can('categoria_actualizar')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-warning update-row btn-md" value="%s">
                        <i class="bi bi-pencil-square"></i>
                    </button>&nbsp;',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            if($user->can('categoria_borrar')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-category btn-md" value="%s">
                        <i class="bi bi-trash"></i>
                    </button>',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            return [
                $index + 1,
                $item->descripcion,
                $item->detalle,
                $item->created_at->format('Y-m-d H:i:s'),
                $buttons ?: 'No hay acciones disponibles'
            ];
        });

      	return response()->json([
 			"sEcho"					    => 1,
 			"iTotalRecords"			    => $data->count(),
 			"iTotalDisplayRecords"	    => $data->count(),
 			"aaData"				    => $data,
 		]);
    }

    public function store(CategoryValidate $request) : JsonResponse {
        $validated  = $request->validated();
        $result     = Category::updateOrCreate(['id' => $request->id], $validated);
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? ($result->wasChanged() ? 'La categoría ha sido actualizado' : 'Se ha añadido una nueva categoría') : 'Recargue la página, algo salió mal',
        ], 200);
    }

    public function show(Category $cat){
        return response()->json(CategoryResource::make($cat), 200);
    }

    public function destroy(Category $cat) : JsonResponse {
        $cat->delete();
        return response()->json([
            'status'    => (bool) $cat,
            'type'      => $cat ? 'success' : 'error',
            'messages'  => $cat ? 'La categoría fue eliminada' : 'Recargue la página, algo salió mal',
        ], 200);
    }
}
