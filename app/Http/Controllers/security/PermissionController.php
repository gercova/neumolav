<?php

namespace App\Http\Controllers\security;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionValidate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller {

    public function __construct() {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:permiso_acceder')->only('index');
        $this->middleware('permission:permiso_crear')->only('store');
        $this->middleware('permission:permiso_ver')->only('list', 'show');
        $this->middleware('permission:permiso_borrar')->only('destroy');
        $this->middleware('permission:permiso_actualizar')->only('edit', 'store');
    }
    
    public function index(): View {
        $data['rl'] = Role::all();
        return view('security.permissions.index', $data);
    }

    public function list() {
        $results    = Permission::all();
        $data       = $results->map(function($item, $index){
            return [
                $index + 1,
                $item->name,
                $item->guard_name,
                $item->created_at->format('Y-m-d H:i:s'),
                sprintf(
                    '<button type="button" class="btn btn-sm btn-warning update-row btn-md" value="%s">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-permission btn-md" value="%s">
                        <i class="bi bi-trash"></i>
                    </button>',
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

    public function store(PermissionValidate $request) {
        $validated  = $request->validated();
        $result     = Permission::updateOrCreate(['id' => $request->id], $validated);
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? ($result->wasChanged() ? 'El permiso ha sido actualizado' : 'Se ha añadido un nuevo permiso') : 'Recargue la página, algo salió mal',
        ], 200);
    }

    public function show(int $id) {
        return response()->json(Permission::findOrFail($id), 200);
    }

    public function search(Request $request) {
        $results = Permission::selectRaw('id, UPPER(name) label')
            ->where('name', 'LIKE', '%'.$request->input('q').'%')
            ->whereNotIn('id', [14, 15, 16, 17])
            ->where('name', 'not like', 'modulo_%')
            ->where('name', 'not like', 'especialidad_%')
            ->where('name', 'not like', 'usuario_%')
            ->where('name', 'not like', 'rol_%')
            ->where('name', 'not like', 'permiso_%')
            ->get()
            ->toArray();
        return response()->json($results, 200);
    }

    public function destroy(int $id) {
        $result = Permission::findOrFail($id);
        $result->delete();
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result? 'success' : 'error',
            'message'   => $result? 'El permiso fue eliminado' : 'No se pudo eliminar el permiso'
        ], 200);
    }
}
