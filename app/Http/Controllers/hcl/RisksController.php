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

    public function add(History $hc): View {
        return view('hcl.risks.add', compact('hc'));
    }

    public function edit(Risk $rk): View {
        $hc	= History::where('id', $rk->id)->first();
        return view('hcl.risks.edit', compact('hc', 'rk'));
    }

    public function see(History $hc): View {
		$hc = History::where('id', $hc->id)->first();
		return view('hcl.risks.see', compact('hc'));
	}

    public function viewRiskDetail(Risk $rk): JsonResponse {
		$hc = History::where('id', $rk->id_historia)->first();
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
            DB::commit();
            return response()->json([
                'status' 		=> true,
                'type'			=> 'success',
                'messages' 		=> $report->wasChanged() ? 'Se ha añadido un nuevo reporte' : 'Reporte actualizado exitosamente',
                'route' 		=> route('hcl.risks.see', $report->id_historia),
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

    public function listRisks(History $hc): JsonResponse {
		$results 	= DB::select('CALL PA_getRisksByMedicalHistory(?)', [$hc->id]);
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
                    route('hcl.risks.edit', ['rk' => $item->id]),
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

    public function listRisksByDNI(History $hc): JsonResponse {
        $results    = DB::select('CALL PA_getRisksByDNI(?)', [$hc->id]);
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

    public function destroy(Risk $rk): JsonResponse {
        $rk->delete();
        return response()->json([
            'status'    => (bool) $rk,
            'type'      => $rk ? 'success' : 'error',
            'messages'  => $rk ? 'Se ha eliminado el informe' : 'Algo salió mal, recargue la página he intente de nuevo',
        ], 200);
    }

    public function printRiskReport(Risk $rk) {
		$hc = DB::select('CALL PA_getMedicalHistoryByRisk(?)', [$rk->id]);
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
        return $pdf->stream("informe-de-riesgo-{$rk->id}.pdf");
	}
}
