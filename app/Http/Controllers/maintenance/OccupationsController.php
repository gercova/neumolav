<?php

namespace App\Http\Controllers\maintenance;

use App\Http\Controllers\Controller;
use App\Http\Requests\OccupationValidate;
use App\Http\Resources\OccupationResource;
use App\Models\Occupation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OccupationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ocupacion_acceder')->only('index');
        $this->middleware('permission:ocupacion_ver')->only('list', 'show');
        $this->middleware('permission:ocupacion_crear|ocupacion_actualizar|historia_crear|historia_actualizar')->only('store');
        $this->middleware('permission:ocupacion_borrar')->only('destroy');
    }

    public function index(): View
    {
        return view('maintenance.occupations.index');
    }

    public function list(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $query = Occupation::query()->orderBy('id', 'desc');

        if (! empty($search)) {
            $query->where('descripcion', 'LIKE', "%{$search}%");
        }

        $user = auth()->user();
        $permissions = [
            'create' => $user ? $user->can('ocupacion_crear') : false,
            'update' => $user ? $user->can('ocupacion_actualizar') : false,
            'delete' => $user ? $user->can('ocupacion_borrar') : false,
        ];

        $pageSize = (int) $request->input('pageSize', $request->input('jtPageSize', 0));
        if ($pageSize > 0) {
            $page = (int) $request->input('page', 1);
            if ($request->has('jtStartIndex') && ! $request->has('page')) {
                $page = (int) floor($request->input('jtStartIndex') / $pageSize) + 1;
            }

            $paginator = $query->paginate($pageSize, ['*'], 'page', $page);
            $formattedItems = collect($paginator->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'descripcion' => $item->descripcion,
                    'estado' => (bool) $item->estado,
                    'created_at' => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : null,
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $formattedItems,
                'total' => $paginator->total(),
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'pageSize' => $paginator->perPage(),
                'permissions' => $permissions,
                'Result' => 'OK',
                'TotalRecordCount' => $paginator->total(),
                'Records' => $formattedItems,
            ]);
        }

        $results = $query->get();
        $formattedItems = $results->map(function ($item) {
            return [
                'id' => $item->id,
                'descripcion' => $item->descripcion,
                'estado' => (bool) $item->estado,
                'created_at' => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : null,
            ];
        });

        $aaData = $results->map(function ($item, $index) use ($user) {
            $buttons = '';
            if ($user && $user->can('ocupacion_actualizar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-warning update-row btn-md" value="%s" title="Editar"><i class="bi bi-pencil-square"></i></button>&nbsp;',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            if ($user && $user->can('ocupacion_borrar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-occupation btn-md" value="%s" title="Eliminar"><i class="bi bi-trash"></i></button>',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }

            return [
                $index + 1,
                $item->descripcion,
                $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '',
                $buttons ?: 'No hay acciones disponibles',
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $formattedItems,
            'total' => $results->count(),
            'permissions' => $permissions,
            'sEcho' => 1,
            'iTotalRecords' => $results->count(),
            'iTotalDisplayRecords' => $results->count(),
            'aaData' => $aaData,
            'Result' => 'OK',
            'TotalRecordCount' => $results->count(),
            'Records' => $formattedItems,
        ]);
    }

    public function store(OccupationValidate $request): JsonResponse
    {
        $validated = $request->validated();
        if ($request->filled('id')) {
            $occupation = Occupation::findOrFail($request->input('id'));
            $occupation->update($validated);

            return response()->json([
                'status' => true,
                'type' => 'success',
                'messages' => 'La ocupación ha sido actualizada',
                'item' => [
                    'id' => $occupation->id,
                    'descripcion' => $occupation->descripcion,
                    'ocupacion' => $occupation->id.' | '.$occupation->descripcion,
                ],
            ], 200);
        }

        $result = Occupation::create($validated);

        return response()->json([
            'status' => (bool) $result,
            'type' => $result ? 'success' : 'error',
            'messages' => $result ? 'Se ha añadido una nueva ocupación' : 'Recargue la página he intente de nuevo',
            'item' => $result ? [
                'id' => $result->id,
                'descripcion' => $result->descripcion,
                'ocupacion' => $result->id.' | '.$result->descripcion,
            ] : null,
        ], 200);
    }

    public function show(Occupation $oc): JsonResponse
    {
        return response()->json(OccupationResource::make($oc), 200);
    }

    public function search(Request $request): JsonResponse
    {
        $term = $request->input('term', $request->input('q'));
        $occupations = Occupation::where('descripcion', 'like', "%{$term}%")->get();

        return response()->json($occupations, 200);
    }

    public function destroy(Occupation $oc): JsonResponse
    {
        $oc->delete();

        return response()->json([
            'status' => true,
            'type' => 'success',
            'messages' => 'La ocupación ha sido eliminada',
        ], 200);
    }
}
