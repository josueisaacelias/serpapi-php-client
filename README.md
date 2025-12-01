# Modern SerpApi PHP Client

![PHP Version](https://img.shields.io/badge/php-%5E7.4%20%7C%7C%20%5E8.0-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![Build Status](https://img.shields.io/badge/build-passing-brightgreen)

A modern, PSR-4 compliant PHP client for [SerpApi](https://serpapi.com).

This library provides a clean and robust wrapper around the SerpApi REST interface, allowing you to scrape results from **Google, Bing, Baidu, Yelp, Walmart, YouTube**, and many other engines using a single, unified client.

> **Note:** This is an unofficial, modernized refactor of the legacy SDK, featuring Guzzle for secure HTTP requests, PSR compliance, and a simplified generic architecture.

## 🚀 Features

* **Generic Architecture:** One client (`SerpApiClient`) for all search engines. No need to manage 20 different classes.
* **Security First:** Uses **Guzzle HTTP Client** with full SSL/TLS verification enabled by default.
* **Modern Standards:** PSR-4 Autoloading and PSR-12 Coding Standards.
* **Developer Friendly:** Throws meaningful Exceptions instead of silent failures.
* **Test Ready:** Designed with Dependency Injection to allow easy mocking in your unit tests.

## 🛠️ Installation

### Option A: Via Composer (Recommended)

Since this package is hosted on GitHub (not yet on Packagist), add the repository to your `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "[https://github.com/josueisaacelias/serpapi-php-client](https://github.com/josueisaacelias/serpapi-php-client)"
    }
]
```

Then run:

```bash
composer require josueisaacelias/serpapi-php-client
```

### Option B: For Development

Clone the repository and install dependencies:

```bash
git clone https://github.com/josueisaacelias/serpapi-php-client.git
cd serpapi-php-client
composer install
```


## ⚡ Quick Start

### 1. Initialize the Client

```php
require 'vendor/autoload.php';

use SerpApi\SerpApiClient;

// Initialize with your Private API Key
$client = new SerpApiClient("YOUR_SECRET_API_KEY");
```

### 2. Search (Any Engine)

This client maps the [SerpApi Documentation](https://serpapi.com/search-api) parameters 1:1. You simply pass the `engine` parameter to switch between Google, Yelp, Bing, etc.

**Example: Search Google for "Coffee"**

```php
try {
    $results = $client->search([
        'engine'   => 'google', 
        'q'        => 'Coffee',
        'location' => 'Austin, Texas'
    ]);

    // Access results directly as an array
    print_r($results['organic_results']);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

**Example: Search Yelp for "Pizza"**

```php
$results = $client->search([
    'engine'    => 'yelp',
    'find_desc' => 'Pizza',
    'find_loc'  => 'New York, NY'
]);
```

### 3. Get Account Information

Retrieve your current plan, credits left, and usage history.

```php
$account = $client->getAccount();

echo "Plan ID: " . $account['plan_id'] . "\n";
echo "Searches Left: " . $account['total_searches_left'] . "\n";
```

### 4. Location API

Get supported locations for geo-targeting.

```php
$locations = $client->getLocations([
    'q'     => 'Austin', 
    'limit' => 3
]);

print_r($locations);
```

### 5. Search Archive API

Retrieve a previous search result from the archive using its `search_id`.

```php
$searchId = "585069bdee19ad271e9bc072"; // Example ID
$archivedResult = $client->getArchive($searchId);
```

## 🧪 Testing

This library is built with testing in mind. It includes a PHPUnit test suite configuration.

To run the tests locally (using Mocks, so **no API credits are consumed**):

```bash
./vendor/bin/phpunit
```

## 📜 License

This project is licensed under the MIT License. 
