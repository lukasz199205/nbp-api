<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Service\NBPApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(NBPApiClient $client): Response
    {
        $em = $this->getDoctrine()->getManager();

        $rates = $client->fetchCurrenciesFromNBP();

        //iteracja po pobranych danych
        foreach ($rates[0]['rates'] as $rate) {
            // pobranie aktualnych danych z bazy dla konkretnej waluty
            $currency = $em->getRepository(Currency::class)
                ->findOneBy(['name' => $rate['currency']]);

            // jeżeli waluta nie istnieje w bazie to ją utwórz
            if (!$currency) {
                $currency = new Currency();
                $currency->setName($rate['currency']);
                $currency->setCurrencyCode($rate['code']);
            }

            // jeżeli kurs jest różny od tego zapisanego w bazie to aktualizuj
            if ($currency->getExchangeRate() != $rate['mid']) {
                $currency->setExchangeRate($rate['mid']);
            }

            $em->persist($currency);
        }
        $em->flush();

        //pobranie danych z bazy do wyświetlenia
        $data = $em->getRepository(Currency::class)
            ->findAll();

        return $this->render('main/index.html.twig', [
            'data' => $data,
        ]);
    }
}
