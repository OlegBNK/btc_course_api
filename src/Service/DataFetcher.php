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
            $beginningToday = \DateTimeImmutable::createFromFormat('H\h i\m s\s', '00h 00m 00s');
            foreach (self::CURRENCY_TO as $currencyTo) {
                $this->addingTransactionsToTable($currencyTo, null, $beginningToday);
            }
        } else {
            foreach (self::CURRENCY_TO as $currencyTo) {
                $lastAddedDate = $this->btcCourseRepository->getDateLastAddedCourse($currencyTo)->getTime();
                $this->addingTransactionsToTable($currencyTo, null, null, $lastAddedDate);
            }
        }
    }

    private function addingTransactionsToTable(
        string $currencyTo,
        ?\DateTimeImmutable $showTo = null,
        ?\DateTimeImmutable $beginningToday = null,
        ?\DateTimeImmutable $lastAddedDate = null
    ): void
    {
        if ($beginningToday) {
            foreach ($this->api->get($currencyTo, $showTo) as $transaction) {
                if ($beginningToday->getTimestamp() <= $transaction['time']) {
                    $this->addingEntityToTable($currencyTo, $transaction);
                }
            }
        } elseif ($lastAddedDate) {
            foreach ($this->api->get($currencyTo, $showTo) as $transaction) {
                if ($lastAddedDate->getTimestamp() < $transaction['time']) {
                    $this->addingEntityToTable($currencyTo, $transaction);
                }
            }
        } else {
            foreach ($this->api->get($currencyTo, $showTo) as $transaction) {
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