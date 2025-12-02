<?php

declare(strict_types=1);

namespace SerpApi;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use Psr\Http\Message\ResponseInterface;

/**
 * SerpApi Client - Modern PHP 8.2 implementation.
 */
class SerpApiClient
{
    private const BASE_URI = 'https://serpapi.com';
    private GuzzleClient $httpClient;

    /**
     * PHP 8.2 Magic: Constructor Promotion + Null Coalescing en una línea.
     */
    public function __construct(
        private readonly string $apiKey, // 🔒 Readonly: Nadie puede cambiar la API key después de iniciar
        ?GuzzleClient $httpClient = null
    ) {
        $this->httpClient = $httpClient ?? new GuzzleClient([
            'base_uri' => self::BASE_URI,
            'timeout'  => 30.0,
            'curl' => [
                CURLOPT_MAXCONNECTS => 50,
                CURLOPT_TCP_NODELAY => true,
            ],
        ]);
    }

    public function search(array $params): array
    {
        return $this->get('/search', $params);
    }

    public function searchBatch(array $queries, array $defaults = []): array
    {
        $promises = [];

        foreach ($queries as $id => $queryParams) {
            $finalParams = [...$defaults, ...$queryParams]; // ✨ PHP 8 Spread Operator para arrays

            $promises[$id] = $this->getAsync('/search', $finalParams)
                ->then(
                    fn(ResponseInterface $res) => $this->decodeResponse($res), // ✨ Arrow Functions cortas
                    fn(\Throwable $e) => [ // ✨ Arrow Functions para manejo de error
                        'error' => "Async Request Failed: " . $e->getMessage(),
                        'code' => $e->getCode()
                    ]
                );
        }

        return Utils::unwrap($promises);
    }

    public function getArchive(string $searchId): array
    {
        return $this->get("/searches/{$searchId}.json", []);
    }

    public function getLocations(array $params): array
    {
        return $this->get('/locations.json', $params);
    }

    public function getAccount(): array
    {
        return $this->get('/account', []);
    }

    protected function get(string $endpoint, array $query): array
    {
        try {
            $response = $this->httpClient->request('GET', $endpoint, [
                'query' => $this->prepareQuery($query)
            ]);
            return $this->decodeResponse($response);
        } catch (GuzzleException $e) {
            throw new \Exception("HTTP Request failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function getAsync(string $endpoint, array $query): PromiseInterface
    {
        return $this->httpClient->requestAsync('GET', $endpoint, [
            'query' => $this->prepareQuery($query)
        ]);
    }

    private function prepareQuery(array $query): array
    {
        // Null Coalescing Assignment Operator (??=) de PHP 7.4/8.0
        $query['api_key'] ??= $this->apiKey;
        $query['output'] = 'json';
        $query['source'] = 'php_modern_8.2_client';
        return $query;
    }

    private function decodeResponse(ResponseInterface $response): array
    {
        try {
            // Throw on error es nativo y limpio
            $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \Exception("Error decoding JSON: " . $e->getMessage());
        }

        if (isset($data['error'])) {
            throw new \Exception("SerpApi Error: " . $data['error']);
        }

        return $data;
    }
} 
