<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Service\CurrencyManager;
use App\Service\NBPApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(NBPApiClient $client, CurrencyManager $currencyManager): Response
    {
        // pobranie danych z API NBP
        $rates = $client->fetchCurrenciesFromNBP();
        // aktualizacja danych w bazie
        $currencies = $currencyManager->checkForCurrencyUpdate($rates);

        return $this->render('main/index.html.twig', [
            'currencies' => $currencies,
        ]);
    }
}
