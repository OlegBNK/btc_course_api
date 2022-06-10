<?php declare(strict_types=1);

namespace App\Service;

use App\Constants\DataOutput;
use App\Entity\BtcCourse;
use App\Repository\BtcCourseRepository;
use App\Service\API\Api;

class DataFetcher
{
    /** @var Api */
    public $api;

    /** @var BtcCourseRepository */
    public $btcCourseRepository;

    /** @var DateConversion */
    public $dateConversion;

    private const CURRENCY_TO = [
        'USD',
        'EUR'
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
            foreach (self::CURRENCY_TO as $currencyTo) {
                $this->addingTransactionsToTable($currencyTo);
            }
        } else {
            foreach (self::CURRENCY_TO as $currencyTo) {
                $oldAddedDate = $this->btcCourseRepository->getDateOldAddedCourse($currencyTo)->getTime();
                $this->addingTransactionsToTable($currencyTo, $oldAddedDate);
            }
        }
    }

    private function addingTransactionsToTable($currencyTo, $showTo = null): void
    {
        foreach ($this->api->get($currencyTo, $showTo) as $transaction) {
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
    }

    public function getCurrencyApiByDateRange(string $dateFrom, string $dateTo)
    {
        $this->dateConversion->validateDate($dateFrom);
        $this->dateConversion->validateDate($dateTo);

        return $this->btcCourseRepository->getDataByDateRange(
            new \DateTimeImmutable($dateFrom),
            new \DateTimeImmutable($dateTo)
        );
    }
}