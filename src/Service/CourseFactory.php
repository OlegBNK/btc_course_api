<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\BtcCourse;

class CourseFactory
{
    private DateConversion $dateConversion;

    public function __construct(
        DateConversion $dateConversion
    )
    {
        $this->dateConversion = $dateConversion;
    }

    public function making($currency, $transaction)
    {
        return new BtcCourse(
            $currency,
            $this->dateConversion->timestampToDateTime($transaction['time']),
            $transaction['high'],
            $transaction['low'],
            $transaction['open'],
            $transaction['close']
        );
    }
}