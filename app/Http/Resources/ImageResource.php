<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource {

    public function toArray(Request $request): array {
        return [
            'id'            => $this->id,
            'id_examen'     => $this->id_examen,
            'dni'           => $this->dni,
            'fecha_examen'  => $this->fecha_examen->format('Y-m-d'),
            'imagen'        => $this->imagen,
            'estado'        => $this->estado,
            'created_at'    => $this->created_at->format('Y-m-d'),
            'updated_at'    => $this->updated_at->format('Y-m-d'),
        ];
    }
}
