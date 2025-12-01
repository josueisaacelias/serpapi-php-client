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
    public function testGetAccountReturnsData()
    {
        // 1. PREPARAR EL ESCENARIO (ARRANGE)
        // Creamos una respuesta falsa que simula ser SerpApi
        $mockBody = json_encode([
            'account_email' => 'test@serpapi.com',
            'plan_id' => 'free',
            'total_searches_left' => 100
        ]);

        // Configuramos Guzzle para que devuelva esa respuesta, y un 200 OK
        $mock = new MockHandler([
            new Response(200, [], $mockBody),
        ]);

        $handlerStack = HandlerStack::create($mock);
        
        // Creamos el cliente Guzzle con nuestro "cerebro falso"
        $mockHttpClient = new Client(['handler' => $handlerStack]);

        // Inyectamos el cliente falso en NUESTRA clase
        $serpApi = new SerpApiClient('fake_key', $mockHttpClient);

        // 2. EJECUTAR LA ACCIÓN (ACT)
        $account = $serpApi->getAccount();

        // 3. VERIFICAR RESULTADOS (ASSERT)
        // Verificamos que nuestro código decodificó bien el JSON
        $this->assertIsArray($account);
        $this->assertEquals('test@serpapi.com', $account['account_email']);
        $this->assertEquals(100, $account['total_searches_left']);
    }
}
