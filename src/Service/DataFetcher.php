<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\BtcCourse;
use App\Repository\BtcCourseRepository;
use App\Service\API\Api;
use Symfony\Component\Console\Input\InputInterface;

class DataFetcher
{
    public Api $api;

    public BtcCourseRepository $btcCourseRepository;

    public DateConversion $dateConversion;

    public function __construct(
        Api $api,
        BtcCourseRepository $btcCourseRepository,
        DateConversion $dateConversion
    )
    {
        $this->api = $api;
        $this->btcCourseRepository = $btcCourseRepository;
        $this->dateConversion = $dateConversion;
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

    public function getCurrencyApiByDateRange(string $dateFrom, string $dateTo): array
    {
        $this->dateConversion->validateDate($dateFrom);
        $this->dateConversion->validateDate($dateTo);

        return $this->btcCourseRepository->getDataByDateRange(
            new \DateTimeImmutable($dateFrom),
            new \DateTimeImmutable($dateTo)
        );
    }

    private function addTodayTransactions(string $currency): void
    {
        $beginningToday = (new \DateTimeImmutable())
            ->setTime(0, 0, 0)
            ->sub(new \DateInterval('PT1H'));

        $this->addTransactions($currency, $beginningToday, Api::ONE_DAY_LIMIT);
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
        $btc = new BtcCourse(
            $currency,
            $this->dateConversion->timestampToDateTime($transaction['time']),
            $transaction['high'],
            $transaction['low'],
            $transaction['open'],
            $transaction['close']
        );
        $this->btcCourseRepository->add($btc, true);
    }

    private function addMissingTransactions(string $currency): void
    {
        $lastAddedDate = $this->btcCourseRepository->getLastAddedCourse($currency)->getTime();
        $this->addTransactions($currency, $lastAddedDate);
    }
}