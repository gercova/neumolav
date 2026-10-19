<?php

namespace App\Http\Controllers\security;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionValidate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:permiso_acceder')->only('index');
        $this->middleware('permission:permiso_ver')->only('list', 'show');
        $this->middleware('permission:permiso_crear|permiso_actualizar')->only('store');
        $this->middleware('permission:permiso_borrar')->only('destroy');
    }

    public function index(): View
    {
        return view('security.permissions.index');
    }

    public function list(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $query = Permission::query()->orderBy('id', 'desc');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('guard_name', 'LIKE', "%{$search}%");
            });
        }

        $user = auth()->user();
        $permissions = [
            'create' => $user ? $user->can('permiso_crear') : false,
            'update' => $user ? $user->can('permiso_actualizar') : false,
            'delete' => $user ? $user->can('permiso_borrar') : false,
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
                    'name' => $item->name,
                    'description' => $item->description ?? '—',
                    'guard_name' => $item->guard_name,
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
                'name' => $item->name,
                'description' => $item->description ?? '—',
                'guard_name' => $item->guard_name,
                'created_at' => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : null,
            ];
        });

        $aaData = $results->map(function ($item, $index) use ($user) {
            $buttons = '';
            if ($user && $user->can('permiso_actualizar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-warning update-row btn-md" value="%s"><i class="bi bi-pencil-square"></i></button>&nbsp;',
                    htmlspecialchars((string) $item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            if ($user && $user->can('permiso_borrar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-permission btn-md" value="%s"><i class="bi bi-trash"></i></button>',
                    htmlspecialchars((string) $item->id, ENT_QUOTES, 'UTF-8')
                );
            }

            return [
                $index + 1,
                $item->name,
                $item->guard_name,
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

    public function store(PermissionValidate $request): JsonResponse
    {
        $validated = $request->validated();
        $id = $request->input('id');

        if (! empty($id)) {
            $permission = Permission::findOrFail($id);
            $permission->update($validated);

            return response()->json([
                'status' => true,
                'type' => 'success',
                'messages' => 'El permiso ha sido actualizado',
                'message' => 'El permiso ha sido actualizado',
            ], 200);
        }

        $result = Permission::create($validated);

        return response()->json([
            'status' => (bool) $result,
            'type' => $result ? 'success' : 'error',
            'messages' => $result ? 'Se ha añadido un nuevo permiso' : 'Recargue la página, algo salió mal',
            'message' => $result ? 'Se ha añadido un nuevo permiso' : 'Recargue la página, algo salió mal',
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(Permission::findOrFail($id), 200);
    }

    public function search(Request $request): JsonResponse
    {
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

    public function destroy(int $id): JsonResponse
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json([
            'status' => true,
            'type' => 'success',
            'messages' => 'El permiso fue eliminado',
            'message' => 'El permiso fue eliminado',
        ], 200);
    }
}
