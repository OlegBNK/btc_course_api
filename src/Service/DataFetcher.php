<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\BtcCourse;
use App\Repository\BtcCourseRepository;
use App\Service\API\Api;

class DataFetcher
{
    public Api $api;

    public BtcCourseRepository $btcCourseRepository;

    public DateConversion $dateConversion;

    private const CURRENCY_TO = [
        'USD'
    ];

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

    public function fillingTable(): void
    {
        if ($this->btcCourseRepository->isEmptyTable() === true) {
            $beginningToday = (new \DateTimeImmutable())
                ->setTime(0, 0, 0)
                ->sub(new \DateInterval('P0Y0M0DT1H0M0S'));
            foreach (self::CURRENCY_TO as $currencyTo) {
                $this->addTransactions($currencyTo, $beginningToday);
            }
        } else {
            foreach (self::CURRENCY_TO as $currencyTo) {
                $lastAddedDate = $this->btcCourseRepository->getDateLastAddedCourse($currencyTo)->getTime();
                $this->addTransactions($currencyTo, $lastAddedDate);
            }
        }
    }

    private function addTransactions(string $currencyTo, \DateTimeImmutable $date)
    {
        foreach ($this->api->get($currencyTo) as $transaction) {
            if ($date->getTimestamp() < $transaction['time']) {
                $this->addingEntityToTable($currencyTo, $transaction);
            }
        }
    }

    private function addingEntityToTable(string $currencyTo, array $transaction): void
    {
        $btc = new BtcCourse(
            $currencyTo,
            $this->dateConversion->timestampToDateTime($transaction['time']),
            $transaction['high'],
            $transaction['low'],
            $transaction['open'],
            $transaction['close']
        );
        $this->btcCourseRepository->add($btc, true);
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
}