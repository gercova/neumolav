<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait para estandarizar las respuestas JSON de la API interna.
 *
 * Formato de salida:
 *   {
 *     "status":   true|false,
 *     "messages": "Texto legible",
 *     "data":     mixed
 *   }
 *
 * Convención alineada con las respuestas existentes de CitasController.
 */
trait ApiResponse
{
    /**
     * Respuesta exitosa.
     */
    protected function success(
        mixed $data         = null,
        string $messages    = 'OK',
        int $code           = 200
    ): JsonResponse {
        return response()->json([
            'status'   => true,
            'messages' => $messages,
            'data'     => $data,
        ], $code);
    }

    /**
     * Respuesta de error genérica.
     */
    protected function error(
        string $messages = 'Error',
        int $code = 400,
        mixed $errors = null
    ): JsonResponse {
        return response()->json([
            'status'   => false,
            'messages' => $messages,
            'errors'   => $errors,
        ], $code);
    }

    /**
     * Error de validación (422).
     */
    protected function validationError(mixed $errors, string $messages = 'Datos inválidos'): JsonResponse {
        return $this->error($messages, 422, $errors);
    }

    /**
     * Recurso no encontrado (404).
     */
    protected function notFound(string $messages = 'Recurso no encontrado'): JsonResponse {
        return $this->error($messages, 404);
    }

    /**
     * Acceso denegado (403).
     */
    protected function forbidden(string $messages = 'Acceso denegado'): JsonResponse {
        return $this->error($messages, 403);
    }

    /**
     * Error interno del servidor (500).
     */
    protected function serverError(string $messages = 'Error interno del servidor'): JsonResponse {
        return $this->error($messages, 500);
    }
}