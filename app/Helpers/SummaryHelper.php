<?php

if (!function_exists('generateMocSummary')) {
    /**
     * Genera un resumen MOC (Minimum Operational Content) del texto dado
     * 
     * @param string $content Contenido completo
     * @param int $maxLength Longitud máxima del resumen
     * @return string Resumen generado
     */
    function generateMocSummary(string $content, int $maxLength = 200): string {
        // Limpiar el contenido de etiquetas HTML y espacios extra
        $cleanContent = strip_tags($content);
        $cleanContent = trim(preg_replace('/\s+/', ' ', $cleanContent));
        
        // Si el contenido es más corto que el máximo, devolverlo completo
        if (strlen($cleanContent) <= $maxLength) {
            return $cleanContent;
        }
        
        // Cortar hasta el último espacio antes del límite
        $summary = substr($cleanContent, 0, $maxLength);
        $lastSpace = strrpos($summary, ' ');
        
        if ($lastSpace !== false) {
            $summary = substr($summary, 0, $lastSpace);
        }
        
        return $summary . '...';
    }
    /*function generateMocSummary(string $content, int $maxLength = 150): string {
        $cleanContent = strip_tags($content);
        $cleanContent = trim(preg_replace('/\s+/', ' ', $cleanContent));
        
        // Buscar el primer punto cercano al límite
        $pos = strpos($cleanContent, '.', $maxLength - 50);
        
        if ($pos !== false && $pos < $maxLength + 50) {
            return substr($cleanContent, 0, $pos + 1);
        }
        
        // Si no encuentra punto, cortar en la última palabra
        if (strlen($cleanContent) <= $maxLength) {
            return $cleanContent;
        }
        
        $summary = substr($cleanContent, 0, $maxLength);
        $lastSpace = strrpos($summary, ' ');
        
        return $lastSpace ? substr($summary, 0, $lastSpace) . '...' : $summary . '...';
    }*/
}