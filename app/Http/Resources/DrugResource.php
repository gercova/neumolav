<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DrugResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'id_categoria'      => $this->id_categoria,
            'id_presentacion'   => $this->id_presentacion,
            'descripcion'       => $this->descripcion,
            'detalle'           => $this->detalle,
            'estado'            => $this->estado,
            'created_at'        => $this->created_at->format('Y-m-d'),
            'updated_at'        => $this->updated_at->format('Y-m-d'),
        ];
    }
}
