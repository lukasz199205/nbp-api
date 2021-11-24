<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NBPApiClient
{
    private HttpClientInterface $httpClient;
    private ContainerBagInterface $params;


    public function __construct(HttpClientInterface $httpClient, ContainerBagInterface $params)
    {
        $this->params = $params;
        $this->httpClient = $httpClient;
    }

    public function fetchCurrenciesFromNBP() :array
    {
        // pobranie aktualnych danych z API NBP z tabeli A


        $response = $this->httpClient->request('GET', $this->params->get('app.nbp_api_table_a'));
        $rates = $response->getContent();
        $rates = $response->toArray();

        return $rates[0]['rates'];
    }
}