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
        protected $mpStatsApiWb,
        protected $mpStatsApi
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

    #[Route('/trends', name: 'trends', methods: ['GET'])]
    public function trends(Request $request): JsonResponse
    {
        $category = $request->query->all()['url'];
        $d1 = (new \DateTime())->modify('-1 day');
        $d2 = (new \DateTime())->modify('-30 day');
        $data = (new Client())->get(
            $this->mpStatsApiWb."category/trends?path=$category&".$this->service->getDate($d1, $d2),
            $this->service->getHeaders()
        )->getBody()->getContents();
        return $this->json(array_reverse(json_decode($data, true)));
    }

    #[Route('/onDay', name: 'onDay', methods: ['GET'])]
    public function onDay(Request $request): JsonResponse
    {
        $category = $request->query->all()['url'];
        $d1 = (new \DateTime())->modify('-1 day');
        $d2 = (new \DateTime())->modify('-30 day');
        $data = (new Client())->get(
            $this->mpStatsApiWb."category/by_date?groupBy=day&path=$category&".$this->service->getDate($d1, $d2),
            $this->service->getHeaders()
        )->getBody()->getContents();
        return $this->json(json_decode($data, true));
    }

    #[Route('/priceSegment', name: 'price_segment', methods: ['GET'])]
    public function priceSegment(Request $request): JsonResponse
    {
        $category = $request->query->all()['url'];
        $d1 = (new \DateTime())->modify('-1 day');
        $d2 = (new \DateTime())->modify('-30 day');
        $data = (new Client())->get(
            $this->mpStatsApiWb."category/price_segmentation?groupBy=day&path=$category&".$this->service->getDate($d1, $d2),
            $this->service->getHeaders()
        )->getBody()->getContents();
        return $this->json(json_decode($data, true));
    }

    #[Route('/compare', name: 'api_compare', methods: ['GET'])]
    public function compare(Request $request): JsonResponse
    {
        $category = $request->query->all()['url'];
        $date = new \DateTime();
        $client = new Client();
        $body = [
            'd11' => $date->modify('-29 day')->format('Y-m-d'),
            'd12' => $date->modify('+2 weeks')->format('Y-m-d'),
            'd21' => $date->modify('+1 day')->format('Y-m-d'),
            'd22' => $date->modify('+2 weeks')->format('Y-m-d'),
        ];
        $data = $client->post(
            $this->mpStatsApi."wb/get/category/compare?path=$category",
            $this->service->getHeadersWithBody($body)
        )->getBody()->getContents();
        $data = json_decode($data, true);
        $data['data'] = array_map(function ($item) use ($client) {
            $item['img'] = json_decode(
                $client->get($this->mpStatsApi."wb/get/item/".$item['sku'], $this->service->getHeaders())->getBody()->getContents(), true
            )['photos'][0]['t'];
            return $item;
        }, $data['data']);
        $data['date'] = $body;
        return $this->json($data);
    }

    #[Route('/items', name: 'items', methods: ['GET'])]
    public function items(Request $request): JsonResponse
    {
        $category = $request->query->all()['url'];
        $d1 = (new \DateTime())->modify('-1 day');
        $d2 = (new \DateTime())->modify('-30 day');
        $data = (new Client())->get(
            $this->mpStatsApiWb."category/items?path=$category&".$this->service->getDate($d1, $d2),
            $this->service->getHeaders()
        )->getBody()->getContents();
        return $this->json(json_decode($data, true));
    }

}