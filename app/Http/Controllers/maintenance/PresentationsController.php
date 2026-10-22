<?php

namespace App\Http\Controllers\maintenance;

use App\Http\Controllers\Controller;
use App\Http\Requests\PresentationValidate;
use App\Http\Resources\PresentationResource;
use App\Models\Presentation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresentationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:presentacion_acceder')->only('index');
        $this->middleware('permission:presentacion_ver')->only('list', 'show');
        $this->middleware('permission:presentacion_crear|presentacion_actualizar')->only('store');
        $this->middleware('permission:presentacion_borrar')->only('destroy');
    }

    public function index(): View
    {
        return view('maintenance.presentations.index');
    }

    public function list(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $query = Presentation::query();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'LIKE', "%{$search}%")
                    ->orWhere('aka', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('id', 'desc');

        $user = auth()->user();
        $permissions = [
            'create' => $user ? $user->can('presentacion_crear') : false,
            'update' => $user ? $user->can('presentacion_actualizar') : false,
            'delete' => $user ? $user->can('presentacion_borrar') : false,
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
                    'aka' => $item->aka,
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
                'aka' => $item->aka,
                'created_at' => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : null,
            ];
        });

        $aaData = $results->map(function ($item, $index) use ($user) {
            $buttons = '';
            if ($user && $user->can('presentacion_actualizar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-warning update-row btn-md" value="%s" title="Editar"><i class="bi bi-pencil-square"></i></button>&nbsp;',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            if ($user && $user->can('presentacion_borrar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-presentation btn-md" value="%s" title="Eliminar"><i class="bi bi-trash"></i></button>',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }

            return [
                $index + 1,
                $item->descripcion,
                $item->aka,
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

    public function store(PresentationValidate $request): JsonResponse
    {
        $validated = $request->validated();
        if ($request->filled('id')) {
            $presentation = Presentation::findOrFail($request->input('id'));
            $presentation->update($validated);

            return response()->json([
                'status' => true,
                'type' => 'success',
                'messages' => 'La presentación ha sido actualizada',
            ], 200);
        }

        $result = Presentation::create($validated);

        return response()->json([
            'status' => (bool) $result,
            'type' => $result ? 'success' : 'error',
            'messages' => $result ? 'Se ha añadido una nueva presentación' : 'Recargue la página, algo salió mal',
        ], 200);
    }

    public function show(Presentation $pre): JsonResponse
    {
        return response()->json(PresentationResource::make($pre), 200);
    }

    public function destroy(Presentation $pre): JsonResponse
    {
        $pre->delete();

        return response()->json([
            'status' => true,
            'type' => 'success',
            'messages' => 'La presentación fue eliminada',
        ], 200);
    }
}
