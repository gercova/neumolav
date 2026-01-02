<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PresentationResource extends JsonResource {

    public function toArray(Request $request): array {
        return [
            'id'            => $this->id,
            'descripcion'   => $this->descripcion,
            'aka'           => $this->aka,
        ];
    }
}
