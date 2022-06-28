<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\DataFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FillData extends AbstractController
{
    private DataFetcher $dataFetcher;

    public function __construct(DataFetcher $dataFetcher)
    {
        $this->dataFetcher = $dataFetcher;
    }

    /**
     * @Route("/fillData")
     */
    public function main(): JsonResponse
    {
        $this->dataFetcher->fillingTable();

        return new JsonResponse('ok');
    }
}