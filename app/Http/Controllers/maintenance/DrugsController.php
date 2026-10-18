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

class DrugsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:farmaco_acceder')->only('index');
        $this->middleware('permission:farmaco_ver')->only('list', 'show', 'options');
        $this->middleware('permission:farmaco_crear|farmaco_actualizar')->only('store');
        $this->middleware('permission:farmaco_borrar')->only('destroy');
    }

    public function index(): View {
        $categories = Category::select('id', 'descripcion', 'detalle')->orderBy('descripcion')->get();
        $presentations = Presentation::select('id', 'descripcion', 'aka')->orderBy('descripcion')->get();

        return view('maintenance.drugs.index', compact('categories', 'presentations'));
    }

    public function options(): JsonResponse {
        return response()->json([
            'categories' => Category::select('id', 'descripcion', 'detalle')->orderBy('descripcion')->get(),
            'presentations' => Presentation::select('id', 'descripcion', 'aka')->orderBy('descripcion')->get(),
        ]);
    }

    public function list(Request $request): JsonResponse {
        $search = $request->input('search');
        $query = Drug::with(['category', 'presentation'])->orderBy('id', 'desc');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'LIKE', "%{$search}%")
                    ->orWhere('detalle', 'LIKE', "%{$search}%")
                    ->orWhereHas('category', function ($cq) use ($search) {
                        $cq->where('descripcion', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('presentation', function ($pq) use ($search) {
                        $pq->where('descripcion', 'LIKE', "%{$search}%")
                            ->orWhere('aka', 'LIKE', "%{$search}%");
                    });
            });
        }

        $user = auth()->user();
        $permissions = [
            'create' => $user ? $user->can('farmaco_crear') : false,
            'update' => $user ? $user->can('farmaco_actualizar') : false,
            'delete' => $user ? $user->can('farmaco_borrar') : false,
        ];

        $pageSize = (int) $request->input('pageSize', $request->input('jtPageSize', 0));
        if ($pageSize > 0) {
            $page = (int) $request->input('page', 1);
            if ($request->has('jtStartIndex') && ! $request->has('page')) {
                $page = (int) floor($request->input('jtStartIndex') / $pageSize) + 1;
            }

            $paginator      = $query->paginate($pageSize, ['*'], 'page', $page);
            $formattedItems = collect($paginator->items())->map(function ($item) {
                return [
                    'id'                => $item->id,
                    'descripcion'       => $item->descripcion,
                    'detalle'           => $item->detalle,
                    'id_categoria'      => $item->id_categoria,
                    'id_presentacion'   => $item->id_presentacion,
                    'category'          => $item->category?->descripcion ?? '—',
                    'presentation'      => $item->presentation?->descripcion ?? '—',
                    'presentation_aka'  => $item->presentation?->aka ?? '',
                    'estado'            => $item->estado,
                    'created_at'        => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at'        => $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : null,
                ];
            });

            return response()->json([
                'status'            => true,
                'data'              => $formattedItems,
                'total'             => $paginator->total(),
                'currentPage'       => $paginator->currentPage(),
                'lastPage'          => $paginator->lastPage(),
                'pageSize'          => $paginator->perPage(),
                'permissions'       => $permissions,
                'Result'            => 'OK',
                'TotalRecordCount'  => $paginator->total(),
                'Records'           => $formattedItems,
            ]);
        }

        $results        = $query->get();
        $formattedItems = $results->map(function ($item) {
            return [
                'id'                => $item->id,
                'descripcion'       => $item->descripcion,
                'detalle'           => $item->detalle,
                'id_categoria'      => $item->id_categoria,
                'id_presentacion'   => $item->id_presentacion,
                'category'          => $item->category?->descripcion ?? '—',
                'presentation'      => $item->presentation?->descripcion ?? '—',
                'presentation_aka'  => $item->presentation?->aka ?? '',
                'estado'            => $item->estado,
                'created_at'        => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                'updated_at'        => $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : null,
            ];
        });

        $aaData = $results->map(function ($item, $index) use ($user) {
            $buttons = '';
            if ($user && $user->can('farmaco_actualizar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-warning update-row btn-md" value="%s"><i class="bi bi-pencil-square"></i></button>&nbsp;',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            if ($user && $user->can('farmaco_borrar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-drug btn-md" value="%s"><i class="bi bi-trash"></i></button>',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }

            return [
                $index + 1,
                $item->category?->descripcion ?? '',
                $item->descripcion,
                $item->presentation?->descripcion ?? '',
                $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '',
                $buttons ?: 'No hay acciones disponibles',
            ];
        });

        return response()->json([
            'status'                => true,
            'data'                  => $formattedItems,
            'total'                 => $results->count(),
            'permissions'           => $permissions,
            'sEcho'                 => 1,
            'iTotalRecords'         => $results->count(),
            'iTotalDisplayRecords'  => $results->count(),
            'aaData'                => $aaData,
            'Result'                => 'OK',
            'TotalRecordCount'      => $results->count(),
            'Records'               => $formattedItems,
        ]);
    }

    public function store(DrugValidate $request): JsonResponse {
        $validated  = $request->validated();
        if ($request->filled('id')) {
            $drug   = Drug::findOrFail($request->input('id'));
            $drug->update($validated);

            return response()->json([
                'status'    => true,
                'type'      => 'success',
                'messages'  => 'El fármaco ha sido actualizado',
            ], 200);
        }

        $result = Drug::create($validated);

        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? 'Se ha añadido un nuevo fármaco' : 'Recargue la página, algo salió mal',
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
            'status'    => true,
            'type'      => 'success',
            'messages'  => 'El fármaco fue eliminado',
        ], 200);
    }
}
