<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TableViewService {

    protected $tableC = 'controles'; // Define tu tabla por defecto aquí

    public function generateTableView(?string $table, string $dni) {
        if (empty($table)) {
            $query = DB::table($this->tableC)
                ->selectRaw('DATE(created_at) as fecha) ,dni, id')
                ->where(['dni' => $dni, 'deleted_at' => NULL])
                ->get();
        } else {
            $query = $this->changeTable($table, $dni);
        }

        return $this->generateHtmlResponse($table, $query);
    }

    protected function changeTable(string $table, string $dni) {
        // Implementa tu lógica para cambiar de tabla aquí
        // Ejemplo básico:
        return DB::table($table)
            ->select(DB::raw('DATE(created_at) as fecha'), 'dni', 'id')
            ->where(['dni' => $dni, 'deleted_at' => NULL])
            ->get();
    }

    protected function generateHtmlResponse(?string $table, $query) {
        if (is_null($table)) {
            return response()->json([
                'html' => view('components.alert', [
                    'type' => 'warning',
                    'message' => 'SELECCIONE TABLA :-)'
                ])->render()
            ]);
        }

        if ($query->isEmpty()) {
            return response()->json([
                'html' => view('components.alert', [
                    'type' => 'danger',
                    'message' => 'NO HAY DATOS :('
                ])->render()
            ]);
        }

        return response()->json([
            'html' => view('components.table-view', [
                'rows' => $query,
                'table' => $table
            ])->render()
        ]);
    }
}
