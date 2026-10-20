<?php

namespace App\Http\Controllers\security;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecialtyValidate;
use App\Models\Occupation;
use App\Models\Specialty;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpecialtiesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:especialidad_acceder')->only('index');
        $this->middleware('permission:especialidad_ver')->only('list', 'show', 'options', 'search');
        $this->middleware('permission:especialidad_crear|especialidad_actualizar')->only('store');
        $this->middleware('permission:especialidad_borrar')->only('destroy');
    }

    public function index(): View
    {
        $occupations = Occupation::select('id', 'descripcion')->orderBy('descripcion')->get();
        $oc = $occupations;

        return view('security.specialties.index', compact('occupations', 'oc'));
    }

    public function options(): JsonResponse
    {
        $occupations = Occupation::select('id', 'descripcion')->orderBy('descripcion')->get();

        return response()->json([
            'status' => true,
            'occupations' => $occupations,
        ], 200);
    }

    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', $request->input('search', '')));
        $query = Specialty::query()->select('id', 'descripcion', 'id_ocupacion')
            ->with('occupation:id,descripcion')
            ->orderBy('descripcion');

        if ($q !== '') {
            $query->where('descripcion', 'LIKE', "%{$q}%");
        }

        $specialties = $query->limit(5)->get()->map(function ($item) {
            $occ = $item->occupation ? " ({$item->occupation->descripcion})" : '';

            return [
                'id' => $item->id,
                'value' => $item->id,
                'label' => $item->descripcion.$occ,
                'descripcion' => $item->descripcion,
            ];
        });

        return response()->json($specialties, 200);
    }

    public function list(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $occupationFilter = $request->input('occupation', $request->input('id_ocupacion'));
        $specialtyFilter = $request->input('specialty_id', $request->input('specialty'));
        $query = Specialty::with('occupation')->orderBy('id', 'desc');

        if (! empty($specialtyFilter)) {
            $query->where('id', $specialtyFilter);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'LIKE', "%{$search}%")
                    ->orWhere('detalle', 'LIKE', "%{$search}%")
                    ->orWhereHas('occupation', function ($oq) use ($search) {
                        $oq->where('descripcion', 'LIKE', "%{$search}%");
                    });
            });
        }

        if (! empty($occupationFilter)) {
            $query->where('id_ocupacion', $occupationFilter);
        }

        $user = auth()->user();
        $permissions = [
            'create' => $user ? $user->can('especialidad_crear') : false,
            'update' => $user ? $user->can('especialidad_actualizar') : false,
            'delete' => $user ? $user->can('especialidad_borrar') : false,
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
                    'detalle' => $item->detalle,
                    'id_ocupacion' => $item->id_ocupacion,
                    'occupation_name' => $item->occupation?->descripcion ?? '—',
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
                'detalle' => $item->detalle,
                'id_ocupacion' => $item->id_ocupacion,
                'occupation_name' => $item->occupation?->descripcion ?? '—',
                'estado' => (bool) $item->estado,
                'created_at' => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : null,
            ];
        });

        $aaData = $results->map(function ($item, $index) use ($user) {
            $buttons = '';
            if ($user && $user->can('especialidad_actualizar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-warning update-row btn-md" value="%s" title="Editar"><i class="bi bi-pencil-square"></i></button>&nbsp;',
                    htmlspecialchars((string) $item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            if ($user && $user->can('especialidad_borrar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-specialty btn-md" value="%s" title="Eliminar"><i class="bi bi-trash"></i></button>',
                    htmlspecialchars((string) $item->id, ENT_QUOTES, 'UTF-8')
                );
            }

            return [
                $index + 1,
                htmlspecialchars($item->descripcion, ENT_QUOTES, 'UTF-8'),
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

    public function store(SpecialtyValidate $request): JsonResponse
    {
        $validated = $request->validated();
        $id = $request->input('id', $request->input('specialtyId'));

        $specialty = Specialty::updateOrCreate(['id' => $id], $validated);

        return response()->json([
            'status' => (bool) $specialty,
            'type' => $specialty ? 'success' : 'error',
            'messages' => $specialty
                ? ($specialty->wasRecentlyCreated ? 'Se ha añadido una nueva especialidad' : 'La especialidad ha sido actualizada')
                : 'Recargue la página, algo salió mal',
            'item' => [
                'id' => $specialty->id,
                'descripcion' => $specialty->descripcion,
                'detalle' => $specialty->detalle,
                'id_ocupacion' => $specialty->id_ocupacion,
                'occupation_name' => $specialty->occupation?->descripcion ?? '—',
            ],
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $specialty = Specialty::with('occupation')->findOrFail($id);

        return response()->json([
            'id' => $specialty->id,
            'descripcion' => $specialty->descripcion,
            'detalle' => $specialty->detalle,
            'id_ocupacion' => $specialty->id_ocupacion,
            'occupation_name' => $specialty->occupation?->descripcion ?? '—',
            'estado' => (bool) $specialty->estado,
            'created_at' => $specialty->created_at ? $specialty->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $specialty->updated_at ? $specialty->updated_at->format('Y-m-d H:i:s') : null,
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = Specialty::findOrFail($id);
        $result->delete();

        return response()->json([
            'status' => (bool) $result,
            'type' => $result ? 'success' : 'error',
            'messages' => $result ? 'La especialidad fue eliminada' : 'Recargue la página, algo salió mal',
        ], 200);
    }
}
