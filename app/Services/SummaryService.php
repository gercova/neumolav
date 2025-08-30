<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;

class SummaryService {

    public function generate(Request $request): ?string {
        // 1. Intenta con DeepSeek
        try {
            $deepseekResponse = Http::timeout(15)
                ->withHeaders(['Authorization' => 'Bearer ' . env('DEEPSEEK_API_KEY')])
                ->post('
                ', [
                    'text' => $request->contenido // Accede al campo desde el Request
                ]);

            if ($deepseekResponse->successful()) {
                return $deepseekResponse->json()['summary'];
            }
        } catch (\Exception $e) {
            Log::error("DeepSeek API Error: " . $e->getMessage());
        }

        // 2. Si DeepSeek falla, intenta con Kimi
        try {
            $kimiResponse = Http::timeout(15)
                ->withHeaders(['Authorization' => 'Bearer ' . env('KIMI_API_KEY')])
                ->post('https://api.kimi.ai/v1/summarize', [
                    'text' => $request->contenido // Accede al campo desde el Request
                ]);

            if ($kimiResponse->successful()) {
                return $kimiResponse->json()['summary'];
            }
        } catch (\Exception $e) {
            Log::error("Kimi API Error: " . $e->getMessage());
        }

        // 3. Si todo falla, devuelve null (sin mock)
        return null;
    }

    /*public function generate(Request $request)
    {
        $request->validate(['contenido' => 'required|string']);

        // 1. Intenta con DeepSeek
        $deepseekResponse = Http::timeout(15)
            ->withHeaders(['Authorization' => 'Bearer ' . env('DEEPSEEK_API_KEY')])
            ->post('https://api.deepseek.com/v1/summarize', [
                'text' => $request->contenido
            ]);

        if ($deepseekResponse->successful()) {
            return response()->json([
                'resumen' => $deepseekResponse->json()['summary']
            ]);
        }

        // 2. Si DeepSeek falla, intenta con Kimi
        $kimiResponse = Http::timeout(15)
            ->withHeaders(['Authorization' => 'Bearer ' . env('KIMI_API_KEY')])
            ->post('https://api.kimi.ai/v1/summarize', [
                'text' => $request->contenido
            ]);

        if ($kimiResponse->successful()) {
            return response()->json([
                'resumen' => $kimiResponse->json()['summary']
            ]);
        }

        // 3. Si ambas APIs fallan
        return response()->json([
            'error' => 'No se pudo generar el resumen. Las APIs no respondieron.'
        ], 500);
    }*/

    /*public function generate(Request $request) {
        $request->validate(['contenido' => 'required|string']);

        $resumen = Cache::remember(
            'resumen_' . md5($request->contenido),
            now()->addHours(1),
            function () use ($request) {
                // Intenta con DeepSeek primero
                $response = Http::timeout(15)
                    ->withHeaders(['Authorization' => 'Bearer ' . env('DEEPSEEK_API_KEY')])
                    ->post('https://api.deepseek.com/v1/summarize', [
                        'text' => $request->contenido
                    ]);

                if ($response->successful()) {
                    return $response->json()['summary'];
                }

                // Fallback a Kimi
                $response = Http::timeout(15)
                    ->withHeaders(['Authorization' => 'Bearer ' . env('KIMI_API_KEY')])
                    ->post('https://api.kimi.ai/v1/summarize', [
                        'text' => $request->contenido
                    ]);

                return $response->successful() ? $response->json()['summary'] : null;
            }
        );

        return $resumen 
            ? response()->json(['resumen' => $resumen])
            : response()->json(['error' => 'Las APIs no respondieron'], 500);
    }*/
    
    /*public function generateSummary(string $contenido): ?string {
        return Cache::remember(
            key: 'summary_' . md5($contenido),
            ttl: now()->addHours(1),
            callback: fn() => $this->callApisWithFallback($contenido)
        );
    }*/

    /*protected function callApisWithFallback(string $contenido): ?string {
        try {
            // 1. Intenta con DeepSeek
            $deepseekResponse = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('DEEPSEEK_API_KEY'),
                ])
                ->post('https://api.deepseek.com/v1/summarize', [
                    'text' => $contenido,
                ]);

            if ($deepseekResponse->successful()) {
                return $deepseekResponse->json()['summary'];
            }
        } catch (RequestException $e) {
            Log::error("DeepSeek API Error: " . $e->response?->body() ?? $e->getMessage());
        }

        try {
            // 2. Si DeepSeek falla, intenta con Kimi
            $kimiResponse = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('KIMI_API_KEY'),
                ])
                ->post('https://api.kimi.ai/v1/summarize', [
                    'text' => $contenido,
                ]);

            if ($kimiResponse->successful()) {
                return $kimiResponse->json()['summary'];
            }
        } catch (RequestException $e) {
            Log::error("Kimi API Error: " . $e->response?->body() ?? $e->getMessage());
        }

        // 3. Si ambas APIs fallan, devuelve null (no hay mock)
        return null;
    }*/

    /*protected function callApisWithFallback(string $contenido): ?string {
        try {
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => 'Bearer ' . env('DEEPSEEK_API_KEY'),])
                ->post('https://api.deepseek.com/v1/summarize', ['text' => $contenido, 'model' => 'deepseek-3.5']);

            return $response->json()['summary'] ?? null;

        } catch (RequestException $e) {
            Log::error("Error en DeepSeek: " . $e->getMessage());
            return null;
        }
    }*/
}