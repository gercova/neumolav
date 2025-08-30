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

class StatisticsController extends Controller {
    
    public function __construct() {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:dashboard')->only('index');
    }

    public function index(): View {
        $yr = $this->years();
        return view('hcl.statistics.index', compact('yr'));
    }

    public function getCountRows(): JsonResponse{
        $fechaActual    = now()->toDateString();
        $result['hc']   = History::whereNull('deleted_at')->count();
        $result['ex']   = Exam::whereNull('deleted_at')->count();
        $result['ap']   = Appointment::whereNull('deleted_at')->count();
        $result['cd']   = History::whereNull('deleted_at')->whereDate('created_at', $fechaActual)->count();
        return response()->json($result, 200);
    }

    public function years(){
        return History::selectRaw('YEAR(created_at) year')->groupBy('year')->orderBy('year', 'desc')->get();
    }

    public function getHistoriesByYear($year): JsonResponse {
        $histories = History::selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyData = array_fill(1, 12, 0);

        foreach ($histories as $history) {
            $monthlyData[(int)$history->month] = $history->count;
        }
        
        $formattedData = [
            'months' => range(1, 12),
            'counts' => array_values($monthlyData)
        ];

        return response()->json($formattedData);
    }

    public function getExamsByYear($year): JsonResponse {
        $exams = Exam::selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyData = array_fill(1, 12, 0);

        foreach ($exams as $ex) {
            $monthlyData[(int)$ex->month] = $ex->count;
        }

        $formattedData = [
            'months' => range(1, 12),
            'counts' => array_values($monthlyData)
        ];

        return response()->json($formattedData);
    }

    public function getAppointmentsByYear($year): JsonResponse {
        $appointments = Appointment::selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        $monthlyData = array_fill(1, 12, 0);

        foreach ($appointments as $ap) {
            $monthlyData[(int)$ap->month] = $ap->count;
        }

        $formattedData = [
            'months' => range(1, 12),
            'counts' => array_values($monthlyData)
        ];

        return response()->json($formattedData);
    }

    public function getDiagnosticsByExam(){
        return DiagnosticExam::selectRaw('d.descripcion diagnostico, COUNT(examen_diagnostico.id_diagnostico) cantidad')
            ->join('diagnosticos as d', 'd.id', '=', 'examen_diagnostico.id_diagnostico')
            ->whereNull('examen_diagnostico.deleted_at')
            ->groupBy('diagnostico')
            ->having('cantidad', '>', '0')
            ->orderBy('cantidad', 'desc')
            ->limit(15)
            ->get();
    }

    public function getDrugsByExam(){
        return MedicationExam::selectRaw('d.descripcion droga, COUNT(*) cantidad')
            ->join('drogas as d', 'd.id', '=', 'examen_medicacion.id_droga')
            ->whereNull('examen_medicacion.deleted_at')
            ->groupBy('droga')
            ->having('cantidad', '>', '0')
            ->orderBy('cantidad', 'desc')
            ->limit(15)
            ->get();
    }

    public function getHistoriesBySex(){
        return History::selectRaw('s.descripcion sexo, COUNT(historias.id_sexo) cantidad')
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

    public function HCByMonth(Request $request){
        $histories = History::where(function ($q) use ($request) {
            if($request->filled('start_date')) $q->where('created_at', '>=', request('start_date'));
            if($request->filled('end_date')) $q->where('created_at', '<=', request('end_date'));
        })
        ->selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as count')
        ->whereYear('created_at', $request->year)
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
}
