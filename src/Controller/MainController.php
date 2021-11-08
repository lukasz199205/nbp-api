<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Service\CurrencyUpdate;
use App\Service\NBPApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(NBPApiClient $client, CurrencyUpdate $update): Response
    {
        $em = $this->getDoctrine()->getManager();
        $rates = $client->fetchCurrenciesFromNBP();
        $update->update($rates);

        //pobranie danych z bazy do wyświetlenia
        $data = $em->getRepository(Currency::class)
            ->findAll();

        return $this->render('main/index.html.twig', [
            'data' => $data,
        ]);
    }
}
