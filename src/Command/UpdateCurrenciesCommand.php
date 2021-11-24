<?php

namespace App\Command;

use App\Service\CurrencyManager;
use App\Service\NBPApiClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateCurrenciesCommand extends Command
{
    protected static $defaultName = 'app:update-currencies';
    protected static $defaultDescription = 'Fetches data from NBP API and updates currencies data in database';
    private CurrencyManager $currencyManager;
    private NBPApiClient $client;

    public function __construct(CurrencyManager $currencyManager, NBPApiClient $client, string $name = null)
    {
        $this->currencyManager = $currencyManager;
        $this->client = $client;
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $rates = $this->client->fetchCurrenciesFromNBP();
        $this->currencyManager->checkForCurrencyUpdate($rates);

        $io->success('Kursy walut zostały zaktualizowane');

        return Command::SUCCESS;
    }
}
