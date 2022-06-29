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

    public function fillingTable(array $currencies): void
    {
        if ($this->btcCourseRepository->isEmptyTable() === true) {
            foreach ($currencies as $currency) {
                $this->addTodayTransaction($currency);
            }
        } else {
            $diffCurrencies = array_diff($currencies, $this->btcCourseRepository->getCurrencies());
            if ($diffCurrencies) {
                foreach ($diffCurrencies as $diffCurrency) {
                    $this->addTodayTransaction($diffCurrency);
                }
            } else {
                foreach ($currencies as $currency) {
                    $this->addMissingTransaction($currency);
                }
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

    private function addTodayTransaction(string $currency): void
    {
        $beginningToday = (new \DateTimeImmutable())
            ->setTime(0, 0, 0)
            ->sub(new \DateInterval('P0Y0M0DT1H0M0S'));

        $this->addTransactions($currency, $beginningToday);
    }

    private function addTransactions(string $currencyTo, \DateTimeImmutable $date): void
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

    private function addMissingTransaction(string $currency): void
    {
        $lastAddedDate = $this->btcCourseRepository->getDateLastAddedCourse($currency)->getTime();
        $this->addTransactions($currency, $lastAddedDate);
    }
}