<?php

namespace App\Controller\Api;

use App\Service\OzonService;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ozon')]
class OzonApiController extends AbstractController
{
    public function __construct(
        protected OzonService $service,
        protected $mpStatsApi
    )
    {
    }

    #[Route(path: '/category', name: 'api_ozon_category')]
    public function category(Request $request): Response
    {
        return $this->json($this->service->getApiCategory($request->query->all()));
    }
}