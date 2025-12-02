<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SerpApi\SerpApiClient;

// Paste your actual API Key here
$apiKey = "Paste your Api Key Here.";

try {
    echo "🚀 Starting Modern Tests (PHP 8.2+)...\n\n";

    // ✨ MODERNIZATION 1: Named Arguments
    // No need to guess argument order anymore. Self-documenting code.
    $client = new SerpApiClient(apiKey: $apiKey);

    // ---------------------------------------------------------
    // 1. SYNCHRONOUS TEST
    // ---------------------------------------------------------
    echo "--- 1. Single Search (Synchronous) ---\n";

    $results = $client->search([
        'engine'   => 'google',
        'q'        => 'Coffee',
        'location' => 'Dallas, Texas'
    ]);

    // ✨ MODERNIZATION 2: Null Safe Operator + Null Coalescing
    // Access deep nested arrays without fear of "Undefined index" errors
    $title = $results['organic_results'][0]['title'] ?? 'No Title Found';
    echo "✅ Result: $title\n\n";

    // ---------------------------------------------------------
    // 2. ASYNCHRONOUS TEST (BATCH)
    // ---------------------------------------------------------
    echo "--- 2. Batch Search (Concurrent) ---\n";

    $queries = [
        'cafe'  => ['q' => 'Coffee',  'location' => 'Chicago, IL'],
        'pizza' => ['q' => 'Pizza',   'location' => 'Detroit, MI'],
        'tacos' => ['q' => 'Tamales', 'location' => 'Mexico City', 'hl' => 'es'], // hl=es for Spanish
    ];

    $start = microtime(true);
    
    // Execute the async engine
    $batchResults = $client->searchBatch(
        queries: $queries, 
        defaults: ['engine' => 'google'] // Named arg for clarity
    );
    
    $duration = microtime(true) - $start;
    echo "⚡ Total time: " . number_format($duration, 2) . "s\n";

    // Iterate through results
    foreach ($batchResults as $id => $data) {
        // ✨ MODERNIZATION 3: MATCH Expression
        // Replaces complex if/else chains. Cleaner and more visual.
        $statusMessage = match (true) {
            isset($data['error']) => "❌ [$id] Error: " . $data['error'],
            isset($data['organic_results']) => "✅ [$id] Success: " . ($data['organic_results'][0]['title'] ?? 'N/A'),
            default => "⚠️ [$id] Unknown response format"
        };

        echo $statusMessage . "\n";
    }

} catch (Exception $e) {
    echo "🚨 Exception Caught: " . $e->getMessage() . "\n";
}
