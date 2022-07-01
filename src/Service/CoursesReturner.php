<?php declare(strict_types=1);

namespace App\Service;

use App\Repository\BtcCourseRepository;

class CoursesReturner
{
    private DateConversion $dateConversion;

    private BtcCourseRepository $btcCourseRepository;

    public function __construct(
        BtcCourseRepository $btcCourseRepository,
        DateConversion $dateConversion
    )
    {
        $this->dateConversion = $dateConversion;
        $this->btcCourseRepository = $btcCourseRepository;
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