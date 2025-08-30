<?php

namespace App\Http\Controllers\hcl;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamValidate;
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

    public function add(string $dni): View {
        $te = ExamType::get();
		$hc = History::where('dni', $dni)->get();
        return view('hcl.exams.add', compact('te', 'hc'));
    }

    public function edit(int $id): View {
		$te = ExamType::get();
		$hc	= Exam::seePatientByExam($id);
		$ex = Exam::findOrFail($id);
		return view('hcl.exams.edit', compact('te', 'hc', 'ex'));
    }

	public function seeExams(string $dni): View {
		$hc = History::where('dni', $dni)->get();
		return view('hcl.exams.see', compact('hc'));
	}

	public function viewDetail(int $id): JsonResponse {
		$data['ex'] 		= Exam::findOrFail($id);
		$data['hc']			= Exam::seePatientByExam($id);
		$data['diagnostic'] = DB::select('CALL getDiagnosticByExam(?)', [$id]);
		$data['medication'] = DB::select('CALL getMedicationByExam(?)', [$id]);
		return response()->json($data, 200);
	}

	public function viewExamImage(int $id): JsonResponse {
		return response()->json(Imagen::findOrFail($id), 200);
	}

    public function store(ExamValidate $request): JsonResponse {
        $validated 		= $request->validated();
		$diagnostics 	= $request->input('diagnostic_id');
		$drugs 			= $request->input('drug_id');
		$descriptions 	= $request->input('description');
		$img 			= $request->file('img');
		$dateImg 		= $request->input('dateImg');
        $id 			= $request->input('examId');
        $data 			= $request->only([
    		'dni', 'id_tipo', 'ta', 'fc', 'rf', 'so2', 'peso', 'talla', 'imc', 'pym', 'typ', 'cv', 'abdomen', 'hemolinfopoyetico', 'tcs', 'neurologico', 'hemograma', 'bioquimico', 'perfilhepatico', 'perfilcoagulacion', 'perfilreumatologico', 'orina', 'sangre', 'esputo', 'heces', 'lcr', 'citoquimico', 'adalp', 'paplp', 'bclp', 'cgchlp', 'cbklp', 'bkdab', 'bkcab', 'cgchab', 'papab', 'bcab', 'pulmon', 'pleurabpp', 'funcionpulmonar', 'medicinanuclear', 'plan', 'otros'
        ]);

        DB::beginTransaction();
        try {
            $exam 	= empty($id) ? Exam::create($data) : Exam::updateOrCreate(['id' => $id], $data);
            $id 	= $exam->id;
			$dni 	= $exam->dni;
            // Guardar diagnóstico, medicación y subir imagen si existen
            if ($diagnostics) 	$this->saveDiagnosis($id, $dni, $diagnostics);
            if ($drugs) 		$this->saveMedicacion($id, $dni, $drugs, $descriptions);
            if ($img) 			$this->uploadImage($img, $dni, $dateImg, $id);

            DB::commit();
            return response()->json([
                'status' 		=> true,
				'type'			=> 'success',
                'messages' 		=> empty($id) ? 'Se ha añadido un nuevo examen' : 'Actualizado exitosamente',
				'route' 		=> route('hcl.exams.see', $dni),
				'route_print' 	=> route('hcl.exams.print', $id)
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

    private function saveDiagnosis($id, $dni, $diagnosticId) {
        $data = collect($diagnosticId)->map(function ($diagnosticId) use ($id, $dni) {
			return [
				'id_examen'     	=> $id,
				'dni' 				=> $dni,
				'id_diagnostico'	=> $diagnosticId,
			];
		})->toArray();

		DiagnosticExam::insert($data);
		return;
    }

    private function saveMedicacion($id, $dni, $drugId, $description) {
		// Prepara los datos para la inserción
		$data = [];
		for ($i = 0; $i < count($drugId); $i++) {
			$data[] = [
				'id_examen'     => $id,
				'dni'           => $dni,
				'id_droga' 		=> $drugId[$i],
				'descripcion'   => $description[$i], // Usa el valor correspondiente del array
			];
		}
		
		MedicationExam::insert($data);
		return;
    }

	public function listExams(string $dni): JsonResponse {
		$results 		= DB::select('CALL getExamsByMedicalHistory(?)', [$dni]);
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
				$editRoute = route('hcl.exams.edit', ['id' => $item->id]);
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

	public function listOfImagesByExamId(int $id): JsonResponse {
		$results 	= DB::select('CALL getImgByExam(?)', [$id]);
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

	public function listOfDiagnosticsByExamId(int $id): JsonResponse {
		$results 		= DB::select('CALL getDiagnosticByExam(?)', [$id]);
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

	public function listOfMedicationByExamId(int $id): JsonResponse {
		$results 		= DB::select('CALL getMedicationByExam(?)', [$id]);
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
		$results['exams'] = DB::select('CALL getExamsByMedicalHistory(?)', [$dni]);
		return response()->json($results, 200);
	}

	public function seeRecipeByExam(int $id): JsonResponse {
		$exam 		= Exam::findOrFail($id);
		$medicacion = MedicationExam::where('id_examen', $id)->get();
		$diagnostic = DiagnosticExam::where('id_examen', $id)->get();
		return response()->json(compact('exam', 'medicacion', 'diagnostic'), 200);
	}

    private function uploadImage($images, $dni, $fechaImg, $id) {
        if ($images) {
            $directorio = "img/pacientes/{$dni}";
			Storage::disk('public')->makeDirectory($directorio);
			foreach ($images as $i => $image) {
				if ($image->isValid()) {
					$extension 	= $image->extension();
					$fileName 	= mt_rand(1000, 9999) . '.' . $extension;
					$route 		= "{$directorio}/{$fileName}";

					if ($image->storeAs($directorio, $fileName, 'public')) {
						Imagen::create([
							'id_examen'    => $id,
							'dni'          => $dni,
							'fecha_examen' => $fechaImg[$i],
							'imagen'       => $route,
						]);
					}
				}
			}
        }
    }

    public function destroy(int $id): JsonResponse {
		$result = Exam::findOrFail($id);
		$result->delete();
		return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? 'El examen fue eliminado' : 'Recargue la página, algo salió mal',
        ], 200);
    }

	public function destroyExamDiagnostics(int $id): JsonResponse {
		$result = DiagnosticExam::findOrFail($id);
		$result->delete();
		return response()->json([
			'status' 	=> (bool) $result,
			'type'   	=> $result ? 'success' : 'error',
			'messages' 	=> $result ? 'El diagnóstico fue quitado de este examen' : 'Recargue la página, algo salió mal',
		], 200);
	}

	public function destroyPrescriptionDrug(int $id): JsonResponse {
		$result = MedicationExam::findOrFail($id);
		$result->delete();
		return response()->json([
			'status' 	=> (bool) $result,
			'type'   	=> $result ? 'success' : 'error',
			'messages' 	=> $result ? 'El fármaco de la receta fue quitado' : 'Recargue la página, algo salió mal',
		], 200);
	}

	public function destroyExamImage(int $id): JsonResponse {
		$result = Imagen::findOrFail($id);
		$result->delete();
		return response()->json([
			'status'    => (bool) $result,
			'type'      => $result ? 'success' : 'error',
			'messages'  => $result ? 'La imagen fue eliminada' : 'Recargue la página, algo salió mal',
		], 200);
	}

	public function printPrescriptionId(int $id) {
		$hc = DB::select('CALL getMedicalHistoryByExam(?)', [$id]);
		$ex = Exam::findOrFail($id);
		$dx = DB::select('CALL getDiagnosticbyExam(?)', [$id]);
		$mx = DB::select('CALL getMedicationByExam(?)', [$id]);
		$us = Auth::user();
		$en = Enterprise::findOrFail(1);
		$pdf = PDF::loadView('hcl.exams.pdf', compact('hc', 'ex', 'dx', 'mx', 'us', 'en'))
			->setPaper('a5')
        	->setOptions(['defaultFont' => 'sans-serif'])
        	->setOptions([
				'margin-top' 	=> 0.5, 
				'margin-bottom' => 0.5, 
				'margin-left' 	=> 0.5, 
				'margin-right' 	=> 0.5,
			]);
        return $pdf->stream("examen-{$id}.pdf");
	}
}