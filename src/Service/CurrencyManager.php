<?php

namespace App\Service;

use App\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;

class CurrencyManager
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $rates
     * @return array
     */
    public function checkForCurrencyUpdate(array $rates) :array
    {
        $currencies = $this->em->getRepository(Currency::class)
            ->findBy([],['name' => 'ASC']);

        foreach ($rates as $rate) {
            $index = array_search($rate['code'], array_column($currencies, 'currency_code'));
            if (($index !== false) && ($rate['mid'] != $currencies[$index]->getExchangeRate())) {
                $currencies[$index]->setExchangeRate($rate['mid']);
            } elseif ($index === false) {
                $this->createNewCurrency($rate);
            }
        }
        $this->em->flush();
        return $currencies;
    }

    /**
     * @param array $rate
     */
    public function createNewCurrency(array $rate) :void
    {
        $currency = new Currency();
        $currency->setName($rate['currency']);
        $currency->setCurrencyCode($rate['code']);
        $currency->setExchangeRate($rate['mid']);
        $this->em->persist($currency);
    }
}