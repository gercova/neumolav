<?php

namespace App\Http\Controllers\hcl;

use App\Http\Controllers\Controller;
use App\Http\Requests\HistoryValidate;
use App\Models\BloodGroups;
use App\Models\DegreesInstruction;
use App\Models\DocumentType;
use App\Models\History;
use App\Models\MaritalStatus;
use App\Models\Sex;
use App\Models\Smoking;
use App\Traits\AuditLogTrait;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HistoriesController extends Controller {

	use AuditLogTrait;
    public function __construct() {
        $this->middleware(['auth', 'prevent.back']);
		$this->middleware('permission:historia_acceder')->only('index');
		$this->middleware('permission:historia_ver')->only('list');
		$this->middleware('permission:historia_crear')->only('add', 'store');
		$this->middleware('permission:historia_actualizar')->only('edit', 'store');
		$this->middleware('permission:historia_borrar')->only('destroy');
    }

    public function index(): View {
        return view('hcl.histories.index');
    }

	public function list(Request $request): JsonResponse {
		$histories 	= new History();
		$startIndex = $request->input('jtStartIndex', 0);
		$pageSize 	= $request->input('jtPageSize', 10);
		$itemSearch = $request->input('search');
		// Obtener los datos filtrados
		list($data, $count) = $histories->getAllHistories($startIndex, $pageSize, $itemSearch);
		// Agregar permisos al resultado para el frontend
		$permissions = [
			'update' 	=> auth()->user()->can('historia_actualizar'), 	// actualizar una historia clínica
			'delete' 	=> auth()->user()->can('historia_borrar'), 		// borrar una historia clinica
			'add_exm' 	=> auth()->user()->can('examen_crear'), 		// añadir un nuevo examen
			'view_exm' 	=> auth()->user()->can('examen_ver'), 			// ver exámenes de un paciente
			'add_ctrl' 	=> auth()->user()->can('control_crear'), 		// añadir nuevo control
			'view_ctrl' => auth()->user()->can('control_ver'), 			// ver controles de un paciente
			'add_rpt'	=> auth()->user()->can('informe_ver'), 			// añadir nuevo reporte
			'view_rpt' 	=> auth()->user()->can('informe_ver'), 			// ver reportes de un paciente
			'add_rsk' 	=> auth()->user()->can('riesgo_crear'), 		// añadir nuevo informe de riesgo
			'view_rsk' 	=> auth()->user()->can('riesgo_ver'), 			// ver informes de riesgo de un paciente
		];

		$data = $data->map(function ($record) use ($permissions) {
			$record->Permissions = $permissions; // Agregar permisos al registro
			return $record;
		});

		$jTableResult = [
			'Result'            => 'OK',
			'Records'           => $data,
			'TotalRecordCount'  => $count,
		];

		return response()->json($jTableResult);
	}

    public function add(): View {
        $dt = DocumentType::where('id', '!=', 2)->get();
		$sx	= Sex::get();
        $bg = BloodGroups::get();
        $di = DegreesInstruction::get();
        $ms = MaritalStatus::get();
        $tb = Smoking::get();
        return view('hcl.histories.add', compact('dt', 'sx', 'bg', 'di', 'ms', 'tb'));
    }

	public function edit(History $history): View {
		$dt 			= DocumentType::where('id', '!=', 2)->get();
		$sx				= Sex::get();
        $bg 			= BloodGroups::get();
        $di 			= DegreesInstruction::get();
        $ms 			= MaritalStatus::get();
        $tb 			= Smoking::get();
		$occupation 	= History::getOccupationByHistoryId($history->id);
		$unacimiento 	= History::getUBirthByHistoryId($history->id);
		$uresidencia 	= History::getUResidenceByHistoryId($history->id);
        return view('hcl.histories.edit', compact('dt', 'sx', 'bg', 'di', 'ms', 'tb', 'history', 'occupation', 'unacimiento', 'uresidencia'));
    }

	public function store(HistoryValidate $request): JsonResponse {
		$validated = $request->validated();

		$processedFields = [
			'nombres' 			=> strtoupper($validated['nombres']),
			'ubigeo_nacimiento' => isset($request->ubigeo_nacimiento) ? ($this->getStringId($request->input('ubigeo_nacimiento'))) : '220901',
			'ubigeo_residencia' => $this->getStringId($validated['ubigeo_residencia']),
			'id_ocupacion' 		=> $this->getStringId($validated['id_ocupacion']),
		];

		$data = array_merge($validated, $processedFields);

		DB::beginTransaction();
        try {
			$result = History::updateOrCreate(['id' => $request->input('id')], $data);
			if(!$result->wasChanged()){
				DB::table('citas')->insert(['id_historia' => $result->id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
			}
			DB::commit();
			return response()->json([
				'status'    => (bool) $result,
				'type'      => $result ? 'success' : 'error',
				'messages'  => $result ? ($result->wasChanged() ? 'Historia clínica actualizada' : 'Nueva historia clínica registrada') : 'Error al guardar, recargue la página he intente de nuevo',
				'route'  	=> route('hcl.histories.home')
			]);
		} catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'type'      => 'error',
                'messages'  => $e->getMessage(),
            ], 500);
        }
	}

	public function searchDni(Request $request) {
		$dni = $request->input('dni');
		$token = 'sk_9811.oyi4O6HZhGzEXQKAyygNmMyfgbXbd4rW';

		try {
			$response = Http::withHeaders([
				'Authorization' => 'Bearer ' . $token,
				'Accept' => 'application/json',
			])->timeout(30)->get('https://api.decolecta.com/v1/reniec/dni?numero=' . $dni);

			return $response->body();

		} catch (\Exception $e) {
			return response()->json([
				'error' => 'Error en la consulta: ' . $e->getMessage()
			], 500);
		}
	}

	public function getStringId($obj){
		$value = explode(" | ", $obj);
		return $value[0];
	}

    public function searchLocation(Request $request): JsonResponse {
		$hc 		= new History();
		$results 	= $hc->getLocation($request->input('q'));
		return response()->json($results);
	}

	public function searchOccupation(Request $request): JsonResponse {
		$results 	= History::getOccupation($request->input('q'));
		return response()->json($results);
	}

	public function getQuotes(): JsonResponse {
		$results = DB::table('citas')
			->join('historias', 'citas.id_historia', '=', 'historias.id')
			->join('estado_cita', 'citas.id_estado', '=', 'estado_cita.id')
			->select([
				'historias.dni',
				'historias.nombres',
				'citas.created_at',
				'citas.id',
				'historias.id as hid'
				// Agregar más campos si es necesario
			])
			->whereDate('citas.created_at', Carbon::today())
			->where('citas.id_estado', 1)
			->orderBy('citas.created_at', 'desc') // Ordenar por fecha de creación
			->get();

		$data = $results->map(function ($item, $index) {
			$buttons = '';
			$buttons = sprintf(
				'<div class="btn-group">
					<button type="button" class="btn btn-sm btn-warning changeStatus btn-md" value="%s">
						<i class="bi bi-check-square"></i> Cambiar estado de cita
					</button>&nbsp;
					<div class="btn-group">
						<button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown">Acciones&nbsp;</button>
						<div class="dropdown-menu">
                            <a class="dropdown-item" href="%s">Nuevo examen</a>
                            <a class="dropdown-item" href="%s">Nuevo Informe</a>
							<a class="dropdown-item" href="%s">Nuevos Riesgo</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="%s">Editar Historia</a>
						</div>
					</div>
				</div>',
				htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8'),  // Para el botón update
            	htmlspecialchars(route('hcl.exams.add', 		['hc' => $item->dni]), ENT_QUOTES, 'UTF-8'), // Para examen
            	htmlspecialchars(route('hcl.reports.add', 		['hc' => $item->dni]), ENT_QUOTES, 'UTF-8'), // Para informe
            	htmlspecialchars(route('hcl.risks.add', 		['hc' => $item->dni]), ENT_QUOTES, 'UTF-8'), // Para riesgo
				htmlspecialchars(route('hcl.histories.edit', 	['history' => $item->hid]), ENT_QUOTES, 'UTF-8') // Para editar historia
			);

			return [
				$index + 1,
				$item->dni,
				$item->nombres,
				Carbon::parse($item->created_at)->format('Y-m-d H:i:s'), // Solución aplicada
				$buttons
			];
		});

		return response()->json([
			"sEcho" 				=> 1,
			"iTotalRecords" 		=> $data->count(),
			"iTotalDisplayRecords" 	=> $data->count(),
			"aaData" 				=> $data,
		]);
	}

	public function addQuotes(Request $request, History $hc): JsonResponse {
		$fecha = Carbon::now()->format('Y-m-d');
		// Validar si ya existe cita hoy
		$validate = DB::table('citas')->where('id_historia', $hc->id)->where('id_estado', 1)->whereDate('created_at', $fecha)->count();

		if ($validate > 0) {
			$response = [
				'status' 	=> false,
				'type' 		=> 'error',
				'messages' 	=> 'El paciente ya se encuentra agendado para el día de hoy.',
			];
		} else {
			try {
				DB::table('citas')->insert([
					'id_historia' 	=> $hc->id,
					'created_at' 	=> Carbon::now(),
					'updated_at' 	=> Carbon::now()
				]);

				$response = [
					'status' 	=> true,
					'type' 		=> 'success',
					'messages' 	=> 'Paciente añadido a la agenda de hoy'
				];
			} catch (\Exception $e) {
				$response = [
					'status' 	=> false,
					'type' 		=> 'error',
					'messages' 	=> 'Algo salió mal, intente de nuevo'
				];
			}
		}

		return response()->json($response, 200);
	}

	public function checkStatusPatient(Request $request, int $id): JsonResponse {
		$result = DB::table('citas')->where('id', $id)->update(['id_estado' => '2']);

		return response()->json([
			'status' 	=> (bool) $result,
			'type'		=> $result ? 'success' : 'error',
			'messages' 	=> $result ? 'Actualizado exitosamente' : 'No se encontró el registro o no hubo cambios',
		]);
	}

	public function destroy(History $hc): JsonResponse {
		$hc->delete();
		return response()->json([
			'status' 	=> (bool) $hc,
			'type'		=> $hc ? 'success' : 'error',
			'messages'	=> $hc ? 'Se ha eliminado la historia clínica' : 'Algo salió mal, recargue la página he intente de nuevo',
		], 200);
	}
}
