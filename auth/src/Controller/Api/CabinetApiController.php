<?php

namespace App\Controller\Api;

use App\Service\CategoryService;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
#[Route('/api/cabinet')]
class CabinetApiController extends AbstractController
{
    public function __construct(
        protected CategoryService $service,
        protected $mpStatsApiWb,
        protected $mpStatsApi
    )
    {
    }

    #[Route('/compare', name: 'cabinet_api_compare', methods: ['GET'])]
    public function cabinetCompare(Request $request): JsonResponse
    {
        $data = (new Client())->post(
            $this->mpStatsApi."cabinet/wb/get/reports/compare?".http_build_query($request->query->all()),
            $this->service->getHeaders()
        )->getBody()->getContents();
        $data = array_map(
            function ($item) {
                $item['img'] = ((int)($item["nm_id"] / 10000)) * 10000;
                return $item;},
            json_decode($data, true)['data']
        );
        $data['data'] = $data;
        return $this->json($data);
    }
}