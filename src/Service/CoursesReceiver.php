<?php declare(strict_types=1);

namespace App\Service;

use App\Repository\BtcCourseRepository;
use App\Service\API\Api;
use Symfony\Component\Console\Input\InputInterface;

class CoursesReceiver
{
    public const COURSES = ['USD', 'EUR', 'UAH'];

    public Api $api;

    public BtcCourseRepository $btcCourseRepository;

    public DateConversion $dateConversion;

    private CourseFactory $btc;

    public function __construct(
        Api $api,
        BtcCourseRepository $btcCourseRepository,
        DateConversion $dateConversion,
        CourseFactory $btc
    )
    {
        $this->api = $api;
        $this->btcCourseRepository = $btcCourseRepository;
        $this->dateConversion = $dateConversion;
        $this->btc = $btc;
    }

    public function updateCoursesFor(array $currencies): void
    {
        foreach ($currencies as $currency) {
            $currencyLastDate = $this->btcCourseRepository->getLastAddedCourseDateFor($currency);
            if ($currencyLastDate) {
                $this->addMissingTransactions($currency);
            } else {
                $this->addTodayTransactions($currency);
            }
        }
    }

    private function addTodayTransactions(string $currency): void
    {
        $this->addTransactions(
            $currency,
            (new \DateTimeImmutable("00:00:00"))->sub(new \DateInterval('PT1H')),
            Api::ONE_DAY_LIMIT
        );
    }

    private function addTransactions(string $currency, \DateTimeImmutable $date, ?int $limit = null): void
    {
        foreach ($this->api->get($currency, null, $limit) as $transaction) {
            if ($date->getTimestamp() < $transaction['time']) {
                $this->addTransaction($currency, $transaction);
            }
        }
    }

    private function addTransaction(string $currency, array $transaction): void
    {
        $this->btcCourseRepository->add($this->btc->making($currency, $transaction), true);
    }

    private function addMissingTransactions(string $currency): void
    {
        $lastAddedDate = $this->btcCourseRepository->getLastAddedCourse($currency);
        $this->addTransactions($currency, $lastAddedDate);
    }
}