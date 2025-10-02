<?php

namespace App\Http\Controllers\hcl;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DiagnosticExam;
use App\Models\Exam;
use App\Models\History;
use App\Models\MedicationExam;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StatisticsController extends Controller {
    
    public function __construct() {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:dashboard')->only('index');
    }

    public function index(): View {
        $yr = $this->years();
        return view('hcl.statistics.index', compact('yr'));
    }

    public function years(){
        return History::selectRaw('YEAR(created_at) as year')->groupBy('year')->orderBy('year', 'desc')->get();
    }

    public function getCountRows(): JsonResponse {
        $fechaActual    = now()->toDateString();
        $hc             = History::whereNull('deleted_at')->count();
        $ex             = Exam::whereNull('deleted_at')->count();
        $ap             = Appointment::whereNull('deleted_at')->count();
        $cd             = History::whereNull('deleted_at')->whereDate('created_at', $fechaActual)->count();
        return response()->json(compact('hc', 'ex', 'ap', 'cd'), 200);
    }

    public function getMonthlyCountsByYear($year, $model, $name) {
        if (!class_exists($model)) {
            throw new InvalidArgumentException("El modelo '{$model}' no existe.");
        }
        
        $records = $model::query()
            ->selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        // Inicializamos array de 12 meses con ceros
        $data = array_fill(0, 12, 0);
        // Asignamos los conteos reales a los meses correspondientes
        foreach ($records as $record) {
            $data[$record->month - 1] = $record->count;
        }
        // Retornamos en el formato solicitado: array de objetos con name y data
        return [
            [
                'name' => $name,
                'data' => $data
            ]
        ];
    }

    public function getAnnualData($year) {
        $histories      = $this->getMonthlyCountsByYear($year, History::class, 'Historias');
        $exams          = $this->getMonthlyCountsByYear($year, Exam::class, 'Exámenes');
        $appointments   = $this->getMonthlyCountsByYear($year, Appointment::class, 'Citas');
        return array_merge($histories, $exams, $appointments);
    }

    /*public static function getPatientWithMonthOptimized($year) {
        return History::selectRaw('MONTH(fecha) as mes, COUNT(id) as cantidad')
            ->whereYear('fecha', $year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->mes => $item->cantidad];
            })
            ->pipe(function ($collection) {
                // Completar meses faltantes con 0
                return collect(range(1, 12))->map(function ($month) use ($collection) {
                    return [
                        'mes' => $month,
                        'cantidad' => $collection->get($month, 0)
                    ];
                });
            });
    }

    public function getHistoriesByYear($year) {
        $histories = History::query()
            ->selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data = [[
            'name' => 'Historias',
            'data' => array_fill(0, 12, 0) // Inicializa un array con 12 ceros (uno por cada mes)
        ]];

        foreach ($histories as $hc) {
            $data[0]['data'][$hc->month - 1] = $hc->count;
        }

        return $data;
    }

    public function getExamsByYear($year) {
        $exams = Exam::query()
            ->selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data = [[
            'name' => 'Exámenes',
            'data' => array_fill(0, 12, 0) // Inicializa un array con 12 ceros (uno por cada mes)
        ]];

        foreach ($exams as $ex) {
            $data[0]['data'][$ex->month - 1] = $ex->count;
        }

        return $data;
    }

    public function getAppointmentsByYear($year) {
        $appointments = Appointment::query()
            ->selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data = [[
            'name' => 'Citas',
            'data' => array_fill(0, 12, 0) // Inicializa un array con 12 ceros (uno por cada mes)
        ]];

        foreach ($appointments as $ap) {
            $data[0]['data'][$ap->month - 1] = $ap->count;
        }

        return $data;
    }*/

    public function getDiagnosticsByExam(){
        return DiagnosticExam::select('d.descripcion as diagnostico', DB::raw('COUNT(examen_diagnostico.id_diagnostico) as cantidad'))
            ->join('diagnosticos as d', 'd.id', '=', 'examen_diagnostico.id_diagnostico')
            ->whereNull('examen_diagnostico.deleted_at')
            ->groupBy('diagnostico')
            ->having('cantidad', '>', '0')
            ->orderBy('cantidad', 'desc')
            ->limit(15)
            ->get();
    }

    public function getDrugsByExam(){
        return MedicationExam::select('d.descripcion as droga', DB::raw('COUNT(*) as cantidad'))
            ->join('drogas as d', 'd.id', '=', 'examen_medicacion.id_droga')
            ->whereNull('examen_medicacion.deleted_at')
            ->groupBy('droga')
            ->having('cantidad', '>', '0')
            ->orderBy('cantidad', 'desc')
            ->limit(15)
            ->get();
    }

    public function getHistoriesBySex(){
        return History::select('s.descripcion as sexo', DB::raw('COUNT(historias.id_sexo) as cantidad'))
            ->join('sexo as s', 's.id', '=', 'historias.id_sexo')
            ->groupBy('sexo')
            ->get();
    }

    public function getHistoriesByBloodingGroup(){
        return History::selectRaw('gs.descripcion grupo_sanguineo, COUNT(historias.id_gs) cantidad')
            ->join('grupo_sanguineo as gs', 'gs.id', '=', 'historias.id_gs')
            ->groupBy('grupo_sanguineo')
            ->orderBy('cantidad', 'desc')
            ->get();
    }

    public function getHistoriesByMaritalStatus(){
        return History::selectRaw('e.descripcion estado_civil, COUNT(historias.id_estado) cantidad')
            ->join('estado_civil as e', 'e.id', '=', 'historias.id_estado')
            ->groupBy('estado_civil')
            ->orderBy('cantidad', 'desc')
            ->get();
    }

    public function getHistoriesByDegreeIntruction(){
        return History::selectRaw('di.descripcion grado_instruccion, COUNT(historias.id_gi) cantidad')
            ->join('grado_instruccion as di', 'di.id', '=', 'historias.id_gi')
            ->groupBy('grado_instruccion')
            ->orderBy('cantidad', 'desc')
            ->get();
    }

    public function getHistoriesBySmoking(){
        return History::selectRaw('t.consumo tabaquismo, COUNT(historias.id_ct) cantidad')
            ->join('tabaquismo as t', 't.id', '=', 'historias.id_ct')
            ->groupBy('t.consumo')
            ->orderBy('cantidad', 'desc')
            ->get();
    }
}