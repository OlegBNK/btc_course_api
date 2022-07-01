<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\CoursesReturner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Cryptocurrency extends AbstractController
{
    private CoursesReturner $coursesReturner;

    public function __construct(
        CoursesReturner $coursesReturner
    )
    {
        $this->coursesReturner = $coursesReturner;
    }

    /**
     * @Route("/{dateFrom}/{dateTo}")
     */
    public function getApiByDateRange(string $dateFrom, string $dateTo): JsonResponse
    {
        try {
            $currencyApiByDateRange = $this->coursesReturner->getCurrencyApiByDateRange($dateFrom, $dateTo);
            return new JsonResponse($currencyApiByDateRange);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

}