<?php

namespace App\Http\Controllers\business;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostValidate;
use App\Models\Post;
use App\Models\PostType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:post_acceder')->only('index', 'add', 'edit');
        $this->middleware('permission:post_ver')->only('list', 'show', 'options');
        $this->middleware('permission:post_crear|post_actualizar')->only('store');
        $this->middleware('permission:post_borrar')->only('destroy');
    }

    public function index(): View
    {
        $postTypes = PostType::select('id', 'description')->get();

        return view('business.posts.index', compact('postTypes'));
    }

    public function options(): JsonResponse
    {
        return response()->json([
            'postTypes' => PostType::select('id', 'description')->get(),
        ]);
    }

    public function add(): RedirectResponse
    {
        return redirect()->route('business.posts');
    }

    public function edit(int $id): RedirectResponse
    {
        return redirect()->route('business.posts');
    }

    public function list(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $typeId = $request->input('type_id');

        $query = Post::with('postType')
            ->select('post.*')
            ->orderBy('post.created_at', 'desc');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'LIKE', "%{$search}%")
                    ->orWhere('titulo_corto', 'LIKE', "%{$search}%")
                    ->orWhere('resumen', 'LIKE', "%{$search}%");
            });
        }

        if (! empty($typeId)) {
            $query->where('type_id', $typeId);
        }

        $user = auth()->user();
        $permissions = [
            'create' => $user?->can('post_crear') ?? false,
            'update' => $user?->can('post_actualizar') ?? false,
            'delete' => $user?->can('post_borrar') ?? false,
        ];

        $pageSize = (int) $request->input('pageSize', 10);
        if ($pageSize > 0) {
            $page = (int) $request->input('page', 1);
            $paginator = $query->paginate($pageSize, ['*'], 'page', $page);
            $items = collect($paginator->items())->map(fn ($item) => $this->formatPost($item));

            return response()->json([
                'status' => true,
                'data' => $items,
                'total' => $paginator->total(),
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'pageSize' => $paginator->perPage(),
                'permissions' => $permissions,
            ]);
        }

        $items = $query->get()->map(fn ($item) => $this->formatPost($item));

        return response()->json([
            'status' => true,
            'data' => $items,
            'total' => $items->count(),
            'permissions' => $permissions,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $post = Post::with('postType')->findOrFail($id);

        return response()->json([
            'id' => $post->id,
            'type_id' => $post->type_id,
            'post_type' => $post->postType?->description,
            'titulo' => $post->titulo,
            'titulo_corto' => $post->titulo_corto,
            'url' => $post->url,
            'img' => $post->img,
            'img_url' => $post->img ? asset('storage/'.$post->img) : null,
            'descrip_img' => $post->descrip_img,
            'alt_img' => $post->alt_img,
            'resumen' => $post->resumen,
            'contenido' => $post->contenido,
            'categories' => $post->categories,
            'meta_content' => $post->meta_content,
            'key_words' => $post->key_words,
            'etiquetas' => $post->etiquetas,
            'created_at' => $post->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $post->updated_at?->format('Y-m-d H:i:s'),
        ], 200);
    }

    public function store(PostValidate $request): JsonResponse
    {
        $validated = $request->validated();
        $id = $request->filled('postId') ? (int) $request->input('postId') : null;

        $extraData = [
            'autor_post' => auth()->id(),
            'titulo_corto' => $request->filled('titulo_corto') ? $request->input('titulo_corto') : null,
        ];

        $processed = [
            'url' => Str::slug($validated['titulo']),
            'alt_img' => Str::slug($validated['descrip_img']),
        ];

        $data = array_merge($validated, $extraData, $processed);

        try {
            if ($request->hasFile('img')) {
                if ($id && ($existingPost = Post::find($id)) && $existingPost->img) {
                    Storage::disk('public')->delete($existingPost->img);
                }
                $data['img'] = $request->file('img')->store('homepage', 'public');
            }

            if ($id) {
                $post = Post::findOrFail($id);
                $post->update($data);
                $result = $post;
            } else {
                $result = Post::create($data);
            }

            return response()->json([
                'status' => (bool) $result,
                'type' => $result ? 'success' : 'error',
                'message' => $result
                    ? ($id ? '¡Publicación actualizada correctamente!' : '¡Publicación creada con éxito!')
                    : 'No se pudo procesar la publicación',
                'route' => route('business.posts'),
                'data' => $this->formatPost($result->load('postType')),
            ], 200);

        } catch (Throwable $e) {
            if (isset($data['img']) && ! $id) {
                Storage::disk('public')->delete($data['img']);
            }

            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => 'Error al procesar la publicación: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
            'status' => true,
            'type' => 'success',
            'message' => 'La publicación fue eliminada',
            'messages' => 'La publicación fue eliminada',
        ], 200);
    }

    /**
     * @return array{id: int, type_id: int, post_type: string|null, titulo: string, titulo_corto: string|null, url: string|null, img: string|null, img_url: string|null, resumen: string|null, created_at: string|null, updated_at: string|null}
     */
    private function formatPost(Post $post): array
    {
        return [
            'id' => $post->id,
            'type_id' => $post->type_id,
            'post_type' => $post->postType?->description,
            'titulo' => $post->titulo,
            'titulo_corto' => $post->titulo_corto,
            'url' => $post->url,
            'img' => $post->img,
            'img_url' => $post->img ? asset('storage/'.$post->img) : null,
            'resumen' => $post->resumen,
            'created_at' => $post->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $post->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
