<?php declare(strict_types=1);

namespace App\Console;

use App\Service\CoursesReceiver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCourses extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:courses-update';

    private CoursesReceiver $dataFetcher;

    public function __construct(CoursesReceiver $dataFetcher)
    {
        parent::__construct();
        $this->dataFetcher = $dataFetcher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $currencies = $input->getArgument('currencies');
        $array_diff = array_diff($currencies, CoursesReceiver::COURSES);

        if ($array_diff){

            $output->writeln(sprintf("неправильно введен буквенный код валюты: %s", implode(", ", $array_diff)));

            return Command::INVALID;
        }

        $this->dataFetcher->updateCoursesFor($currencies);

        $output->writeln("data updated");

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument(
            'currencies',
            InputArgument::IS_ARRAY,
            'доступны данные валюты: USD, EUR, UAH'
        );
    }
}