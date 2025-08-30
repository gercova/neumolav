<?php

namespace App\Http\Controllers\business;

use App\Http\Controllers\Controller;
use App\Models\Enterprise;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EnterpriseController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:empresa_acceder')->only('index');
        $this->middleware('permission:empresa_actualizar')->only('store');
    }

    public function index(): View {
        $etp = Enterprise::first();
        return view('business.enterprise.index', compact('etp'));
    }

    public function store(Request $request): JsonResponse {
        $enterprise = Enterprise::where('id', $request->input('id'))->first();
        if (!$enterprise) return response()->json(['status' => false, 'type' => 'error', 'messages' => 'Empresa no encontrada'], 404);
        $response = ['status' => false, 'type' => 'error', 'messages' => 'No se pudo actualizar'];
    
        try {
            switch ($request->input('op')) {
                case 1: // Actualización de datos básicos
                    $data = $request->only([
                        'razon_social', 'nombre_comercial', 'ruc', 'email', 'descripcion', 'direccion', 'codigo_pais', 'pais', 'telefono', 'pagina_web', 'representante_legal', 'frase', 'mision', 'vision', 'ubigeo', 'slogan', 'iframe_location', 'rubro', 'fecha_creacion', 'autocomplete'
                    ]);
                    $data['nombre_comercial']       = trim(request('nombre_comercial') ?? '');
                    $data['razon_social']           = trim(request('razon_social') ?? '');
                    $data['representante_legal']    = trim(request('representante_legal') ?? '');
    
                    if ($enterprise->update($data)) $response = ['status' => true, 'type' => 'success', 'messages' => 'Actualizado exitosamente'];
                    break;

                case 2: //Actualizar foto del representante
                    if ($request->hasFile('foto-representante')) {
                        $this->uploadFile($enterprise, 'foto_representante', $request->file('foto-representante'));
                        //$enterprise->foto_representante = $request->file('foto-representante')->store('photos', 'public');
                        $response = ['status' => true, 'type' => 'success', 'messages' => 'Foto actualizada exitosamente'];
                    }
                    break;
    
                case 3: // Actualización del logo
                    if ($request->hasFile('logo')) {
                        $this->uploadFile($enterprise, 'logo', $request->file('logo'));
                        // $enterprise->logo = $request->file('logo')->store('photos', 'public');
                        $response = ['status' => true, 'type' => 'success', 'messages' => 'Logo actualizado exitosamente'];
                    }
                    break;
    
                case 4: // Actualización del mini-logo
                    if ($request->hasFile('mini-logo')) {
                        $this->uploadFile($enterprise, 'logo_mini', $request->file('mini-logo'));
                        // $enterprise->logo_mini = $request->file('mini-logo')->store('photos', 'public');
                        $response = ['status' => true, 'type' => 'success', 'messages' => 'Mini-logo actualizado exitosamente'];
                    }
                    break;
    
                case 5: // Actualización del logo-receta
                    if ($request->hasFile('logo-receta')) {
                        $this->uploadFile($enterprise, 'logo_receta', $request->file('logo-receta'));
                        // $enterprise->logo_receta = $request->file('logo-receta')->store('photos', 'public');
                        $response = ['status' => true, 'type' => 'success', 'messages' => 'Imagen actualizada exitosamente'];
                    }
                    break;
    
                default:
                    $response = ['status' => false, 'type' => 'error', 'messages' => 'Operación no válida'];
            }
        } catch (\Exception $e) {
            $response = ['status' => false, 'type' => 'error', 'messages' => 'Error interno: '.$e->getMessage()];
        }
    
        return response()->json($response, 200);
    }
    
    private function uploadFile($model, $field, $file) {
        $previousPath   = $model->getRawOriginal($field);
        $filename       = Str::random(10).'.'.$file->extension();
        $path           = $file->storeAs('/photos', $filename, 'public');
        $model->update([$field => $path]);
        
        if ($previousPath) Storage::delete($previousPath);
        return $path;
    }

    public function getEnterprise(): JsonResponse {
        $info = Enterprise::where('id', 1)->get();
        return response()->json(['info' => $info], 200);
    }

    public function getImages(): JsonResponse {
        $enterprise = Enterprise::find(1);
        if (!$enterprise) return response()->json(['status' => false, 'message' => 'Empresa no encontrada'], 404);
        // Devuelve las rutas de las imágenes
        return response()->json([
            'status'                => true,
            'foto_representante'    => asset('storage/'.$enterprise->foto_representante),
            'logo'                  => asset('storage/'.$enterprise->logo),
            'logo_mini'             => asset('storage/'.$enterprise->logo_mini),
            'logo_receta'           => asset('storage/'.$enterprise->logo_receta),
        ]);
    }
}
