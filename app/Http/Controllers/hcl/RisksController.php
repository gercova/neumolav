<?php

namespace App\Http\Controllers\hcl;

use App\Http\Controllers\Controller;
use App\Http\Requests\RiskValidate;
use App\Models\Enterprise;
use App\Models\History;
use App\Models\Risk;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RisksController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:riesgo_acceder')->only('index', 'listHistories');
        $this->middleware('permission:riesgo_ver')->only('see', 'viewRiskDetail', 'listRisks', 'listRisksByDNI', 'printRiskReport');
        $this->middleware('permission:riesgo_crear')->only('add', 'store');
        $this->middleware('permission:riesgo_actualizar')->only('edit', 'store');
        $this->middleware('permission:riesgo_borrar')->only('destroy');
    }

    public function index(): View
    {
        return view('hcl.risks.index');
    }

    public function listHistories(Request $request): JsonResponse
    {
        $startIndex = $request->input('jtStartIndex', 0);
        $pageSize = $request->input('jtPageSize', 10);
        $search = $request->input('search');
        $query = DB::table('view_active_histories')
            ->when(! empty($search), function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('dni', 'LIKE', "%{$search}%")
                        ->orWhere('nombres', 'LIKE', "%{$search}%");
                });
            });

        $count = (clone $query)->count();
        $records = $query->offset($startIndex)->limit($pageSize)->get();
        $user = auth()->user();
        $permissions = [
            'view_risk' => $user->can('riesgo_ver'),
            'add_risk' => $user->can('riesgo_crear'),
            'view_riesgo' => $user->can('riesgo_ver'),
            'add_riesgo' => $user->can('riesgo_crear'),
            'riesgo_ver' => $user->can('riesgo_ver'),
            'riesgo_crear' => $user->can('riesgo_crear'),
        ];

        $records = $records->map(function ($row) use ($permissions) {
            $row->Permissions = $permissions;
            $row->see_url = route('hcl.risks.see', ['hc' => $row->id]);
            $row->add_url = route('hcl.risks.add', ['hc' => $row->id]);

            return $row;
        });

        return response()->json([
            'Result' => 'OK',
            'Records' => $records,
            'TotalRecordCount' => $count,
        ]);
    }

    public function add(History $hc): View
    {
        return view('hcl.risks.add', compact('hc'));
    }

    public function edit(Risk $rk): View
    {
        $hc = History::where('id', $rk->id_historia)->first();

        return view('hcl.risks.edit', compact('hc', 'rk'));
    }

    public function see(History $hc): View
    {
        $hc = History::where('id', $hc->id)->first();

        return view('hcl.risks.see', compact('hc'));
    }

    public function viewRiskDetail(Risk $rk): JsonResponse
    {
        $hc = History::where('id', $rk->id_historia)->first();

        return response()->json(compact('rk', 'hc'), 200);
    }

    public function store(RiskValidate $request): JsonResponse
    {
        $validated = $request->validated();
        $id = $request->input('riskId');
        DB::beginTransaction();
        try {
            $report = Risk::updateOrCreate(['id' => $id], $validated);
            $id = $report->id;
            DB::commit();

            return response()->json([
                'status' => true,
                'type' => 'success',
                'messages' => $report->wasRecentlyCreated ? 'Se ha añadido un nuevo reporte' : 'Reporte actualizado exitosamente',
                'route' => route('hcl.risks.see', $report->id_historia),
                'route_print' => route('hcl.risks.print', $id),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'type' => 'error',
                'messages' => $e->getMessage(),
            ], 500);
        }
    }

    public function listRisks(History $hc): JsonResponse
    {
        $results = DB::select('CALL PA_getRisksByMedicalHistory(?)', [$hc->id]);
        $data = collect($results)->map(function ($item, $index) {
            $user = auth()->user();
            $buttons = '';
            if ($user->can('riesgo_ver')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-info view-risk btn-xs" value="%s"><i class="bi bi-eye"></i> Ver informe</button>&nbsp;',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }

            if ($user->can('riesgo_actualizar')) {
                $buttons .= sprintf(
                    '<a class="btn btn-warning btn-xs" href="%s"><i class="bi bi-pencil-square"></i> Editar</a>&nbsp;',
                    route('hcl.risks.edit', ['rk' => $item->id]),
                );
            }

            if ($user->can('riesgo_borrar')) {
                $buttons .= sprintf(
                    '<button type="button" class="btn btn-danger delete-risk btn-xs" value="%s"><i class="bi bi-trash"></i> Eliminar</button>',
                    htmlspecialchars($item->id, ENT_QUOTES, 'UTF-8')
                );
            }

            return [
                $index + 1,
                $item->created_at,
                $item->dni,
                $buttons ?: '<span class="text-muted">No autorizado</span>',
            ];
        });

        return response()->json([
            'sEcho' => 1,
            'iTotalRecords' => $data->count(),
            'iTotalDisplayRecords' => $data->count(),
            'aaData' => $data ?? [],
        ], 200);
    }

    public function listRisksByDNI(History $hc): JsonResponse
    {
        $results = DB::select('CALL PA_getRisksByDNI(?)', [$hc->id]);
        $data = collect($results)->map(function ($item, $index) {
            return [
                $index + 1,
                $item->created_at,
                sprintf(
                    '<button type="button" class="btn btn-info view-risk btn-xs" value="%s"><i class="bi bi-eye"></i> Ver informe</button>',
                    $item->id,
                ),
            ];
        });

        return response()->json([
            'sEcho' => 1,
            'iTotalRecords' => $data->count(),
            'iTotalDisplayRecords' => $data->count(),
            'aaData' => $data ?? [],
        ], 200);
    }

    public function destroy(Risk $rk): JsonResponse
    {
        $rk->delete();

        return response()->json([
            'status' => (bool) $rk,
            'type' => $rk ? 'success' : 'error',
            'messages' => $rk ? 'Se ha eliminado el informe' : 'Algo salió mal, recargue la página he intente de nuevo',
        ], 200);
    }

    public function printRiskReport(Request $request, Risk $rk)
    {
        $hc = DB::select('CALL PA_getMedicalHistoryByRisk(?)', [$rk->id]);
        $us = Auth::user() ?? (! empty($rk->id_user) ? User::find($rk->id_user) : null);
        $en = Enterprise::findOrFail(1);
        $withSignature = $request->boolean('signature', false);
        $signatureBase64 = null;
        if ($withSignature && $us && $us->firma_digital && Storage::disk('public')->exists($us->firma_digital)) {
            $mime = Storage::disk('public')->mimeType($us->firma_digital) ?: 'image/png';
            $data = Storage::disk('public')->get($us->firma_digital);
            $signatureBase64 = 'data:'.$mime.';base64,'.base64_encode($data);
        }

        $pdf = PDF::loadView('hcl.risks.pdf', compact('hc', 'rk', 'us', 'en', 'withSignature', 'signatureBase64'))
            ->setPaper('a4')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-bottom' => 0.5,
                'margin-left' => 0.5,
                'margin-right' => 0.5,
                'fontDefault' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'isPhpEnabled' => false,
                'chroot' => realpath(base_path()),
            ]);

        return $pdf->stream("informe-de-riesgo-{$rk->id}.pdf");
    }
}
