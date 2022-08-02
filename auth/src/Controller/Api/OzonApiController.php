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

    #[Route(path: '/category/brands', name: 'api_ozon_category_brands')]
    public function brandsCategory(Request $request): Response
    {
        return $this->json($this->service->getApiCategoryBrands($request->query->all(), $request->query->get('url')));
    }

    #[Route(path: '/subCategory', name: 'api_ozon_sub_category')]
    public function subCategory(Request $request): Response
    {
        return $this->json($this->service->getApiSubCategory($request->query->all()));
    }

    #[Route(path: '/category', name: 'api_ozon_category')]
    public function category(Request $request): Response
    {
        return $this->json($this->service->getApiCategory($request->query->all(), $request->query->get('url', 'seller')));
    }
    #[Route(path: '/brands', name: 'api_ozon_brands')]
    public function brands(Request $request): Response
    {
        return $this->json($this->service->getApiBrands($request->query->all(), $request->query->get('url', 'seller')));
    }
    #[Route(path: '/onDay', name: 'api_ozon_onday')]
    public function onDay(Request $request): Response
    {
        return $this->json($this->service->getApiOnDay($request->query->all(), $request->query->get('url', 'seller')));
    }
    #[Route(path: '/prcSegm', name: 'api_ozon_prc_segm')]
    public function prcSegm(Request $request): Response
    {
        return $this->json($this->service->getApiPrcSegm($request->query->all(), $request->query->get('url', 'seller')));
    }
    #[Route(path: '/compare', name: 'api_ozon_compare')]
    public function compare(Request $request): Response
    {
        return $this->json($this->service->getApiCompare($request->query->all(), $request->query->get('url', 'seller')));
    }
}