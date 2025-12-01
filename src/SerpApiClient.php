<?php

declare(strict_types=1);

namespace SerpApi;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

/**
 * SerpApi Client - A generic wrapper for the SerpApi REST interface.
 */
class SerpApiClient
{
    private string $apiKey;
    private GuzzleClient $httpClient;
    private const BASE_URI = 'https://serpapi.com';

    /**
     * @param string $apiKey Your private API Key.
     * @param GuzzleClient|null $httpClient Optional: Inject a custom client for testing.
     */
    public function __construct(string $apiKey, ?GuzzleClient $httpClient = null)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient ?? new GuzzleClient([
            'base_uri' => self::BASE_URI,
            'timeout'  => 30.0, // Damos tiempo suficiente para búsquedas pesadas
        ]);
    }

    /**
     * Core method to execute searches.
     * Maps to: GET /search
     *
     * @param array $params Query parameters (e.g., ['engine' => 'google', 'q' => 'coffee'])
     * @return array Decoded JSON response
     */
    public function search(array $params): array
    {
        return $this->get('/search', $params);
    }

    /**
     * Retrieve a specific search from the archive.
     * Maps to: GET /searches/{search_id}.json
     */
    public function getArchive(string $searchId): array
    {
        return $this->get("/searches/{$searchId}.json", []);
    }

    /**
     * Get locations map.
     * Maps to: GET /locations.json
     */
    public function getLocations(array $params): array
    {
        // Ejemplo: ['q' => 'Austin', 'limit' => 5]
        return $this->get('/locations.json', $params);
    }

    /**
     * Get account information.
     * Maps to: GET /account
     */
    public function getAccount(): array
    {
        return $this->get('/account', []);
    }

    /**
     * Internal generic request handler.
     */
    protected function get(string $endpoint, array $query): array
    {
        // Inyectamos la API Key automáticamente si el usuario no la puso en el array
        if (!isset($query['api_key'])) {
            $query['api_key'] = $this->apiKey;
        }

        // Forzamos salida JSON y source PHP para estadísticas internas de SerpApi
        $query['output'] = 'json';
        $query['source'] = 'php_modern_client';

        try {
            $response = $this->httpClient->request('GET', $endpoint, [
                'query' => $query
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Error decoding JSON: " . json_last_error_msg());
            }

            // Manejo de errores que vienen DENTRO del JSON (Status 200 pero con error lógico)
            if (isset($data['error'])) {
                throw new \Exception("SerpApi Error: " . $data['error']);
            }

            return $data;

        } catch (GuzzleException $e) {
            throw new \Exception("HTTP Request failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }
} 
