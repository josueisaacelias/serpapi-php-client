<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Middleware;
use SerpApi\SerpApiClient;

class SerpApiClientTest extends TestCase
{
    #[Test]
    public function synchronous_search_works(): void
    {
        $mockBody = json_encode([
            'search_metadata' => ['status' => 'Success'],
            'organic_results' => [['title' => 'Pizza Place']]
        ]);

        $client = $this->createMockClient([new Response(200, [], $mockBody)]);

        $data = $client->search([
            'engine' => 'google',
            'q' => 'Pizza'
        ]);

        $this->assertIsArray($data);
        $this->assertEquals('Success', $data['search_metadata']['status']);
        $this->assertEquals('Pizza Place', $data['organic_results'][0]['title']);
    }

    #[Test]
    public function batch_mode_returns_correct_keys_and_data(): void
    {
        $client = $this->createMockClient([
            new Response(200, [], json_encode(['organic_results' => [['title' => 'Result A']]])),
            new Response(200, [], json_encode(['organic_results' => [['title' => 'Result B']]])),
        ]);

        $queries = [
            'query_a' => ['q' => 'A'],
            'query_b' => ['q' => 'B'],
        ];

        $results = $client->searchBatch($queries);

        $this->assertCount(2, $results);
        $this->assertArrayHasKey('query_a', $results);
        $this->assertEquals('Result A', $results['query_a']['organic_results'][0]['title']);
        $this->assertEquals('Result B', $results['query_b']['organic_results'][0]['title']);
    }

    #[Test]
    public function batch_mode_handles_partial_failures_gracefully(): void
    {
        $client = $this->createMockClient([
            new Response(200, [], json_encode(['status' => 'ok'])), // Éxito
            new RequestException('Error de Red', new Request('GET', 'test')) // Fallo
        ]);

        $queries = [
            'id_success' => ['q' => 'good'],
            'id_fail'    => ['q' => 'bad'],
        ];

        $results = $client->searchBatch($queries);

        $this->assertArrayHasKey('status', $results['id_success']);
        $this->assertArrayHasKey('error', $results['id_fail']);
        $this->assertStringContainsString('Error de Red', $results['id_fail']['error']);
    }

    #[Test]
    public function defaults_are_merged_correctly_in_batch_mode(): void
    {
        $container = [];
        $history = Middleware::history($container);

        $mock = new MockHandler([new Response(200, [], json_encode([]))]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        // Inyectamos el handler manualmente, así que no usamos el helper createMockClient aquí
        $client = new SerpApiClient('fake_key', new Client(['handler' => $handlerStack]));

        $client->searchBatch(['test_1' => ['q' => 'Coffee']], ['engine' => 'google', 'location' => 'Austin']);

        $this->assertCount(1, $container);
        $queryString = $container[0]['request']->getUri()->getQuery();

        $this->assertStringContainsString('engine=google', $queryString);
        $this->assertStringContainsString('location=Austin', $queryString);
        $this->assertStringContainsString('api_key=fake_key', $queryString);
    }

    // --- Helper Privado ---
    private function createMockClient(array $responses): SerpApiClient
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        return new SerpApiClient('fake_key', new Client(['handler' => $handlerStack]));
    }
} 
