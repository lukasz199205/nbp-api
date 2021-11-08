<?php

namespace App\Controller;

use App\Entity\Currency;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(HttpClientInterface $client): Response
    {

        $em = $this->getDoctrine()->getManager();

        // pobranie danych z API NBP
        $response = $client->request('GET', 'http://api.nbp.pl/api/exchangerates/tables/A?format=json/');
        $rates = $response->getContent();
        $rates = $response->toArray();

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
