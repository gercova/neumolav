<?php

namespace App\Http\Controllers\hcl;

use App\Http\Controllers\Controller;
use App\Http\Requests\RiskValidate;
use App\Models\Enterprise;
use App\Models\History;
use App\Models\Risk;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RisksController extends Controller {
    
    public function __construct() {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:riesgo_acceder')->only('index');
		$this->middleware('permission:riesgo_ver')->only('seeRisks', 'viewRiskDetail', 'listRisks', 'listRisksByDNI', 'printRiskReportId');
		$this->middleware('permission:riesgo_crear')->only('add', 'store');
		$this->middleware('permission:riesgo_actualizar')->only('edit', 'store');
		$this->middleware('permission:riesgo_borrar')->only('destroy');
    }

    public function index(): View {
        return view('hcl.risks.index');
    }

    public function add($dni): View {
        $hc = History::where('dni', $dni)->get();
        return view('hcl.risks.add', compact('hc'));
    }

    public function edit(int $id): View {
        $hc	= Risk::seePatientByRisk($id);
		$rk = Risk::findOrFail($id);
        return view('hcl.risks.edit', compact('hc', 'rk'));
    }

    public function seeRisks($dni): View {
		$hc = History::where('dni', $dni)->get();
		return view('hcl.risks.see', compact('hc'));
	}

    public function viewRiskDetail(int $id): JsonResponse {
		$rk = Risk::findOrFail($id);
		$hc = Risk::seePatientByRisk($id);
		return response()->json(compact('rk', 'hc'), 200);
	}

    public function store(RiskValidate $request): JsonResponse {
        $validated  = $request->validated();
        $id         = $request->input('riskId');
        // Si no se proporciona un ID, crear nuevo registro
        DB::beginTransaction();
        try {
            $report = Risk::updateOrCreate(['id' => $id], $validated);
            $id 	= $report->id;
            $dni    = $report->dni;
            DB::commit();
            return response()->json([
                'status' 		=> true,
                'type'			=> 'success',
                'messages' 		=> $report->wasChanged() ? 'Se ha añadido un nuevo reporte' : 'Reporte actualizado exitosamente',
                'route' 		=> route('hcl.risks.see', $dni),
                'route_print' 	=> route('hcl.risks.print', $id)
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

    public function listRisks($dni): JsonResponse {
		$results 	= DB::select('CALL getRisksByMedicalHistory(?)', [$dni]);
		$data       = collect($results)->map(function ($item, $index) {
            $user = auth()->user();
            $buttons = '';
            if($user->can('riesgo_ver')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-info view-risk btn-xs" value="%s"><i class="bi bi-eye"></i> Ver informe</button>&nbsp;',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }

            if($user->can('riesgo_actualizar')){
                $buttons .= sprintf(
                    '<a class="btn btn-warning btn-xs" href="%s"><i class="bi bi-pencil-square"></i> Editar</a>&nbsp;',
                    route('hcl.risks.edit', ['id' => $item->id]),
                );
            }
            
            if($user->can('riesgo_borrar')){
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-danger delete-risk btn-xs" value="%s"><i class="bi bi-trash"></i> Eliminar</button>',
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

    public function listRisksByDNI($dni): JsonResponse {
        $results    = DB::select('CALL getRisksByDNI(?)', [$dni]);
        $data       = collect($results)->map(function ($item, $index) {
            return [
                $index + 1,
                $item->created_at,
                sprintf(
                    '<button type="button" class="btn btn-info view-risk btn-xs" value="%s"><i class="bi bi-eye"></i> Ver informe</button>',
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

    public function destroy(int $id): JsonResponse {
        $result = Risk::findOrFail($id);
        $result->delete();
        return response()->json([
            'status'    => (bool) $result,
            'type'      => $result ? 'success' : 'error',
            'messages'  => $result ? 'Se ha eliminado el informe' : 'Algo salió mal, recargue la página he intente de nuevo',
        ], 200);
    }

    public function printRiskReportId(int $id) {
		$hc = DB::select('CALL getMedicalHistoryByRisk(?)', [$id]);
		$rk	= Risk::findOrFail($id);
		$us = Auth::user();
        $en = Enterprise::findOrFail(1);
		$pdf = PDF::loadView('hcl.risks.pdf', compact('hc', 'rk', 'us', 'en'))
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
        return $pdf->stream("informe-de-riesgo-{$id}.pdf");
	}
}