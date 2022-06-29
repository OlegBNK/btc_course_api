<?php
// src/Command/CreateUserCommand.php
namespace App\Console;

use App\Service\DataFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCourses extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:courses-update';

    private DataFetcher $dataFetcher;

    public function __construct(DataFetcher $dataFetcher)
    {
        parent::__construct();
        $this->dataFetcher = $dataFetcher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $currencies = $input->getArgument('currencies');

//        dd($currencies);

        $this->dataFetcher->fillingTable($currencies);

        $output->writeln("data updated");

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
//        $this->addArgument('currency', InputArgument::IS_ARRAY, 'description example');

        $this
//        ->addArgument('limit', InputArgument::REQUIRED, 'What request limit do you need?')
        ->addArgument('currencies', InputArgument::IS_ARRAY, 'What currencies do you want to set?');
    }
}