<?php

namespace App\Http\Controllers\hcl;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentValidate;
use App\Models\Appointment;
use App\Models\DiagnosticAppointment;
use App\Models\Enterprise;
use App\Models\History;
use App\Models\MedicationAppointment;
use App\Services\TableViewService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AppointmentsController extends Controller {
    
	protected $tableViewService;
	public function __construct(TableViewService $tableViewService) {
		$this->tableViewService = $tableViewService;
        $this->middleware(['auth', 'prevent.back']);
		$this->middleware('permission:control_acceder')->only('index');
		$this->middleware('permission:control_ver')->only('seeAppointments', 'listAppointments', 'viewDetail');
		$this->middleware('permission:control_crear')->only('add', 'store');
		$this->middleware('permission:control_actualizar')->only('edit', 'store');
		$this->middleware('permission:control_borrar')->only('destroy', 'destroyDiagnosticAppointment', 'destroyMedicationAppointment');
    }

    public function index(): View {
        return view('hcl.appointments.index');
    }

    public function add(string $dni): View {
		$hc = History::where('dni', $dni)->get();
        return view('hcl.appointments.add', compact('hc'));
    }

    public function edit(int $id): View {
        $hc	= Appointment::seePatientByAppointment($id);
		$ap = Appointment::findOrFail($id);
		return view('hcl.appointments.edit', compact('hc', 'ap'));
    }

	public function seeAppointments(string $dni): View {
		$hc = History::where('dni', $dni)->get();
		return view('hcl.appointments.see', compact('hc'));
	}

	public function viewTable(Request $request){
        $request->validate([
            'table' => 'nullable|string|in:controles,examenes',
            'dni' 	=> 'required|string'
        ]);

        $table 	= $request->input('table');
        $dni 	= $request->input('dni');

        return $this->tableViewService->generateTableView($table, $dni);
    }

    public function viewDetail(int $id): JsonResponse {
		$data['ap'] 		= Appointment::findOrFail($id);
		$data['hc']			= Appointment::seePatientByAppointment($id);
		$data['diagnostic'] = DB::select('CALL getDiagnosticByAppointment(?)', [$id]);
		$data['medication'] = DB::select('CALL getMedicationByAppointment(?)', [$id]);
		return response()->json($data, 200);
	}

    public function store(AppointmentValidate $request): JsonResponse {
        $validated      = $request->validated();
        $id             = $request->input('appointmentId');
        $diagnostics    = $request->input('diagnostic_id');
        $drugs          = $request->input('drug_id');
        $descriptions   = $request->input('description');
        // Si no se proporciona un ID, crear nuevo registro
        DB::beginTransaction();
        try {
            $appointment    = empty($id) ? Appointment::create($validated) : Appointment::updateOrCreate(['id' => $id], $validated);
            $id 	        = $appointment->id;
            $dni 	        = $appointment->dni;
			$format 		= '';
            // Guardar diagnóstico, medicación y subir imagen si existen
            if ($diagnostics) 	$this->saveDiagnostic($id, $dni, $diagnostics);
            if ($drugs) 		$this->saveMedication($id, $dni, $drugs, $descriptions);

            DB::commit();
            return response()->json([
                'status' 	=> true,
                'type'		=> 'success',
                'messages' 	=> empty($id) ? 'Se ha añadido un nuevo examen' : 'Actualizado exitosamente',
                'route' 	=> route('hcl.appointments.see', $dni),
                'print_a5' 	=> route('hcl.appointments.print', [$id, 'a5']),
				'print_a4' 	=> route('hcl.appointments.print', [$id, 'a4']),
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

    protected function saveDiagnostic($id, $dni, $diagnosticId) {
        $data = collect($diagnosticId)->map(function ($diagnosticId) use ($id, $dni) {
			return [
				'id_control'    	=> $id,
				'dni' 				=> $dni,
				'id_diagnostico'	=> $diagnosticId,
				'created_at' 		=> now(),
			];
		})->toArray();

		DiagnosticAppointment::insert($data);
		return;
    }

    protected function saveMedication($id, $dni, $drugId, $description) {
        // Prepara los datos para la inserción
		$data = [];
		for ($i = 0; $i < count($drugId); $i++) {
			$data[] = [
				'id_control'    => $id,
				'dni'           => $dni,
				'id_droga' 		=> $drugId[$i],
				'descripcion'   => $description[$i],
				'created_at'	=> now(),
			];
		}
		// Inserta los datos en la base de datos
		MedicationAppointment::insert($data);
		return;
    }

    public function listAppointments(string $dni): JsonResponse {
		$results 	= DB::select('CALL getAppointmentsByMedicalHistory(?)', [$dni]);
		$data 		= collect($results)->map(function ($item, $index) {
			$user   	= auth()->user();
			$buttons 	= '';

			if($user->can('control_ver')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-info view-appointment btn-xs" value="%s"><i class="bi bi-eye"></i> Ver receta</button>&nbsp;',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }

			if($user->can('control_actualizar')){
                $buttons .= sprintf(
                    '<a type="button" class="btn btn-warning btn-xs" href="%s"><i class="bi bi-pencil-square"></i> Editar</a>&nbsp;',
                    route('hcl.appointments.edit', ['id' => $item->id]),
                );
            }

			if($user->can('control_borrar')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-danger delete-appointment btn-xs" value="%s"><i class="bi bi-trash"></i> Eliminar</button>',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
			return [
				$index + 1,
                $item->created_at,
                $item->dni,
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

	public function listAppointmentsByDNI(string $dni): JsonResponse {
        $results    = DB::select('CALL getAppointmentsByDNI(?)', [$dni]);
        $data       = collect($results)->map(function ($item, $index) {
            return [
                $index + 1,
                $item->created_at,
                sprintf(
                    '<button type="button" class="btn btn-info view-appointment btn-xs" value="%s"><i class="bi bi-eye"></i> Ver receta</button>',
                    $item->id,
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

    public function listOfDiagnosticsByAppointmentId(int $id): JsonResponse {
		$results    	= DB::select('CALL getDiagnosticByAppointment(?)', [$id]);
		$data       	= collect($results)->map(function ($item, $index) {
			$user 		= auth()->user();
			$buttons 	= '';
			if($user->can('control_borrar')){
				$buttons .= sprintf(
					'<button type="button" class="btn btn-danger delete-diagnostic btn-xs" value="%s"><i class="bi bi-trash"></i> Eliminar</button>',
					$item->id,
				);
			}
			return [
				$index + 1,
				$item->diagnostic,
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
	
	public function listOfMedicationByAppointmentId(int $id): JsonResponse {
		$results 		= DB::select('CALL getMedicationByAppointment(?)', [$id]);
		$data 			= collect($results)->map(function ($item, $index) {
			$user 		= auth()->user();
			$buttons 	= '';
			if($user->can('control_borrar')){
				$buttons .= sprintf(
					'<button type="button" class="btn btn-danger delete-medication btn-xs" value="%s"><i class="bi bi-trash"></i> Eliminar</button>',
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

	public function seeAppointmentsByMedicalHistory(string $dni): JsonResponse {
		$results['exams'] = DB::select('CALL getExamsByMedicalHistory(?)', [$dni]);
		return response()->json($results, 200);
	}

	public function seeRecipeByAppointment(int $id): JsonResponse {
		$data['app'] 		= Appointment::findOrFail($id);
		$data['medicacion'] = MedicationAppointment::where('id_control', $id)->get();
		$data['diagnostic'] = DiagnosticAppointment::where('id_control', $id)->get();
		return response()->json($data, 200);
	}

    public function destroy(int $id): JsonResponse {
        $result = Appointment::findOrFail($id);
        $result->delete();
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? 'El control fue eliminado' : 'Recargue la página, algo salió mal',
        ], 200);
    }

    public function destroyDiagnosticAppointment(int $id): JsonResponse {
        $result = DiagnosticAppointment::findOrFail($id);
        $result->delete();
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? 'El diagnóstico fue eliminado' : 'Recargue la página, algo salió mal',
        ], 200);
    }

    public function destroyMedicationAppointment(int $id): JsonResponse {
        $result = MedicationAppointment::findOrFail($id);
        $result->delete();
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? 'El medicamento fue eliminado' : 'Recargue la página, algo salió mal',
        ], 200);
    }

	public function printPrescriptionId(int $id, string $format = 'a5') {
        // Validar formato
        if (!in_array($format, ['a4', 'a5'])) {
            $format = 'a5';
        }

        // Obtener datos
        $hc = DB::select('CALL getMedicalHistoryByAppointment(?)', [$id]);
        $ap = Appointment::findOrFail($id);
        $dx = DB::select('CALL getDiagnosticbyAppointment(?)', [$id]);
        $mx = DB::select('CALL getMedicationByAppointment(?)', [$id]);
        $us = Auth::user();
        $en = Enterprise::findOrFail(1);

        // Configurar PDF según formato
        if ($format === 'a4') {
			$pdf = PDF::loadView('hcl.appointments.pdf-a4', compact('hc', 'ap', 'dx', 'mx', 'us', 'en', 'format'));
            $pdf->setPaper('a4', 'portrait')
                ->setOptions([
                    'margin_top' 	        => 10,
                    'margin_bottom'         => 10,
                    'margin_left' 	        => 15,
                    'margin_right' 	        => 15,
                ]);
        } else {
			$pdf = PDF::loadView('hcl.appointments.pdf-a5', compact('hc', 'ap', 'dx', 'mx', 'us', 'en', 'format'));
            $pdf->setPaper('a5', 'portrait')
                ->setOptions([
                    'margin_top' 			=> 0.5,
                    'margin_bottom' 		=> 0.5,
                    'margin_left' 			=> 0.5,
                    'margin_right' 			=> 0.5,
                ]);
        }

        $filename = "receta-medica-{$id}-" . strtoupper($format) . ".pdf";
        return $pdf->stream($filename);
    }
}