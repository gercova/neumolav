<?php

namespace App\Http\Controllers\hcl;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportValidate;
use App\Models\DiagnosticReport;
use App\Models\Enterprise;
use App\Models\History;
use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller {

    public function __construct() {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:informe_acceder')->only('index');
		$this->middleware('permission:informe_ver')->only('seeReports', 'listReports', 'listOfDiagnosticsByReportId');
		$this->middleware('permission:informe_crear')->only('add', 'store');
		$this->middleware('permission:informe_actualizar')->only('edit', 'store');
		$this->middleware('permission:informe_borrar')->only('destroy', 'destroyDiagnosticReport');
    }

    public function index(): View {
        return view('hcl.reports.index');
    }

    public function add($dni): View {
        $hc = History::where('dni', $dni)->get();
        return view('hcl.reports.add', compact('hc'));
    }

    public function edit($id): View {
        $hc	= Report::seePatientByReport($id);
		$rp = Report::findOrFail($id);
        return view('hcl.reports.edit', compact('hc', 'rp'));
    }

    public function seeReports($dni): View {
		$hc = History::where('dni', $dni)->get();
		return view('hcl.reports.see', compact('hc'));
	}

    public function viewReportDetail($id): JsonResponse {
		$rp = Report::findOrFail($id);
		$hc	= Report::seePatientByReport($id);
		$dx = DB::select('CALL getDiagnosticByReport(?)', [$id]);
		return response()->json(compact('rp', 'hc', 'dx'), 200);
	}

    public function store(ReportValidate $request): JsonResponse {
        $validated      = $request->validated();
        $id             = $request->input('reportId');
        $diagnostics    = $request->input('diagnostic_id');
        // Si no se proporciona un ID, crear nuevo registro
        DB::beginTransaction();
        try {
            $report = Report::updateOrCreate(['id' => $id], $validated);
            $id 	= $report->id;
            $dni    = $report->dni;
            // Guardar diagnóstico, medicación y subir imagen si existen
            if ($diagnostics) $this->saveDiagnosis($id, $dni, $diagnostics);

            DB::commit();
            return response()->json([
                'status' 		=> true,
                'type'			=> 'success',
                'messages' 		=> empty($id) ? 'Se ha añadido un nuevo reporte' : 'Actualizado exitosamente',
                'route' 		=> route('hcl.reports.see', $dni),
                'route_print' 	=> route('hcl.reports.print', $id)
            ], 200);
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
				'id_informe'        => $id,
				'dni' 			    => $dni,
				'id_diagnostico'	=> $diagnosticId,
                'created_at'        => now()
			];
		})->toArray();

		DiagnosticReport::insert($data);
		return;
    }

    public function listReports($dni): JsonResponse {
		$results 	    = DB::select('CALL getReportsByMedicalHistory(?)', [$dni]);
		$data 		    = collect($results)->map(function ($item, $index) {
            $user   	= auth()->user();
            $buttons 	= '';
            if($user->can('informe_ver')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-info view-report btn-xs" value="%s"><i class="bi bi-eye"></i> Ver reporte</button>&nbsp;',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }
            if($user->can('informe_actualizar')){
                $buttons .= sprintf(
                    '<a class="btn btn-warning btn-xs" href="%s"><i class="bi bi-pencil-square"></i> Editar</a>&nbsp;',
                    route('hcl.reports.edit', ['id' => $item->id]),
                );
            }
            if($user->can('informe_borrar')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-danger delete-report btn-xs" value="%s"><i class="bi bi-trash"></i> Eliminar</button>&nbsp;',
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

    public function listReportsByDNI($dni): JsonResponse {
        $results    = DB::select('CALL getReportsByDNI(?)', [$dni]);
        $data       = collect($results)->map(function ($item, $index) {
            return [
                $index + 1,
                $item->created_at,
                sprintf(
                    '<button type="button" class="btn btn-info view-report btn-xs" value="%s"><i class="bi bi-eye"></i> Ver reporte</button>',
                    $item->id,
                )
            ];
        });
        $results = [
            "sEcho"					=> 1,
            "iTotalRecords"			=> $data->count(),
            "iTotalDisplayRecords"	=> $data->count(),
            "aaData"				=> $data ?? [],
        ];
        return response()->json($results, 200);
    }

    public function listOfDiagnosticsByReportId(int $id): JsonResponse {
		$results = DB::select('CALL getDiagnosticByReport(?)', [$id]);
		$data = collect($results)->map(function ($item, $index) {
			return [
				$index + 1,
				$item->diagnostic,
				sprintf(
					'<button type="button" class="btn btn-danger delete-diagnostic btn-xs" value="%s"><i class="bi bi-trash"></i> Eliminar</button>',
					$item->id,
				)
			];
		});

		$results = [
			"sEcho"					=> 1,
			"iTotalRecords"			=> $data->count(),
			"iTotalDisplayRecords"	=> $data->count(),
			"aaData"				=> $data ?? [],
		];
		return response()->json($results, 200);
	}

    public function destroy($id): JsonResponse {
        $result = Report::findOrFail($id);
        $dni = $result->dni;
        $result->delete();
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? 'Se ha eliminado el informe' : 'Algo salió mal, recargue la página he intente de nuevo',
            'route' => route('hcl.reports.see', $dni),
        ], 200);
    }

    public function destroyDiagnosticReport(int $id): JsonResponse {
        $result = DiagnosticReport::findOrFail($id);
        $result->delete();
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? 'El diagnóstico fue eliminado' : 'Recargue la página, algo salió mal',
        ], 200);
    }

    public function printReportId(int $id) {
		$hc = DB::select('CALL getMedicalHistoryByReport(?)', [$id]);
        $dx = DB::select('CALL getDiagnosticByReport(?)', [$id]);
		$rk	= Report::findOrFail($id);
		$us = Auth::user();
		$en = Enterprise::findOrFail(1);
		$pdf = PDF::loadView('hcl.reports.pdf', compact('hc', 'dx', 'rk', 'us', 'en'))
			->setPaper('a4')
        	->setOptions([
				'margin-top' 	        => 0.5, 
				'margin-bottom'         => 0.5, 
				'margin-left' 	        => 0.5, 
				'margin-right' 	        => 0.5,
                'fontDefault'           => 'sans-serif',
                'isHtml5ParserEnabled'  => true,
                'isRemoteEnabled'       => false,
                'isPhpEnabled'          => false,
                'chroot'                => realpath(base_path()),
			]);
        return $pdf->stream("informe-neumológico-{$id}.pdf");
	}
}
