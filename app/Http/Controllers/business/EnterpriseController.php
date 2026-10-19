<?php

namespace App\Http\Controllers\business;

use App\Http\Controllers\Controller;
use App\Models\Enterprise;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EnterpriseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:empresa_acceder')->only('index');
        $this->middleware('permission:empresa_ver')->only('getData', 'getEnterprise', 'getImages');
        $this->middleware('permission:empresa_actualizar')->only('store');
    }

    public function index(): View
    {
        $etp = Enterprise::first();
        if (! $etp) {
            $etp = Enterprise::create([
                'razon_social' => 'NeumoLav',
                'nombre_comercial' => 'NeumoLav',
                'ruc' => '20000000001',
                'email' => 'contacto@neumolav.com',
                'pais' => 'Perú',
                'codigo_pais' => '+51',
                'telefono' => '999999999',
                'pagina_web' => 'neumolav.com',
                'representante_legal' => 'Dr. NeumoLav',
            ]);
        }

        return view('business.enterprise.index', compact('etp'));
    }

    public function getData(): JsonResponse
    {
        $enterprise = Enterprise::first();
        if (! $enterprise) {
            $enterprise = Enterprise::create([
                'razon_social' => 'NeumoLav',
                'nombre_comercial' => 'NeumoLav',
                'ruc' => '20000000001',
                'email' => 'contacto@neumolav.com',
                'pais' => 'Perú',
                'codigo_pais' => '+51',
                'telefono' => '999999999',
                'pagina_web' => 'neumolav.com',
                'representante_legal' => 'Dr. NeumoLav',
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $enterprise,
            'images' => [
                'foto_representante' => $enterprise->foto_representante ? asset('storage/'.$enterprise->foto_representante) : null,
                'logo' => $enterprise->logo ? asset('storage/'.$enterprise->logo) : null,
                'logo_mini' => $enterprise->logo_mini ? asset('storage/'.$enterprise->logo_mini) : null,
                'logo_receta' => $enterprise->logo_receta ? asset('storage/'.$enterprise->logo_receta) : null,
            ],
            'permissions' => [
                'update' => auth()->user()?->can('empresa_actualizar') ?? false,
            ],
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $enterprise = Enterprise::where('id', $request->input('id'))->first() ?: Enterprise::first();
        if (! $enterprise) {
            return response()->json([
                'status' => false,
                'type' => 'error',
                'messages' => 'Empresa no encontrada',
            ], 404);
        }

        $op = (int) $request->input('op', 1);

        try {
            switch ($op) {
                case 1: // Actualización de datos básicos
                    $data = $request->only([
                        'razon_social', 'nombre_comercial', 'ruc', 'email', 'descripcion',
                        'direccion', 'codigo_pais', 'pais', 'telefono', 'pagina_web',
                        'representante_legal', 'frase', 'mision', 'vision', 'ubigeo',
                        'slogan', 'iframe_location', 'rubro', 'autocomplete',
                    ]);

                    $fechaCreacion = $request->input('fecha_creacion', $request->input('fce'));
                    if ($fechaCreacion !== null) {
                        $data['fecha_creacion'] = $fechaCreacion;
                    }

                    $data['nombre_comercial'] = trim((string) ($request->input('nombre_comercial') ?? ''));
                    $data['razon_social'] = trim((string) ($request->input('razon_social') ?? ''));
                    $data['representante_legal'] = trim((string) ($request->input('representante_legal') ?? ''));

                    $enterprise->update($data);

                    return response()->json([
                        'status' => true,
                        'type' => 'success',
                        'messages' => 'Información de la empresa actualizada exitosamente',
                        'data' => $enterprise->fresh(),
                    ], 200);

                case 2: // Actualizar foto del representante
                    $file = $request->file('foto-representante') ?: $request->file('foto_representante');
                    if ($file) {
                        $path = $this->uploadFile($enterprise, 'foto_representante', $file);

                        return response()->json([
                            'status' => true,
                            'type' => 'success',
                            'messages' => 'Foto del representante legal actualizada exitosamente',
                            'image_url' => asset('storage/'.$path),
                        ], 200);
                    }

                    return response()->json([
                        'status' => false,
                        'type' => 'warning',
                        'messages' => 'No se seleccionó ningún archivo de imagen',
                    ], 422);

                case 3: // Actualización del logo
                    $file = $request->file('logo');
                    if ($file) {
                        $path = $this->uploadFile($enterprise, 'logo', $file);

                        return response()->json([
                            'status' => true,
                            'type' => 'success',
                            'messages' => 'Logo institucional actualizado exitosamente',
                            'image_url' => asset('storage/'.$path),
                        ], 200);
                    }

                    return response()->json([
                        'status' => false,
                        'type' => 'warning',
                        'messages' => 'No se seleccionó ningún archivo de imagen',
                    ], 422);

                case 4: // Actualización del mini-logo
                    $file = $request->file('mini-logo') ?: $request->file('logo_mini');
                    if ($file) {
                        $path = $this->uploadFile($enterprise, 'logo_mini', $file);

                        return response()->json([
                            'status' => true,
                            'type' => 'success',
                            'messages' => 'Miniatura del logo actualizada exitosamente',
                            'image_url' => asset('storage/'.$path),
                        ], 200);
                    }

                    return response()->json([
                        'status' => false,
                        'type' => 'warning',
                        'messages' => 'No se seleccionó ningún archivo de imagen',
                    ], 422);

                case 5: // Actualización del logo-receta
                    $file = $request->file('logo-receta') ?: $request->file('logo_receta');
                    if ($file) {
                        $path = $this->uploadFile($enterprise, 'logo_receta', $file);

                        return response()->json([
                            'status' => true,
                            'type' => 'success',
                            'messages' => 'Imagen de fondo para recetas actualizada exitosamente',
                            'image_url' => asset('storage/'.$path),
                        ], 200);
                    }

                    return response()->json([
                        'status' => false,
                        'type' => 'warning',
                        'messages' => 'No se seleccionó ningún archivo de imagen',
                    ], 422);

                default:
                    return response()->json([
                        'status' => false,
                        'type' => 'error',
                        'messages' => 'Operación no válida',
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'type' => 'error',
                'messages' => 'Error interno al actualizar empresa: '.$e->getMessage(),
            ], 500);
        }
    }

    private function uploadFile(Enterprise $model, string $field, $file): string
    {
        $previousPath = $model->getRawOriginal($field);
        $filename = Str::random(10).'.'.$file->extension();
        $path = $file->storeAs('photos', $filename, 'public');
        $model->update([$field => $path]);

        if ($previousPath && Storage::disk('public')->exists($previousPath)) {
            Storage::disk('public')->delete($previousPath);
        }

        return $path;
    }

    public function getEnterprise(): JsonResponse
    {
        $info = Enterprise::where('id', 1)->first() ?: Enterprise::first();

        return response()->json(['info' => $info ? [$info] : []], 200);
    }

    public function getImages(): JsonResponse
    {
        $enterprise = Enterprise::first();
        if (! $enterprise) {
            return response()->json(['status' => false, 'message' => 'Empresa no encontrada'], 404);
        }

        return response()->json([
            'status' => true,
            'foto_representante' => $enterprise->foto_representante ? asset('storage/'.$enterprise->foto_representante) : asset('storage/img/anonymous.png'),
            'logo' => $enterprise->logo ? asset('storage/'.$enterprise->logo) : asset('storage/img/anonymous.png'),
            'logo_mini' => $enterprise->logo_mini ? asset('storage/'.$enterprise->logo_mini) : asset('storage/img/anonymous.png'),
            'logo_receta' => $enterprise->logo_receta ? asset('storage/'.$enterprise->logo_receta) : asset('storage/img/anonymous.png'),
        ]);
    }
}
