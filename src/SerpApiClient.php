<?php

declare(strict_types=1);

namespace SerpApi;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Modern Client for SerpApi.
 */
class SerpApiClient
{
    private string $apiKey;
    private GuzzleClient $httpClient;
    private const BASE_URI = 'https://serpapi.com';

    /**
     * @param string $apiKey Your private API Key from SerpApi.
     * @param GuzzleClient|null $httpClient Optional: Inject a custom client (useful for testing).
     */
    public function __construct(string $apiKey, ?GuzzleClient $httpClient = null)
    {
        $this->apiKey = $apiKey;

        // Si no nos pasan un cliente, creamos uno estándar configurado correctamente.
        $this->httpClient = $httpClient ?? new GuzzleClient([
            'base_uri' => self::BASE_URI,
            'timeout'  => 10.0, // 10 segundos de espera máximo (evita cuelgues eternos)
        ]);
    }

    /**
     * Generic GET request handler.
     * Protected so child classes (like GoogleSearch) can use it for other endpoints.
     */
    protected function get(string $endpoint, array $query): array
    {
        try {
            $response = $this->httpClient->request('GET', $endpoint, [
                'query' => $query
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Error decoding JSON: " . json_last_error_msg());
            }

            if (isset($data['error'])) {
                throw new \Exception("SerpApi Error: " . $data['error']);
            }

            return $data;

        } catch (GuzzleException $e) {
            throw new \Exception("HTTP Request failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Executes a search query against SerpApi.
     *
     * @param string $engine The engine to use (e.g., 'google', 'bing').
     * @param array $parameters Search parameters (q, location, etc.).
     * @return array The JSON response decoded as an associative array.
     * @throws \Exception If the API request fails.
     */
    public function search(string $engine, array $parameters = []): array
    {
        $defaultParams = [
            'api_key' => $this->apiKey,
            'engine'  => $engine,
            'output'  => 'json',
            'source'  => 'php',
        ];

	return $this->get('/search', array_merge($defaultParams, $parameters));
    }
    /**
     * Get account information (plan, credits, usage).
     * * @return array Account details.
     */
    public function getAccount(): array
    {
        // Reutilizamos el método 'get' protegido que acabamos de crear.
        // Solo necesitamos pasar la api_key, que ya está en $defaultParams del método padre?
        // Espera, el método 'get' recibe raw query params.

        return $this->get('/account', [
            'api_key' => $this->apiKey
        ]);
    }
}
