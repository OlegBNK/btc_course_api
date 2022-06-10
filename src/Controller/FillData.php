<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\DataFetcher;
use Symfony\Component\HttpFoundation\JsonResponse;

class FillData
{
    /**
     * @var DataFetcher
     */
    private $dataFetcher;

    public function __construct(DataFetcher $dataFetcher)
    {
        $this->dataFetcher = $dataFetcher;
    }

    /**
     * @return JsonResponse
     * @Route("/")
     */
    public function main(): JsonResponse
    {
        $this->dataFetcher->fillingTable();

        return new JsonResponse('ok');
    }


}