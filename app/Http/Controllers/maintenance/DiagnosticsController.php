<?php

namespace App\Http\Controllers\maintenance;

use App\Http\Controllers\Controller;
use App\Http\Requests\DiagnosticValidate;
use App\Http\Resources\DiagnosticResource;
use App\Models\Diagnostic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiagnosticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:diagnostico_acceder')->only('index');
        $this->middleware('permission:diagnostico_ver')->only('list', 'show');
        $this->middleware('permission:diagnostico_crear|diagnostico_actualizar')->only('store');
        $this->middleware('permission:diagnostico_borrar')->only('destroy');
    }

    public function index(): View
    {
        return view('maintenance.diagnostics.index');
    }

    public function list(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $query = Diagnostic::query()->orderBy('id', 'desc');

        if (! empty($search)) {
            $query->where('descripcion', 'LIKE', "%{$search}%");
        }

        $user = auth()->user();
        $permissions = [
            'create' => $user ? $user->can('diagnostico_crear') : false,
            'update' => $user ? $user->can('diagnostico_actualizar') : false,
            'delete' => $user ? $user->can('diagnostico_borrar') : false,
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
                'created_at' => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : null,
            ];
        });

        $aaData = $results->map(function ($item, $index) use ($user) {
            $buttons = '';
            if ($user && $user->can('diagnostico_actualizar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-warning update-row btn-md" value="%s"><i class="bi bi-pencil-square"></i></button>&nbsp;',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            if ($user && $user->can('diagnostico_borrar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-diagnostic btn-md" value="%s"><i class="bi bi-trash"></i></button>',
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

    public function store(DiagnosticValidate $request): JsonResponse
    {
        $validated = $request->validated();
        if ($request->filled('id')) {
            $diagnostic = Diagnostic::findOrFail($request->input('id'));
            $diagnostic->update($validated);

            return response()->json([
                'status' => true,
                'type' => 'success',
                'messages' => 'El diagnóstico ha sido actualizado',
            ], 200);
        }

        $result = Diagnostic::create($validated);

        return response()->json([
            'status' => (bool) $result,
            'type' => $result ? 'success' : 'error',
            'messages' => $result ? 'Se ha añadido un nuevo diagnóstico' : 'Recargue la página, algo salió mal',
        ], 200);
    }

    public function show(Diagnostic $diagnostic): JsonResponse
    {
        return response()->json(DiagnosticResource::make($diagnostic), 200);
    }

    public function search(Request $request): JsonResponse
    {
        $term = $request->input('q');
        $results = Diagnostic::selectRaw('id, descripcion, UPPER(descripcion) text')
            ->where('descripcion', 'like', "%{$term}%")
            ->limit(5)
            ->get()
            ->toArray();

        return response()->json($results, 200);
    }

    public function advancedSearch(Request $request): JsonResponse
    {
        $query = $request->query('q');
        $keywords = Diagnostic::where('descripcion', 'LIKE', "%{$query}%")
            ->pluck('descripcion')
            ->toArray();

        return response()->json($keywords);
    }

    public function destroy(Diagnostic $diagnostic): JsonResponse
    {
        $diagnostic->delete();

        return response()->json([
            'status' => true,
            'type' => 'success',
            'messages' => 'El diagnóstico fue eliminado',
        ], 200);
    }
}
