# High-Performance SerpApi PHP Client

A modern, **Async-First**, PSR-compliant PHP client for [SerpApi](https://serpapi.com/ "null").

This library is a complete re-architecture of the legacy SDK, designed for high-throughput applications. It leverages **PHP 8.2+ features** and **Guzzle Promises** to execute multiple search requests concurrently, reducing total execution time from linear ($O(n)$) to the time of the slowest request ($O(1)$ approx).

## 🚀 Key Features

-   **⚡ True Concurrency:** Run 10, 50, or 100 searches simultaneously using `searchBatch()`.
    
-   **🛡️ PHP 8.2 Native:** Built with `readonly` properties, strict types, and modern syntax for maximum safety and performance.
    
-   **🔌 Connection Pooling:** Optimized cURL configuration (`CURLOPT_MAXCONNECTS`) to handle massive batch processing without resource exhaustion.
    
-   **🧠 Smart Error Handling:** Batch operations are resilient. A failure in one request (e.g., network timeout) does **not** crash the entire batch.
    
-   **💎 Generic Architecture:** One client (`SerpApiClient`) supports Google, Bing, eBay, YouTube, and all other SerpApi engines.
    

## 🛠️ Installation

### Via Composer

Add the repository to your `composer.json` (until published to Packagist):

```
"repositories": [
    {
        "type": "vcs",
        "url": "[https://github.com/josueisaacelias/serpapi-php-client](https://github.com/josueisaacelias/serpapi-php-client)"
    }
]
```

Then require the package:

```
composer require josueisaacelias/serpapi-php-client
```

**Requirements:**

-   PHP ^8.2
    
-   `ext-curl` and `ext-json`
    

## ⚡ Quick Start

### 1\. Initialize the Client

Use the new Named Arguments syntax for clarity.

```
require 'vendor/autoload.php';

use SerpApi\SerpApiClient;

$client = new SerpApiClient(apiKey: "YOUR_SECRET_API_KEY");
```

### 2\. Synchronous Search (Standard)

For simple, single requests. This blocks execution until the response arrives.

```
try {
    $results = $client->search([
        'engine'   => 'google', 
        'q'        => 'Coffee',
        'location' => 'Austin, Texas'
    ]);

    echo $results['organic_results'][0]['title'] ?? 'No results found';

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### 3\. Asynchronous Batch Search (High Performance)

This is the power feature. Execute multiple searches in parallel. The client handles the complexity of Promises and non-blocking I/O internally.

```
// Define unique IDs for your queries to easily track results
$queries = [
    'coffee_usa' => ['q' => 'Coffee', 'location' => 'Austin, Texas'],
    'tea_uk'     => ['q' => 'Tea',    'location' => 'London, UK'],
    'tacos_mx'   => ['q' => 'Tacos',  'location' => 'Mexico City', 'hl' => 'es'],
];

// Common parameters for all queries (merged automatically)
$defaults = ['engine' => 'google', 'num' => 10];

// 🚀 Executes all 3 requests at the same time
$batchResults = $client->searchBatch($queries, $defaults);

// Process results
foreach ($batchResults as $id => $data) {
    if (isset($data['error'])) {
        echo "❌ Query [$id] failed: " . $data['error'] . "\n";
    } else {
        echo "✅ Query [$id] returned " . count($data['organic_results']) . " results.\n";
    }
}
```

## 📚 API Reference

### `search(array $params): array`

Executes a single standard GET request to `/search`.

### `searchBatch(array $queries, array $defaults = []): array`

Executes multiple requests concurrently.

-   **$queries:** Associative array where keys are custom IDs and values are query parameters.
    
-   **$defaults:** Parameters applied to every query (e.g., `['engine' => 'google']`). Specific query params override defaults.
    
-   **Returns:** An array keyed by your custom IDs containing either the result data or an `['error' => '...']` array.
    

### `getArchive(string $searchId): array`

Retrieves a specific search from the SerpApi archive using its ID.

### `getLocations(array $params): array`

Retrieves supported locations for geo-targeting.

### `getAccount(): array`

Retrieves account information (plan limit, usage, etc.).

## 🧪 Testing

This project uses **PHPUnit 11+** with modern Attributes (`#[Test]`).

To run the test suite (using Mocks, so **no API credits are consumed**):

```
./vendor/bin/phpunit
```

## 📜 License

This project is licensed under the MIT License. 
