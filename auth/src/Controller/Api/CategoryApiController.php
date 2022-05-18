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
#[Route('/api')]
class CategoryApiController extends AbstractController
{
    public function __construct(
        protected CategoryService $service,
        protected $mpStatsApiWb
    )
    {
    }

    #[Route('/subCategory', name: 'sub_category', methods: ['GET'])]
    public function subCategory(Request $request): JsonResponse
    {
        $category = $request->query->all()['url'];
        $d1 = (new \DateTime())->modify('-1 day');
        $d2 = (new \DateTime())->modify('-30 day');
        $data = (new Client())->get(
            $this->mpStatsApiWb."category/subcategories?path=$category&".$this->service->getDate($d1, $d2),
            $this->service->getHeaders()
        )->getBody()->getContents();
        return $this->json(json_decode($data, true));
    }
    #[Route('/brands', name: 'brands', methods: ['GET'])]
    public function brands(Request $request): JsonResponse
    {
        $category = $request->query->all()['url'];
        $d1 = (new \DateTime())->modify('-1 day');
        $d2 = (new \DateTime())->modify('-30 day');
        $data = (new Client())->get(
            $this->mpStatsApiWb."category/brands?path=$category&".$this->service->getDate($d1, $d2),
            $this->service->getHeaders()
        )->getBody()->getContents();
        $data = json_decode($data, true);
        $sales_percent = 0;
        $revenue_percent = 0;
        foreach ($data as $item){
            $sales_percent += $item['sales'];
            $revenue_percent += $item['revenue'];
        }
        $data = array_map(function ($item) use ($revenue_percent, $sales_percent) {
            $item['sales_percent'] = $item['sales']*100/$sales_percent;
            $item['revenue_percent'] = $item['revenue']*100/$revenue_percent;
            return $item;
        }, $data);

        return $this->json($data);
    }
    #[Route('/sellers', name: 'sellers', methods: ['GET'])]
    public function sellers(Request $request): JsonResponse
    {
        $category = $request->query->all()['url'];
        $d1 = (new \DateTime())->modify('-1 day');
        $d2 = (new \DateTime())->modify('-30 day');
        $data = (new Client())->get(
            $this->mpStatsApiWb."category/sellers?path=$category&".$this->service->getDate($d1, $d2),
            $this->service->getHeaders()
        )->getBody()->getContents();
        $data = json_decode($data, true);
        $sales_percent = 0;
        $revenue_percent = 0;
        foreach ($data as $item){
            $sales_percent += $item['sales']??0;
            $revenue_percent += $item['revenue']??0;
        }
        $data = array_map(function ($item) use ($revenue_percent, $sales_percent) {
            $item['sales_percent'] = $item['sales']*100/$sales_percent;
            $item['revenue_percent'] = $item['revenue']*100/$revenue_percent;
            return $item;
        }, $data);
        return $this->json($data);
    }
}