<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class NBPApiClient
{
    private HttpClientInterface $httpClient;


    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function fetchCurrenciesFromNBP() :array
    {
        // pobranie aktualnych danych z API NBP z tabeli A

        $response = $this->httpClient->request('GET', 'http://api.nbp.pl/api/exchangerates/tables/A?format=json/');
        $rates = $response->getContent();
        $rates = $response->toArray();

        return $rates;
    }
}