<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ImageResource extends JsonResource {

    public function toArray(Request $request): array {
        return [
            'id'            => $this->id,
            'id_examen'     => $this->id_examen,
            'dni'           => $this->dni,
            'fecha_examen'  => $this->fecha_examen,
            'imagen'        => Storage::url($this->imagen),
            'estado'        => $this->estado,
            'created_at'    => $this->created_at->format('Y-m-d'),
            'updated_at'    => $this->updated_at->format('Y-m-d'),
        ];
    }
}
