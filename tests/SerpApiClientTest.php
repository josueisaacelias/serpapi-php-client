<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use SerpApi\SerpApiClient;

class SerpApiClientTest extends TestCase
{
    public function testSearchEngineInjection()
    {
        // 1. Simulamos una respuesta de Yelp
        $mockBody = json_encode([
            'search_metadata' => [
                'status' => 'Success',
                'engine_url' => 'https://www.yelp.com/search...'
            ]
        ]);

        $mock = new MockHandler([
            new Response(200, [], $mockBody),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $mockHttpClient = new Client(['handler' => $handlerStack]);

        // 2. Instanciamos el cliente
        $client = new SerpApiClient('fake_key', $mockHttpClient);

        // 3. Probamos la flexibilidad del array
        $data = $client->search([
            'engine' => 'yelp',
            'find_desc' => 'Pizza',
            'find_loc' => 'New York'
        ]);

        $this->assertIsArray($data);
        $this->assertEquals('Success', $data['search_metadata']['status']);
    }
} 
