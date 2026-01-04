<?php

namespace App\Http\Controllers\hcl;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamValidate;
use App\Http\Resources\ImageResource;
use App\Models\DiagnosticExam;
use App\Models\Exam;
use App\Models\Enterprise;
use App\Models\ExamType;
use App\Models\History;
use App\Models\Imagen;
use App\Models\MedicationExam;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExamsController extends Controller {

    public function __construct() {
        $this->middleware(['auth', 'prevent.back']);
		$this->middleware('permission:examen_acceder')->only('index');
		$this->middleware('permission:examen_ver')->only('seeExams', 'listExams', 'viewDetail');
		$this->middleware('permission:examen_crear')->only('add', 'store');
		$this->middleware('permission:examen_crear')->only('edit', 'store');
		$this->middleware('permission:examen_borrar')->only('destroy', 'destroyExamDiagnostics', 'destroyPrescriptionDrug', 'destroyExamImage');
    }

    public function index(): View {
        return view('hcl.exams.index');
    }

    public function add(History $hc): View {
        $te = ExamType::get();
        return view('hcl.exams.add', compact('te', 'hc'));
    }

    public function edit(Exam $ex): View {
		$te = ExamType::get();
		$hc	= History::where('id', $ex->id_historia)->first();
		return view('hcl.exams.edit', compact('te', 'hc', 'ex'));
    }

	public function see(History $hc): View {
		return view('hcl.exams.see', compact('hc'));
	}

	public function viewDetail(Exam $ex): JsonResponse {
		$hc			= History::where('dni', $ex->dni)->get();
		$diagnostic = DB::select('CALL PA_getDiagnosticByExam(?)', [$ex->id]);
		$medication = DB::select('CALL PA_getMedicationByExam(?)', [$ex->id]);
		return response()->json(compact('ex', 'hc', 'diagnostic', 'medication'), 200);
	}

	public function viewExamImage(Imagen $image): JsonResponse {
		return response()->json(ImageResource::make($image), 200);
	}

    public function store(ExamValidate $request): JsonResponse {
        $validated 		= $request->validated();
        $id             = $request->input('examId');
		$diagnostics    = $request->input('diagnostic_id');
        $drugs          = $request->input('drug_id');
        $descriptions   = $request->input('description');
		$img 			= $request->file('img');
		$dateImg 		= $request->input('dateImg');

        DB::beginTransaction();
        try {
            $exam       = Exam::updateOrCreate(['id' => $id], $validated);
            $id 	    = $exam->id;
            $historia   = $exam->id_historia;
			$dni 	    = $exam->dni;
            // Guardar diagnóstico, medicación y subir imagen si existen
            if ($diagnostics) 	$this->saveDiagnosis($id, $historia, $dni, $diagnostics);
            if ($drugs) 		$this->saveMedicacion($id, $historia, $dni, $drugs, $descriptions);
            if ($img) 			$this->uploadImage($img, $historia, $dni, $dateImg, $id);

            DB::commit();
            return response()->json([
                'status' 	=> true,
				'type'		=> 'success',
                'message' 	=> $exam->wasChanged() ? 'Actualizado exitosamente' : 'Se ha añadido un nuevo examen',
				'route' 	=> route('hcl.exams.see', $exam->id_historia),
				'print_a4' 	=> route('hcl.exams.print', [$id, 'a4']),
				'print_a5' 	=> route('hcl.exams.print', [$id, 'a5']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' 	=> false,
				'type'		=> 'error',
                'messages' 	=> $e->getMessage(),
            ], 500);
        }
    }

    private function saveDiagnosis($id, $historia, $dni, $diagnosticId) {
        $data = collect($diagnosticId)->map(function ($diagnosticId) use ($id, $historia, $dni) {
			return [
				'id_examen'         => $id,
                'id_historia'       => $historia,
				'dni' 				=> $dni,
				'id_diagnostico'	=> $diagnosticId,
				'created_at' 		=> now(),
                'updated_at' 		=> now(),
			];
		})->toArray();

		DiagnosticExam::insert($data);
		return;
    }

    private function saveMedicacion($id, $historia, $dni, $drugId, $description) {
		$data = [];
		for ($i = 0; $i < count($drugId); $i++) {
			$data[] = [
				'id_examen'     => $id,
                'id_historia'   => $historia,
				'dni'           => $dni,
				'id_droga' 		=> $drugId[$i],
				'descripcion'   => $description[$i],
				'created_at' 	=> now(),
                'updated_at'    => now(),
			];
		}

		MedicationExam::insert($data);
		return;
    }

	private function uploadImage($images, $historia, $dni, $fechaImg, $id) {
        if ($images) {
            $directorio = "img/pacientes/{$dni}";
			if (!Storage::exists($directorio)) {
                Storage::makeDirectory($directorio);
            }
			# Storage::disk('public')->makeDirectory($directorio);
			foreach ($images as $i => $image) {
				if ($image->isValid()) {
					$extension 	= $image->getClientOriginalExtension();
					$fileName 	= mt_rand(1000, 9999).'.'.$extension;
					$route 		= "{$directorio}/{$fileName}";

					if ($image->storeAs($directorio, $fileName, 'public')) {
						Imagen::create([
							'id_examen'     => $id,
                            'id_historia'   => $historia,
							'dni'           => $dni,
							'fecha_examen'  => $fechaImg[$i],
							'imagen'        => $route,
						]);
					}
				}
			}
        }
    }

	public function listExams(History $hc): JsonResponse {
		$results 		= DB::select('CALL PA_getExamsByMedicalHistory(?)', [$hc->id]);
		$data 			= collect($results)->map(function ($item, $index) {
			$user   	= auth()->user();
			$buttons 	= '';

			if($user->can('informe_ver')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-info view-exam btn-xs" value="%s"><i class="bi bi-eye"></i> Ver receta</button>&nbsp;',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }

			if($user->can('informe_actualizar')){
				$editRoute = route('hcl.exams.edit', ['ex' => $item->id]);
                $buttons .= sprintf(
                    '<a type="button" class="btn btn-warning btn-xs" href="%s"><i class="bi bi-pencil-square"></i> Editar</a>&nbsp;',
                    htmlspecialchars($editRoute, ENT_QUOTES, 'UTF-8'),
                );
            }

			if($user->can('informe_borrar')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-danger delete-exam btn-xs" value="%s"><i class="bi bi-trash"></i> Eliminar</button>',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }

			return [
				$index + 1,
                $item->created_at,
                $item->dni,
                $item->examen,
                $buttons ?: '<span class="text-muted">No autorizado</span>'
			];
		});

		return response()->json([
 			"sEcho"					=> 1,
 			"iTotalRecords"			=> $data->count(),
 			"iTotalDisplayRecords"	=> $data->count(),
 			"aaData"				=> $data ?? [],
 		], 200);
	}

	public function listOfImagesByExam(Exam $ex): JsonResponse {
		$results 	= DB::select('CALL PA_getImgByExam(?)', [$ex->id]);
		$data 		= collect($results)->map(function ($item, $index) {
			return [
				$index + 1,
				$item->fecha_examen,
				$item->created_at,
				sprintf(
					'<button type="button" class="btn btn-info view-img-exam btn-xs" value="%s"><i class="bi bi-eye"></i> Ver imagen</button>
					<button type="button" class="btn btn-danger delete-image btn-xs" value="%s"><i class="bi bi-trash"></i> Eliminar</button>',
					$item->id,
					$item->id
				)
			];
		});

		return response()->json([
			"sEcho"					=> 1,
			"iTotalRecords"			=> $data->count(),
			"iTotalDisplayRecords"	=> $data->count(),
			"aaData"				=> $data ?? [],
		], 200);
	}

	public function listOfDiagnosticsByExam(Exam $ex): JsonResponse {
		$results 		= DB::select('CALL PA_getDiagnosticByExam(?)', [$ex->id]);
		$data 			= collect($results)->map(function ($item, $index) {
			$user 		= auth()->user();
			$buttons 	= '';
			if($user->can('examen_borrar')){
				$buttons .= sprintf(
					'<button type="button" class="btn btn-danger delete-diagnostic btn-xs" value="%s"><i class="bi bi-trash"></i> Eliminar</button>',
					$item->id,
				);
			}
			return [
				$index + 1,
				$item->diagnostic,
				$buttons ?: '<span class="text-muted">No autorizado</span>'
			];
		});

		return response()->json([
			"sEcho"					=> 1,
			"iTotalRecords"			=> $data->count(),
			"iTotalDisplayRecords"	=> $data->count(),
			"aaData"				=> $data ?? [],
		], 200);
	}

	public function listOfMedicationByExam(Exam $ex): JsonResponse {
		$results 		= DB::select('CALL PA_getMedicationByExam(?)', [$ex->id]);
		$data 			= collect($results)->map(function ($item, $index) {
			$user 		= auth()->user();
			$buttons 	= '';
			if($user->can('examen_borrar')){
				$buttons .= sprintf(
					'<button type="button" class="btn btn-danger delete-drug btn-xs" value="%s"><i class="bi bi-trash"></i> Eliminar</button>',
					$item->id
				);
			}
			return [
				$index + 1,
				$item->drug,
				$item->rp,
				$buttons ?: '<span class="text-muted">No autorizado</span>',
			];
		});

		return response()->json([
			"sEcho"					=> 1,
			"iTotalRecords"			=> $data->count(),
			"iTotalDisplayRecords"	=> $data->count(),
			"aaData"				=> $data ?? [],
		], 200);
	}

	public function seeExamsByMedicalHistory(string $dni): JsonResponse {
		$results['exams'] = DB::select('CALL PA_getExamsByMedicalHistory(?)', [$dni]);
		return response()->json($results, 200);
	}

	public function seeRecipeByExam(int $id): JsonResponse {
		$exam 		= Exam::findOrFail($id);
		$medicacion = MedicationExam::where('id_examen', $id)->get();
		$diagnostic = DiagnosticExam::where('id_examen', $id)->get();
		return response()->json(compact('exam', 'medicacion', 'diagnostic'), 200);
	}

    public function destroy(Exam $ex): JsonResponse {
		$ex->delete();
		return response()->json([
            'status'    => (bool) $ex,
            'type'      => $ex ? 'success' : 'error',
            'messages'  => $ex ? 'El examen fue eliminado' : 'Recargue la página, algo salió mal',
        ], 200);
    }

	public function destroyExamDiagnostic(DiagnosticExam $dx): JsonResponse {
		$dx->delete();
		return response()->json([
			'status' 	=> (bool) $dx,
			'type'   	=> $dx ? 'success' : 'error',
			'messages' 	=> $dx ? 'El diagnóstico fue quitado de este examen' : 'Recargue la página, algo salió mal',
		], 200);
	}

	public function destroyPrescriptionDrug(MedicationExam $mx): JsonResponse {
		$mx->delete();
		return response()->json([
			'status' 	=> (bool) $mx,
			'type'   	=> $mx ? 'success' : 'error',
			'messages' 	=> $mx ? 'El fármaco de la receta fue quitado' : 'Recargue la página, algo salió mal',
		], 200);
	}

	public function destroyExamImage(Imagen $ix): JsonResponse {
		$ix->delete();
		return response()->json([
			'status'    => (bool) $ix,
			'type'      => $ix ? 'success' : 'error',
			'messages'  => $ix ? 'La imagen fue eliminada' : 'Recargue la página, algo salió mal',
		], 200);
	}

	public function printPrescription(Exam $ex, string $format = 'a5') {
        // Validar formato
        if (!in_array($format, ['a4', 'a5'])) {
            $format = 'a5';
        }
        // Obtener datos
        $hc = DB::select('CALL PA_getMedicalHistoryByExam(?)', [$ex->id]);
        $dx = DB::select('CALL PA_getDiagnosticbyExam(?)', [$ex->id]);
		$mx = DB::select('CALL PA_getMedicationByExam(?)', [$ex->id]);
        $us = Auth::user();
        $en = Enterprise::findOrFail(1);

        // Configurar PDF según formato
        if ($format === 'a4') {
			$pdf = PDF::loadView('hcl.exams.pdf-a4', compact('hc', 'ex', 'dx', 'mx', 'us', 'en', 'format'));
            $pdf->setPaper('a4', 'portrait')
                ->setOptions([
                    'margin_top' 	        => 10,
                    'margin_bottom'         => 10,
                    'margin_left' 	        => 15,
                    'margin_right' 	        => 15,
					'fontDefault'           => 'sans-serif',
                    'isHtml5ParserEnabled'  => true,
					'isRemoteEnabled'       => false,
					'isPhpEnabled'          => false,
					'chroot'                => realpath(base_path()),
                ]);
        } else {
			$pdf = PDF::loadView('hcl.exams.pdf-a5', compact('hc', 'ex', 'dx', 'mx', 'us', 'en', 'format'));
            $pdf->setPaper('a5', 'portrait')
                ->setOptions([
                    'margin_top' 			=> 0.5,
                    'margin_bottom' 		=> 0.5,
                    'margin_left' 			=> 0.5,
                    'margin_right' 			=> 0.5,
					'fontDefault'           => 'sans-serif',
                    'isHtml5ParserEnabled'  => true,
					'isRemoteEnabled'       => false,
					'isPhpEnabled'          => false,
					'chroot'                => realpath(base_path()),
                ]);
        }

        $filename = "receta-medica-examen-{$ex->id}-" . strtoupper($format) . ".pdf";
        return $pdf->stream($filename);
    }
}
