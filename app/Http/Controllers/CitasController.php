<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuickPatientValidate;
use App\Models\AppointmentStatus;
use App\Models\Cita;
use App\Models\DocumentType;
use App\Models\History;
use App\Models\Sex;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CitasController extends Controller
{
    public function index(Request $request): View {
        $selectedDate  = $request->query('date', Carbon::today()->format('Y-m-d'));
        $documentTypes = DocumentType::where('id', '!=', 2)->get();
        $sexes         = Sex::get();
        $statuses      = AppointmentStatus::all();

        return view('citas.index', compact('selectedDate', 'documentTypes', 'sexes', 'statuses'));
    }

    public function list(Request $request): JsonResponse {
        $date   = $request->query('date', Carbon::today()->format('Y-m-d'));
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Cita::with(['history', 'status'])
            ->whereDate('fecha_cita', $date);

        if (!empty($status)) {
            $query->where('id_estado', $status);
        }

        if (!empty($search)) {
            $query->whereHas('history', function ($q) use ($search) {
                $q->where('dni', 'like', "%{$search}%")
                  ->orWhere('nombres', 'like', "%{$search}%");
            });
        }

        $appointments = $query->orderBy(DB::raw('COALESCE(numero_turno, 999999)'), 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $data = $appointments->map(function ($item, $index) {
            $patient = $item->history;
            $turnNumber = $item->numero_turno ?: ($index + 1);

            return [
                'id'           => $item->id,
                'turn_number'  => $turnNumber,
                'history_id'   => $patient ? $patient->id : null,
                'dni'          => $patient ? $patient->dni : '--',
                'nombres'      => $patient ? strtoupper($patient->nombres) : 'PACIENTE NO ENCONTRADO',
                'telefono'     => $patient ? ($patient->telefono ?: '--') : '--',
                'fecha_cita'   => Carbon::parse($item->fecha_cita)->format('Y-m-d'),
                'fecha_formato'=> Carbon::parse($item->fecha_cita)->format('d/m/Y'),
                'hora_cita'    => $item->hora_cita ? Carbon::parse($item->hora_cita)->format('h:i A') : 'Orden de llegada',
                'hora_raw'     => $item->hora_cita ? Carbon::parse($item->hora_cita)->format('H:i') : '',
                'motivo'       => $item->motivo ?: ($item->descripcion ?: 'Consulta general'),
                'status_id'    => $item->id_estado,
                'status_desc'  => $item->status ? $item->status->descripcion : 'DESCONOCIDO',
                'created_at'   => Carbon::parse($item->created_at)->format('d/m/Y H:i'),
                'routes'       => [
                    'exam_add'     => $patient ? route('hcl.exams.add', ['hc' => $patient->dni]) : '#',
                    'report_add'   => $patient ? route('hcl.reports.add', ['hc' => $patient->dni]) : '#',
                    'risk_add'     => $patient ? route('hcl.risks.add', ['hc' => $patient->dni]) : '#',
                    'history_edit' => $patient ? route('hcl.histories.edit', ['history' => $patient->id]) : '#',
                    'control_add'  => $patient ? route('hcl.appointments.add', ['hc' => $patient->dni]) : '#',
                ],
            ];
        });

        return response()->json([
            'status'     => true,
            'date'       => $date,
            'count'      => $data->count(),
            'data'       => $data,
        ]);
    }

    public function stats(Request $request): JsonResponse {
        $date = $request->query('date', Carbon::today()->format('Y-m-d'));

        $total       = Cita::whereDate('fecha_cita', $date)->count();
        $enEspera    = Cita::whereDate('fecha_cita', $date)->where('id_estado', 7)->count();
        $atendidos   = Cita::whereDate('fecha_cita', $date)->where('id_estado', 6)->count();
        $pendientes  = Cita::whereDate('fecha_cita', $date)->whereIn('id_estado', [1, 2])->count();
        $cancelados  = Cita::whereDate('fecha_cita', $date)->where('id_estado', 3)->count();

        return response()->json([
            'status'     => true,
            'date'       => $date,
            'total'      => $total,
            'en_espera'  => $enEspera,
            'atendidos'  => $atendidos,
            'pendientes' => $pendientes,
            'cancelados' => $cancelados,
        ]);
    }

    public function store(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'id_historia'   => 'required|exists:historias,id',
            'fecha_cita'    => 'required|date',
            'hora_cita'     => 'nullable',
            'motivo'        => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:500',
        ], [
            'id_historia.required' => 'Debe seleccionar un paciente.',
            'id_historia.exists'   => 'El paciente seleccionado no existe.',
            'fecha_cita.required'  => 'La fecha de la cita es obligatoria.',
            'fecha_cita.date'      => 'La fecha de la cita no es válida.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'type'     => 'error',
                'messages' => $validator->errors()->first(),
            ], 422);
        }

        $patientId = $request->id_historia;
        $fechaCita = Carbon::parse($request->fecha_cita)->format('Y-m-d');

        // Validation: Prevent patient from registering twice on the same day (excluding cancelled)
        $exists = Cita::where('id_historia', $patientId)
            ->whereDate('fecha_cita', $fechaCita)
            ->whereNotIn('id_estado', [3]) // 3 is CANCELADO
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            return response()->json([
                'status'   => false,
                'type'     => 'warning',
                'messages' => 'El paciente ya cuenta con una cita activa para el día ' . Carbon::parse($fechaCita)->format('d/m/Y') . '.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Next sequential turn for this date (FIFO)
            $maxTurno = Cita::whereDate('fecha_cita', $fechaCita)->max('numero_turno');
            $numeroTurno = ($maxTurno ?? 0) + 1;

            $cita = Cita::create([
                'id_historia'   => $patientId,
                'fecha_cita'    => $fechaCita,
                'hora_cita'     => $request->filled('hora_cita') ? $request->hora_cita : null,
                'numero_turno'  => $numeroTurno,
                'motivo'        => $request->motivo ?: 'Consulta general',
                'observaciones' => $request->observaciones,
                'id_estado'     => 1, // PENDIENTE
            ]);

            DB::commit();

            return response()->json([
                'status'       => true,
                'type'         => 'success',
                'messages'     => "Cita agendada exitosamente. Turno asignado: #{$numeroTurno}.",
                'numero_turno' => $numeroTurno,
                'data'         => $cita,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'   => false,
                'type'     => 'error',
                'messages' => 'Ocurrió un error al agendar la cita: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reschedule(Request $request, int $id): JsonResponse {
        $validator = Validator::make($request->all(), [
            'fecha_cita' => 'required|date',
            'hora_cita'  => 'nullable',
            'motivo'     => 'nullable|string|max:255',
        ], [
            'fecha_cita.required' => 'La nueva fecha es requerida.',
            'fecha_cita.date'     => 'La nueva fecha no es válida.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'type'     => 'error',
                'messages' => $validator->errors()->first(),
            ], 422);
        }

        $cita = Cita::find($id);
        if (!$cita) {
            return response()->json([
                'status'   => false,
                'type'     => 'error',
                'messages' => 'No se encontró la cita a reagendar.',
            ], 404);
        }

        $newDate = Carbon::parse($request->fecha_cita)->format('Y-m-d');

        // Validation: Verify patient doesn't already have another active appointment on that target date
        $exists = Cita::where('id_historia', $cita->id_historia)
            ->whereDate('fecha_cita', $newDate)
            ->where('id', '!=', $cita->id)
            ->whereNotIn('id_estado', [3]) // Cancelado
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            return response()->json([
                'status'   => false,
                'type'     => 'warning',
                'messages' => 'El paciente ya cuenta con otra cita activa en la fecha destino (' . Carbon::parse($newDate)->format('d/m/Y') . ').',
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Next sequential turn for the target date
            $maxTurno = Cita::whereDate('fecha_cita', $newDate)->max('numero_turno');
            $newTurno = ($maxTurno ?? 0) + 1;

            $cita->update([
                'fecha_cita'   => $newDate,
                'hora_cita'    => $request->filled('hora_cita') ? $request->hora_cita : null,
                'numero_turno' => $newTurno,
                'motivo'       => $request->motivo ?: $cita->motivo,
                'id_estado'    => 4, // REAGENDADO
            ]);

            DB::commit();

            return response()->json([
                'status'   => true,
                'type'     => 'success',
                'messages' => "Cita reagendada para el " . Carbon::parse($newDate)->format('d/m/Y') . ". Nuevo turno: #{$newTurno}.",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'   => false,
                'type'     => 'error',
                'messages' => 'Error al reagendar la cita: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Quick status update for an appointment.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_estado' => 'required|exists:estado_cita,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'type'     => 'error',
                'messages' => 'Estado no válido.',
            ], 422);
        }

        $cita = Cita::find($id);
        if (!$cita) {
            return response()->json([
                'status'   => false,
                'type'     => 'error',
                'messages' => 'Cita no encontrada.',
            ], 404);
        }

        $cita->update(['id_estado' => $request->id_estado]);

        $statusName = AppointmentStatus::where('id', $request->id_estado)->value('descripcion');

        return response()->json([
            'status'   => true,
            'type'     => 'success',
            'messages' => "Estado actualizado a '{$statusName}'.",
        ]);
    }

    public function destroy(int $id): JsonResponse {
        $cita = Cita::find($id);
        if (!$cita) {
            return response()->json([
                'status'   => false,
                'type'     => 'error',
                'messages' => 'Cita no encontrada.',
            ], 404);
        }

        $cita->delete();

        return response()->json([
            'status'   => true,
            'type'     => 'success',
            'messages' => 'Cita eliminada correctamente.',
        ]);
    }

    public function searchPatients(Request $request): JsonResponse {
        $q = trim($request->query('q', ''));
        if (empty($q) || strlen($q) < 2) {
            return response()->json([]);
        }

        $patients = History::select('id', 'dni', 'nombres', 'telefono', 'fecha_nacimiento', 'id_sexo')
            ->where(function ($query) use ($q) {
                $query->where('dni', 'like', "%{$q}%")
                    ->orWhere('nombres', 'like', "%{$q}%");
            })
            ->whereNull('deleted_at')
            ->orderBy('nombres', 'asc')
            ->limit(15)
            ->get();

        $results = $patients->map(function ($p) {
            $age = $p->fecha_nacimiento ? Carbon::parse($p->fecha_nacimiento)->age : null;
            return [
                'id'       => $p->id,
                'dni'      => $p->dni,
                'nombres'  => strtoupper($p->nombres),
                'telefono' => $p->telefono ?: '--',
                'edad'     => $age !== null ? "{$age} años" : 'Sin edad',
                'sexo'     => $p->id_sexo === 'M' ? 'Masc' : 'Fem',
                'label'    => "{$p->dni} - " . strtoupper($p->nombres),
            ];
        });

        return response()->json($results);
    }

    public function quickPatient(QuickPatientValidate $request): JsonResponse {
        $validated = $request->validated();

        if ($validated) {
            return response()->json([
                'status'   => false,
                'type'     => 'error',
                'messages' => $validated->errors()->first(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 1. Create Patient with safe defaults for required database fields
            $patient = History::create([
                'id_td'              => $request->id_td,
                'dni'                => trim($request->dni),
                'nombres'            => strtoupper(trim($request->nombres)),
                'fecha_nacimiento'   => $request->fecha_nacimiento,
                'id_sexo'            => $request->id_sexo,
                'telefono'           => trim($request->telefono),
                'email'              => $request->filled('email') ? trim($request->email) : null,
                'id_gs'              => 9,        // Sin datos
                'ubigeo_nacimiento'  => '220901', // Tarapoto default
                'ubigeo_residencia'  => '220901', // Tarapoto default
                'id_gi'              => 9,        // Sin grado
                'ocupacion'          => 'TRABAJADOR INDEPENDIENTE',
                'id_ocupacion'       => 2,        // Trabajador independiente
                'id_estado'          => 1,        // Soltero(a)
                'id_ct'              => 4,        // No fumador
                'estado'             => true,
            ]);

            // 2. Schedule Appointment immediately (FIFO turn)
            $fechaCita = Carbon::parse($request->fecha_cita)->format('Y-m-d');
            $maxTurno  = Cita::whereDate('fecha_cita', $fechaCita)->max('numero_turno');
            $numeroTurno = ($maxTurno ?? 0) + 1;

            $cita = Cita::create([
                'id_historia'   => $patient->id,
                'fecha_cita'    => $fechaCita,
                'hora_cita'     => $request->filled('hora_cita') ? $request->hora_cita : null,
                'numero_turno'  => $numeroTurno,
                'motivo'        => $request->motivo ?: 'Registro rápido y primera consulta',
                'id_estado'     => 1, // PENDIENTE
            ]);

            DB::commit();

            return response()->json([
                'status'       => true,
                'type'         => 'success',
                'messages'     => "Paciente registrado y agendado con éxito. Turno asignado: #{$numeroTurno}.",
                'patient_id'   => $patient->id,
                'numero_turno' => $numeroTurno,
                'cita'         => $cita,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'   => false,
                'type'     => 'error',
                'messages' => 'Error al registrar paciente y cita: ' . $e->getMessage(),
            ], 500);
        }
    }
}
