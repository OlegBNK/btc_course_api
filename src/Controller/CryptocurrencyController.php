<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\DataFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CryptocurrencyController extends AbstractController
{
    /** @var DataFetcher */
    private $dataFetcher;

    /**
     * CryptocurrencyController constructor.
     * @param DataFetcher $dataFetcher
     */
    public function __construct(
        DataFetcher $dataFetcher
    )
    {
        $this->dataFetcher = $dataFetcher;
    }


    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @Route("/{dateFrom}/{dateTo}")
     * @return JsonResponse
     */
    public function getApiByDateRange(string $dateFrom, string $dateTo): JsonResponse
    {
        try {
            $currencyApiByDateRange = $this->dataFetcher->getCurrencyApiByDateRange($dateFrom, $dateTo);
            return new JsonResponse($currencyApiByDateRange);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

    }

}