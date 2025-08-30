<?php

namespace App\Http\Controllers\business;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostValidate;
use GuzzleHttp\Client;
use App\Models\Post;
use App\Models\PostType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\SummaryService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;
use Throwable;

class PostsController extends Controller {

    protected $summaryService;
    public function __construct(SummaryService $summaryService) {
        $this->middleware('auth');
        $this->middleware('permission:post_acceder')->only('index');
        $this->middleware('permission:post_crear')->only('add', 'store');
        $this->middleware('permission:post_actualizar')->only('edit', 'store');
        $this->middleware('permission:post_ver')->only('list');
        $this->middleware('permission:post_borrar')->only('destroy');
        $this->summaryService = $summaryService;
    }
    public function index(): View {
        return view('business.posts.index');
    }

    public function add(): View {
        $tipo_post = PostType::get();
        return view('business.posts.add', compact('tipo_post'));
    }

    public function edit(int $id): View {
        $tipo_post  = PostType::get();
        $post       = Post::findOrFail($id);
        return view('business.posts.edit', compact('post', 'tipo_post'));
    }

    public function list(): JsonResponse {
        $results    = Post::join('post_type as pt', 'pt.id', '=', 'post.type_id')
            ->select('post.*', 'pt.description')
            ->orderBy('post.created_at', 'desc')->get();
        $data       = $results->map(function ($item, $key) {
            $user   = auth()->user();
            $buttons = '';

            if ($user->can('post_actualizar')) {
                $route = route('business.posts.edit', ['id' => $item->id]);
                $buttons .= sprintf(
                    '<a href="%s" class="btn btn-sm btn-warning btn-md" value="%s">
                        <i class="bi bi-pencil-square"></i>
                    </a>&nbsp;',
                    htmlspecialchars($route, ENT_QUOTES, 'UTF-8'),
                    $item->id,
                );
            }

            if ($user->can('post_borrar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-sm btn-danger delete-post btn-md" value="%s">
                        <i class="bi bi-trash"></i>
                    </button>',
                    $item->id
                );
            }

            return [
                $key + 1, // Aquí usas $key como índice
                $item->titulo,
                $item->description,
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

    public function store(PostValidate $request): JsonResponse {
        $validated = $request->validated();
        $id        = $request->input('postId');

        $extraDataMap = [
			'autor_post'    => auth()->user()->id,
            'titulo_corto'  => $request->titulo_corto,
		];

        $processedFields = [
            'url'           => strtolower($this->formatDescription($validated['titulo'])),
			'alt_img'       => strtolower($this->formatDescription($validated['descrip_img'])),
		];

        $data = array_merge($validated, $extraDataMap, $processedFields);

        DB::beginTransaction();
        try {
            // Manejo de la imagen
            if ($request->hasFile('img')) {
                if ($id) {
                    $post = Post::find($id);
                    if ($post && $post->img) {
                        Storage::delete('public/'.$post->img); // Asegúrate de eliminar la imagen anterior
                    }
                }
                // Guardar nueva imagen en el disco 'public'
                $data['img'] = $request->file('img')->store('homepage', 'public');
            }

            if (!empty($validated['titulo'])) {
                $validated['url'] = $this->formatDescription($validated['titulo']);
            }
            
            $result = Post::updateOrCreate(['id' => $id], $data);

            DB::commit();
            return response()->json([
                'status'    => (bool) $result,
                'type'      => $result ? 'success' : 'error',
                'message'   => $result ? ($result->wasRecentlyCreated ? '¡Post creado con éxito!' : '¡Post actualizado!') : 'Recargue la página y vuelva a intentarlo',
                'route'     => route('business.posts'),
                'data'      => $result,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            // Eliminar imagen si se subió pero falló la transacción
            if (isset($validated['img'])) {
                Storage::disk('public')->delete($validated['img']);
            }

            return response()->json([
                'status'        => false,
                'type'          => 'error',
                'message'       => 'Error al procesar el post: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }

    protected function handleImageUpload($request, $postId = null): string {
        if ($postId && ($post = Post::find($postId))) {
            // Guardar referencia a la imagen anterior para eliminación después de commit
            $oldImage = $post->img;
            // Eliminar después del commit exitoso
            DB::afterCommit(function () use ($oldImage) {
                if ($oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            });
        }

        return $request->file('img')->store('homepage', 'public');
    }

    protected function formatDescription(string $description): string {
        return Str::slug($description);
    }

    public function destroy(int $id): JsonResponse {
        $result = Post::findOrFail($id);
        $result->delete();
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? 'El post fue eliminado' : 'Recargue la página, algo salió mal',
        ], 200);
    }
}
