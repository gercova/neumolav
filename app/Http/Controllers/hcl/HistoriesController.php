<?php

namespace App\Http\Controllers\hcl;

use App\Http\Controllers\Controller;
use App\Http\Requests\HistoryValidate;
use App\Models\Appointment;
use App\Models\Exam;
use App\Models\BloodGroups;
use App\Models\DegreesInstruction;
use App\Models\DocumentType;
use App\Models\History;
use App\Models\MaritalStatus;
use App\Models\Sex;
use App\Models\Smoking;
use App\Models\TipoAtencion;
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
        $tiposAtencion = TipoAtencion::all();
        return view('hcl.histories.add', compact('dt', 'sx', 'bg', 'di', 'ms', 'tb', 'tiposAtencion'));
    }

	public function edit(History $history): View {
		$dt 			= DocumentType::where('id', '!=', 2)->get();
		$sx				= Sex::get();
        $bg 			= BloodGroups::get();
        $di 			= DegreesInstruction::get();
        $ms 			= MaritalStatus::get();
        $tb 			= Smoking::get();
        $tiposAtencion  = TipoAtencion::all();
		$occupation 	= History::getOccupationByHistoryId($history->id);
		$unacimiento 	= History::getUBirthByHistoryId($history->id);
		$uresidencia 	= History::getUResidenceByHistoryId($history->id);
        return view('hcl.histories.edit', compact('dt', 'sx', 'bg', 'di', 'ms', 'tb', 'tiposAtencion', 'history', 'occupation', 'unacimiento', 'uresidencia'));
    }

	public function store(HistoryValidate $request): JsonResponse {
		$validated = $request->validated();

		$processedFields = [
			'nombres' 			=> strtoupper($validated['nombres']),
			'ubigeo_nacimiento' => !empty($request->input('ubigeo_nacimiento')) ? ($this->getStringId($request->input('ubigeo_nacimiento'))) : '220901',
			'ubigeo_residencia' => $this->getStringId($validated['ubigeo_residencia']),
			'id_ocupacion' 		=> $this->getStringId($validated['id_ocupacion']),
			'ubigeo_extranjero' => $request->input('extranjero') ?: ($request->input('ubigeo_extranjero') ?: null),
			'transfusiones'     => $request->input('transfusiones'),
			'id_ct' 			=> !empty($request->input('id_ct')) ? $request->input('id_ct') : 4,
			'id_tipo_atencion'  => !empty($request->input('id_tipo_atencion')) ? (int) $request->input('id_tipo_atencion') : 1,
		];

		$data = array_merge($validated, $processedFields);
		unset($data['extranjero']);

		DB::beginTransaction();
        try {
			$historyId 	= $request->input('id');
			$result 	= History::updateOrCreate(['id' => $historyId], $data);
			$isNew 		= $result->wasRecentlyCreated;
			$today      = Carbon::today()->format('Y-m-d');
			$tipoAtencion = !empty($data['id_tipo_atencion']) ? (int)$data['id_tipo_atencion'] : ($isNew ? 1 : 2);

			// Verificar si ya existe cita activa para el paciente hoy
			$existingCita = DB::table('citas')
				->where('id_historia', $result->id)
				->whereDate('fecha_cita', $today)
				->whereNotIn('id_estado', [3]) // no cancelada
				->whereNull('deleted_at')
				->first();

			if ($existingCita) {
				// Reutilizar cita del día actualizando tipo de atención
				DB::table('citas')
					->where('id', $existingCita->id)
					->update([
						'id_tipo_atencion' => $tipoAtencion,
						'updated_at'       => Carbon::now(),
					]);
			} else {
				// Encolar nueva cita con turno secuencial del día
				$maxTurno = DB::table('citas')
					->whereDate('fecha_cita', $today)
					->whereNull('deleted_at')
					->max('numero_turno');

				DB::table('citas')->insert([
					'id_historia'      => $result->id,
					'id_tipo_atencion' => $tipoAtencion,
					'fecha_cita'       => $today,
					'numero_turno'     => ($maxTurno ?? 0) + 1,
					'motivo'           => $isNew ? 'Nueva historia clínica registrada' : 'Atención registrada en historia clínica',
					'id_estado'        => 1, // Pendiente
					'created_at'       => Carbon::now(),
					'updated_at'       => Carbon::now(),
				]);
			}

			DB::commit();
			return response()->json([
				'status'    => (bool) $result,
				'type'      => $result ? 'success' : 'error',
				'messages'  => $result ? ($isNew ? 'Nueva historia clínica registrada y añadida a la cola de hoy' : 'Historia clínica actualizada y agendada para hoy') : 'Error al guardar, recargue la página e intente de nuevo',
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

	public function getStringId(string $obj){
		if (empty($obj)) return '';
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
		$today = Carbon::today()->format('Y-m-d');
		$results = DB::table('citas')
			->join('historias', 'citas.id_historia', '=', 'historias.id')
			->join('estado_cita', 'citas.id_estado', '=', 'estado_cita.id')
			->leftJoin('tipos_atencion', 'citas.id_tipo_atencion', '=', 'tipos_atencion.id')
			->select([
				'historias.dni',
				'historias.nombres',
				'citas.fecha_cita',
				'citas.hora_cita',
				'citas.created_at',
				'citas.numero_turno',
				'citas.id',
				'citas.id_tipo_atencion',
				'tipos_atencion.descripcion as tipo_atencion_desc',
				'tipos_atencion.badge_color',
				'historias.id as hid'
			])
			->whereDate('citas.fecha_cita', $today)
			->whereNull('citas.deleted_at')
			->where('citas.id_estado', 1)
			->orderBy(DB::raw('COALESCE(citas.numero_turno, 999999)'), 'asc')
			->orderBy('citas.created_at', 'asc')
			->get();

		$data = $results->map(function ($item, $index) {
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
				htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8'),
            	htmlspecialchars(route('hcl.exams.add', 		['hc' => $item->hid]), ENT_QUOTES, 'UTF-8'),
            	htmlspecialchars(route('hcl.reports.add', 		['hc' => $item->hid]), ENT_QUOTES, 'UTF-8'),
            	htmlspecialchars(route('hcl.risks.add', 		['hc' => $item->hid]), ENT_QUOTES, 'UTF-8'),
				htmlspecialchars(route('hcl.histories.edit', 	['history' => $item->hid]), ENT_QUOTES, 'UTF-8')
			);

			// Badge coloreado según tipo de atención:
			// 1: Nuevo (verde), 2: Control (azul), 3: Continuador (amarillo)
			$badgeClass = match((int)($item->id_tipo_atencion ?? 1)) {
				1 => 'badge-success',
				2 => 'badge-primary',
				3 => 'badge-warning text-dark',
				default => 'badge-secondary'
			};
			$badgeText = $item->tipo_atencion_desc ?: match((int)($item->id_tipo_atencion ?? 1)) {
				1 => 'Nuevo',
				2 => 'Control',
				3 => 'Continuador',
				default => 'Nuevo'
			};
			$badgeHtml = sprintf(
				'<span class="badge %s ml-2 font-weight-normal py-1 px-2"><i class="bi %s mr-1"></i>%s</span>',
				$badgeClass,
				(int)($item->id_tipo_atencion ?? 1) === 1 ? 'bi-person-plus-fill' : ((int)($item->id_tipo_atencion ?? 1) === 2 ? 'bi-arrow-repeat' : 'bi-person-check-fill'),
				htmlspecialchars($badgeText, ENT_QUOTES, 'UTF-8')
			);
			$nombresConBadge = htmlspecialchars($item->nombres, ENT_QUOTES, 'UTF-8') . ' ' . $badgeHtml;

			return [
				$index + 1,
				$item->dni,
				$nombresConBadge,
				$item->hora_cita ? Carbon::parse($item->hora_cita)->format('h:i A') : Carbon::parse($item->created_at)->format('Y-m-d H:i'),
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

	public function addQuotes(History $hc, Request $request): JsonResponse {
		$fecha = Carbon::today()->format('Y-m-d');
		$tipoAtencion = (int) $request->input('id_tipo_atencion', 2);

		// Validar si ya existe cita hoy (sin contar canceladas)
		$existingCita = DB::table('citas')
			->where('id_historia', $hc->id)
			->whereNotIn('id_estado', [3])
			->whereDate('citas.fecha_cita', $fecha)
			->whereNull('deleted_at')
			->first();

		if ($existingCita) {
			// Reutilizar cita existente actualizando tipo de atención
			DB::table('citas')
				->where('id', $existingCita->id)
				->update([
					'id_tipo_atencion' => $tipoAtencion,
					'updated_at'       => Carbon::now(),
				]);

			$response = [
				'status' 	=> true,
				'type' 		=> 'success',
				'messages' 	=> 'Paciente ya se encontraba en la agenda. Se actualizó su cita para el día de hoy.',
			];
		} else {
			try {
				$maxTurno = DB::table('citas')
					->whereDate('fecha_cita', $fecha)
					->whereNull('deleted_at')
					->max('numero_turno');

				DB::table('citas')->insert([
					'id_historia' 	    => $hc->id,
					'id_tipo_atencion'  => $tipoAtencion,
					'fecha_cita'        => $fecha,
					'numero_turno'      => ($maxTurno ?? 0) + 1,
					'motivo'            => 'Añadido desde historias',
					'id_estado'         => 1,
					'created_at' 	    => Carbon::now(),
					'updated_at' 	    => Carbon::now(),
				]);

				$response = [
					'status' 	=> true,
					'type' 		=> 'success',
					'messages' 	=> 'Paciente añadido a la agenda de hoy',
				];
			} catch (\Exception $e) {
				$response = [
					'status' 	=> false,
					'type' 		=> 'error',
					'messages' 	=> 'Algo salió mal, intente de nuevo',
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
			'messages'	=> $hc ? 'Se ha eliminado la historia clínica' : 'Algo salió mal, recargue la página e intente de nuevo',
		], 200);
	}

    /**
     * Vista rápida de la historia clínica y controles previos del paciente
     * Sin restricción de fechas (soporta pacientes inactivos hasta 5+ años y soft-deleted)
     */
    public function quickView(int|string $id): JsonResponse {
        $history = History::withTrashed()
            ->with(['documentType', 'sex', 'bloodGroup', 'smoking', 'tipoAtencion'])
            ->find($id);

        if (!$history) {
            return response()->json([
                'status'  => false,
                'message' => 'Historia clínica no encontrada.'
            ], 404);
        }

        // Obtener controles clínicos previos sin límite de antigüedad (5+ años) y con soporte para soft-deletes
        // Se excluyen controles con estado=0 (inactivos/borrador)
        $appointments = Appointment::where('id_historia', $history->id)
            ->with(['diagnostics.diagnostic'])
            ->where('estado', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener exámenes activos del paciente (estado=1)
        $exams = Exam::where('id_historia', $history->id)
            ->where('estado', 1)
            ->whereNull('deleted_at')
            ->with(['diagnostics.diagnostic', 'type'])
            ->orderBy('created_at', 'desc')
            ->get();

        $appointmentsData = $appointments->map(function ($item, $index) {
            $diagnosticsList = $item->diagnostics ? $item->diagnostics->map(function ($d) {
                return $d->diagnostic ? $d->diagnostic->descripcion : null;
            })->filter()->values() : collect();
            
            $diagnosticText = $diagnosticsList->isNotEmpty() 
                ? $diagnosticsList->implode(', ') 
                : ($item->diagnostico ?: 'Sin diagnóstico registrado');

            $diffForHumans = $item->created_at ? Carbon::parse($item->created_at)->diffForHumans() : '';

            return [
                'index'             => $index + 1,
                'id'                => $item->id,
                'id_historia'       => $item->id_historia,
                'fecha_formato'     => $item->created_at ? Carbon::parse($item->created_at)->format('d/m/Y h:i A') : '--',
                'fecha_corta'       => $item->created_at ? Carbon::parse($item->created_at)->format('d/m/Y') : '--',
                'antiguedad'        => $diffForHumans,
                'sintomas'          => $item->sintomas ?: 'No especificado',
                'diagnostico'       => $diagnosticText,
                'diagnostics_array' => $diagnosticsList,
                'plan'              => $item->plan ?: '',
                'tratamiento'       => $item->tratamiento ?: '',
                'recomendaciones'   => $item->recomendaciones ?: '',
                'is_deleted'        => !is_null($item->deleted_at),
                'estado'            => (bool) $item->estado,
                'print_a4'          => route('hcl.appointments.print', ['ap' => $item->id, 'format' => 'a4']),
                'print_a5'          => route('hcl.appointments.print', ['ap' => $item->id, 'format' => 'a5']),
            ];
        });

        // Tipo de atención
        $tipoAtencionId = (int) ($history->id_tipo_atencion ?? 1);
        $tipoAtencionColor = match($tipoAtencionId) {
            1 => 'badge-success',
            2 => 'badge-primary',
            3 => 'badge-warning text-dark',
            default => 'badge-secondary'
        };
        $tipoAtencionDesc = $history->tipoAtencion ? $history->tipoAtencion->descripcion : match($tipoAtencionId) {
            1 => 'Nuevo',
            2 => 'Control',
            3 => 'Continuador',
            default => 'Nuevo'
        };

        // Resumen clínico / antecedentes
        $antecedentes = [
            'asma'               => $history->asmabronquial,
            'epoc'               => $history->epoc,
            'epid'               => $history->epid,
            'tuberculosis'       => $history->tuberculosis,
            'cancerpulmon'       => $history->cancerpulmon,
            'efusionpleural'     => $history->efusionpleural,
            'neumonias'          => $history->neumonias,
            'tabaquismo'         => $history->smoking ? $history->smoking->descripcion : ($history->tabaquismo ?: 'No'),
            'ipa'                => $history->result ?: ($history->aniosfum && $history->cig ? round(($history->cig * $history->aniosfum) / 20, 2) : null),
            'contactotbc'        => $history->contactotbc,
            'biomasa'            => $history->exposicionbiomasa,
            'cirugias'           => $history->cirugias,
            'transfusiones'      => $history->transfusiones,
            'traumatismos'       => $history->traumatismos,
            'hospitalizaciones'  => $history->hospitalizaciones,
            'alergias_drogas'    => $history->drogas,
            'medicacion_habitual'=> $history->medicacion,
            'otros'              => $history->otros,
            'motivoconsulta'     => $history->motivoconsulta,
            'relatocronologico'  => $history->relatocronologico,
        ];

        // Mapear exámenes
        $examsData = $exams->map(function ($item, $index) {
            $diagnosticsList = $item->diagnostics ? $item->diagnostics->map(function ($d) {
                return $d->diagnostic ? $d->diagnostic->descripcion : null;
            })->filter()->values() : collect();

            $diagnosticText = $diagnosticsList->isNotEmpty()
                ? $diagnosticsList->implode(', ')
                : ($item->diagnostico ?? 'Sin diagnóstico registrado');

            return [
                'index'         => $index + 1,
                'id'            => $item->id,
                'id_historia'   => $item->id_historia,
                'fecha_formato' => $item->created_at ? Carbon::parse($item->created_at)->format('d/m/Y h:i A') : '--',
                'tipo'          => $item->type ? $item->type->descripcion : 'Examen',
                'diagnostico'   => $diagnosticText,
                'plan'          => $item->plan ?: '',
                'otros'         => $item->otros ?: '',
                'print_a4'      => route('hcl.exams.print', ['ex' => $item->id, 'format' => 'a4']),
                'print_a5'      => route('hcl.exams.print', ['ex' => $item->id, 'format' => 'a5']),
                'edit_url'      => route('hcl.exams.edit', ['ex' => $item->id]),
            ];
        });

        return response()->json([
            'status'  => true,
            'history' => [
                'id'                  => $history->id,
                'dni'                 => $history->dni,
                'nombres'             => strtoupper($history->nombres),
                'fecha_nacimiento'    => $history->fecha_nacimiento ? Carbon::parse($history->fecha_nacimiento)->format('d/m/Y') : '--',
                'fecha_nacimiento_raw'=> $history->fecha_nacimiento ? Carbon::parse($history->fecha_nacimiento)->format('Y-m-d') : null,
                'edad'                => $history->calculated_age ?? ($history->fecha_nacimiento ? Carbon::parse($history->fecha_nacimiento)->age : null),
                'sexo'                => $history->sex ? $history->sex->descripcion : '--',
                'telefono'            => $history->telefono ?: '--',
                'email'               => $history->email ?: '--',
                'tipo_documento'      => $history->documentType ? $history->documentType->descripcion : 'DNI',
                'grupo_sanguineo'     => $history->bloodGroup ? $history->bloodGroup->descripcion : '--',
                'ocupacion'           => $history->ocupacion ?: '--',
                'lugar_residencia'    => $history->lugar_residencia ?: '--',
                'id_tipo_atencion'    => $tipoAtencionId,
                'tipo_atencion_desc'  => $tipoAtencionDesc,
                'tipo_atencion_color' => $tipoAtencionColor,
                'is_deleted'          => !is_null($history->deleted_at),
                'estado'              => (bool) $history->estado,
                'antecedentes'        => $antecedentes,
            ],
            'appointments_count' => $appointmentsData->count(),
            'appointments'       => $appointmentsData,
            'exams_count'        => $examsData->count(),
            'exams'              => $examsData,
            'routes' => [
                'history_edit' => route('hcl.histories.edit', ['history' => $history->id]),
                'control_add'  => route('hcl.appointments.add', ['hc' => $history->id]),
                'exam_add'     => route('hcl.exams.add', ['hc' => $history->id]),
                'report_add'   => route('hcl.reports.add', ['hc' => $history->id]),
            ]
        ], 200);
    }
}
