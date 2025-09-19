<?php

namespace App\Http\Controllers\security;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecialtyValidate;
use App\Models\Occupation;
use App\Models\Specialty;

class SpecialtiesController extends Controller {
    public function __construct() {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:especialidad_acceder')->only('index');
        $this->middleware('permission:especialidad_crear')->only('store');
        $this->middleware('permission:especialidad_ver')->only('list', 'show');
        $this->middleware('permission:especialidad_borrar')->only('destroy');
    }
    
    public function index() {
        $oc = Occupation::all();
        return view('security.specialties.index', compact('oc'));
    }

    public function list() {
        $results    = Specialty::all();
        $data       = $results->map(function ($item, $index) {
            $user   = auth()->user();
            $buttons = '';
            if($user->can('especialidad_actualizar')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-warning update-row btn-md" value="%s">
                        <i class="bi bi-pencil-square"></i>
                    </button>&nbsp;',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            if($user->can('especialidad_borrar')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-specialty btn-md" value="%s">
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
 			"sEcho"					    => 1,
 			"iTotalRecords"			    => $data->count(),
 			"iTotalDisplayRecords"	    => $data->count(),
 			"aaData"				    => $data,
 		]);
    }

    public function store(SpecialtyValidate $request) {
        $validated  = $request->validated();
        $result     = Specialty::updateOrCreate(['id' => $request->id], $validated);
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? ($result->wasChanged() ? 'La especialidad ha sido actualizada' : 'Se ha añadido una nueva especialidad') : 'Recargue la página, algo salió mal',
        ], 200);
    }
    
    public function show(int $id) {
        return response()->json(Specialty::findOrFail($id), 200);
    }

    public function destroy(int $id) {
        $result = Specialty::findOrFail($id);
        $result->delete();
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? 'La especialidad fue eliminada' : 'Recargue la página, algo salió mal',
        ], 200);
    }
}
