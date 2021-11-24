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
        $em = $this->getDoctrine()->getManager();
        // pobranie danych z API NBP
        $rates = $client->fetchCurrenciesFromNBP();
        // aktualizacja danych w bazie
        $currencyManager->update($rates);
        //pobranie danych z bazy do wyświetlenia
        $currencies = $em->getRepository(Currency::class)
            ->findBy([],['name' => 'ASC']);

        return $this->render('main/index.html.twig', [
            'currencies' => $currencies,
        ]);
    }
}
