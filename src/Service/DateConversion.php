<?php declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;
use http\Exception\InvalidArgumentException;

class DateConversion
{
    public function dateTimeToTimestamp(DateTimeImmutable $dateTime): int
    {
        return (new \DateTime($dateTime->format('Y-m-d H:i:s')))->getTimestamp();
    }

    public function timestampToDateTime(int $unixTimestamp): DateTimeImmutable
    {
        return (new DateTimeImmutable())->setTimestamp($unixTimestamp);
    }

    public function validateDate($date, $format = 'Y-m-d H:i'): void
    {
        try {
            $datetime = \DateTime::createFromFormat($format, $date);
            if ($datetime->format($format) !== $date) {
                throw new \InvalidArgumentException('invalid date');
            }
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Message: ' . $e->getMessage());
        }
    }
}