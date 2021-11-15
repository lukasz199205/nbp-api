<?php

namespace App\Service;

use App\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;

class CurrencyUpdate
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //TODO podzielić funkcje na update i create oraz dodatkowo pozbyć się z pętli zapytania do bazy
    public function update(array $rates) :void
    {
        foreach ($rates[0]['rates'] as $rate) {

            // pobranie aktualnych danych z bazy dla konkretnej waluty
            $currency = $this->em->getRepository(Currency::class)
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
            $this->em->persist($currency);
        }
        $this->em->flush();
    }
}