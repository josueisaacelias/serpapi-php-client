<?php

declare(strict_types=1);

namespace SerpApi;

/**
 * Specialized client for Google Search.
 */
class GoogleSearch extends SerpApiClient
{
    /**
     * @param string $apiKey
     * @param mixed $httpClient (Opcional, para testing)
     */
    public function __construct(string $apiKey, $httpClient = null)
    {
        // Llamamos al padre (SerpApiClient), pero... 
        // ¡Aquí no definimos el engine todavía! El padre es el transporte.
        parent::__construct($apiKey, $httpClient);
    }

    /**
     * Wrapper simplificado para buscar en Google.
     * El usuario ya no necesita pasar 'engine' => 'google', nosotros lo forzamos.
     */
    public function search(array $parameters = []): array
    {
        // Forzamos el motor correcto. 
        // Esto evita el bug de "HomeDepot buscando en Walmart" del repo antiguo.
        return parent::search('google', $parameters);
    }

    /**
     * Ejemplo de método específico que solo Google tiene.
     * En el repo viejo, esto lanzaba error de "Bing" por copy-paste.
     */
    public function getLocation(string $q, int $limit = 5): array
    {
        // Usamos el cliente HTTP interno para consultar la API de Locations
        // Nota: SerpApi tiene un endpoint diferente para locations, no es /search
        // Así que usaremos una lógica similar a search, pero a '/locations.json'
        $params = [
            'q' => $q,
            'limit' => $limit
        ];
        
        return $this->get('/locations.json', $params);
    }
}
