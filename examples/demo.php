<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SerpApi\SerpApiClient;

// Tu API KEY
$apiKey = "Paste yout Api Key Here.";

try {
    echo "🚀 Iniciando Pruebas Modernas (PHP 8.2+)...\n\n";

    // ✨ MODERNIZACIÓN 1: Named Arguments
    // Ya no necesitas adivinar qué es el primer argumento. Se lee como inglés.
    $client = new SerpApiClient(apiKey: $apiKey);

    // ---------------------------------------------------------
    // 1. PRUEBA SINCRÓNICA
    // ---------------------------------------------------------
    echo "--- 1. Búsqueda Single ---\n";

    $results = $client->search([
        'engine'   => 'google',
        'q'        => 'Coffee',
        'location' => 'Dallas, Texas'
    ]);

    // ✨ MODERNIZACIÓN 2: Null Safe Operator + Null Coalescing
    // Accedemos a arrays profundos sin miedo a "Undefined index"
    $title = $results['organic_results'][0]['title'] ?? 'Sin título';
    echo "✅ Resultado: $title\n\n";

    // ---------------------------------------------------------
    // 2. PRUEBA ASÍNCRONA (BATCH)
    // ---------------------------------------------------------
    echo "--- 2. Búsqueda Batch (Concurrente) ---\n";

    $queries = [
        'cafe'  => ['q' => 'Coffee', 'location' => 'Chicago, IL'],
        'pizza' => ['q' => 'Pizza',  'location' => 'Detroit, MI'],
        'tacos' => ['q' => 'Tamales',  'location' => 'Mexico City', 'hl' => 'es'], // hl=es para español
    ];

    $start = microtime(true);
    
    // Ejecutamos el motor
    $batchResults = $client->searchBatch(
        queries: $queries, 
        defaults: ['engine' => 'google'] // Named arg para claridad
    );
    
    $duration = microtime(true) - $start;
    echo "⚡ Tiempo total: " . number_format($duration, 2) . "s\n";

    // Iteramos resultados
    foreach ($batchResults as $id => $data) {
        // ✨ MODERNIZACIÓN 3: Expresión MATCH
        // Reemplaza a los if/else complejos. Es más limpio y visual.
        $statusMessage = match (true) {
            isset($data['error']) => "❌ [$id] Error: " . $data['error'],
            isset($data['organic_results']) => "✅ [$id] Éxito: " . ($data['organic_results'][0]['title'] ?? 'N/A'),
            default => "⚠️ [$id] Respuesta desconocida"
        };

        echo $statusMessage . "\n";
    }

} catch (Exception $e) {
    echo "🚨 Excepción Capturada: " . $e->getMessage() . "\n";
} 
