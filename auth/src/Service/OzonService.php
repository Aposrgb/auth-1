<?php

namespace App\Service;

use App\Entity\CategorySales;
use App\Entity\DataCategory;
use App\Helper\Enum\CategoryEnum;
use GuzzleHttp\Client;

class OzonService extends AbstractService
{
    public function findBrand($brand, $query)
    {
        $context = [];
        try{
            $date = key_exists("date", $query)?explode(' to ', $query['date']):null;
            $d1 = $date?$date[0]:(new \DateTime())->modify("- 31 day")->format("Y-m-d");
            $d2 = $date?$date[1]:(new \DateTime())->modify("- 1 day")->format("Y-m-d");
            $body = [
                "startRow" => 0,
                "endRow" => 100,
                "sortModel" => [
                    [
                        "sort" => "desc",
                        "colId" => "revenue"
                    ]
                ]
            ];
            $context['data'] = (new Client())->post($this->mpStatsApi."oz/get/brand?path=$brand&d1=$d1&d2=$d2", $this->getHeadersWithBody($body));
            $context['data'] = json_decode($context['data']->getBody()->getContents(), true)['data'];
            $context['seller'] = $brand;
            $context['d1'] = $d1;
            $context['d2'] = $d2;
        }catch (\Exception $exception){}

        return $context;
    }

    public function getApiCompare($query, $url = 'seller')
    {
        $path = $query['path'];
        $date = (new \DateTime())->modify("-30 day");
        $body = [
            "startRow" => 0,
            "endRow" => 100,
            "sortModel"=> [
                [
                    "sort"=> "desc",
                    "colId"=> "revenue_diff"
                ]
            ],
            "d11" => $date->format('Y-m-d'),
            "d12" => $date->modify('+14 day')->format('Y-m-d'),
            "d21" => $date->modify("+1 day")->format('Y-m-d'),
            "d22" => $date->modify('+14 day')->format('Y-m-d')
        ];
        $response = (new Client())->post($this->mpStatsApi."oz/get/$url/compare?path=$path&delivery_scheme=0,1,2,3", $this->getHeadersWithBody($body));
        $response = json_decode($response->getBody()->getContents(), true)['data'];
        return [
            'data' => $response,
            'd11' => $body['d11'],
            'd12' => $body['d12'],
            'd21' => $body['d21'],
            'd22' => $body['d22'],
        ];
    }

    public function getApiPrcSegm($query, $url = 'seller')
    {
        $date = explode(' to ', $query['date']);
        $path = $query['path'];
        $max = "maxPrice=".($query['max']??'');
        $min = "minPrice=".($query['min']??'');
        $segm = "segmentsCnt=".($query['prcSegm']??25);
        $response = (new Client())->get($this->mpStatsApi."oz/get/$url/price_segmentation?d1=$date[0]&d2=$date[1]&path=$path&$max&$min&$segm&deliveryScheme=0,1,2,3", $this->getHeaders());
        $response = json_decode($response->getBody()->getContents(), true);
        return [
            'data' => $response,
            'min' => count($response) > 0 ? min(array_map(function ($item){return $item['min_range_price'];}, $response)):0,
            'max' => count($response) > 0 ? max(array_map(function ($item){return $item['max_range_price'];}, $response)):($query['max']??100),
            'prcSegm' => $query['prcSegm']??25
        ];
    }

    public function getApiOnDay($query, $url = 'seller')
    {
        $date = explode(' to ', $query['date']);
        $path = $query['path'];
        $response = (new Client())->get($this->mpStatsApi."oz/get/$url/by_date?d1=$date[0]&d2=$date[1]&path=$path", $this->getHeaders());
        $response = json_decode($response->getBody()->getContents(), true);
        $data = [];
        foreach ($response as $index => $item){
            $data[] = array_merge(['name' => $index], $item);
        }
        return $data;
    }

    public function getApiBrands($query, $url = 'seller')
    {
        $date = explode(' to ', $query['date']);
        $path = $query['path'];
        $response = (new Client())->get($this->mpStatsApi."oz/get/$url/". ($url == 'seller'?'brands':'sellers') ."?d1=$date[0]&d2=$date[1]&path=$path", $this->getHeaders());
        $response = json_decode($response->getBody()->getContents(), true);
        $data = [];
        foreach ($response as $index => $item){
            $data[] = array_merge(['name' => $index], $item);
        }
        return $data;
    }

    public function getApiCategory($query, $url = 'seller')
    {
        $date = explode(' to ', $query['date']);
        $path = $query['path'];
        $response = (new Client())->get($this->mpStatsApi."oz/get/$url/categories?d1=$date[0]&d2=$date[1]&path=$path", $this->getHeaders());
        $response = json_decode($response->getBody()->getContents(), true);
        $data = [];
        foreach ($response as $index => $item){
            $data[] = array_merge(['name' => $index], $item);
        }
        return $data;
    }

    public function findSeller($seller, $query)
    {
        $context = [];
        try{
            $date = key_exists("date", $query)?explode(' to ', $query['date']):null;
            $d1 = $date?$date[0]:(new \DateTime())->modify("- 31 day")->format("Y-m-d");
            $d2 = $date?$date[1]:(new \DateTime())->modify("- 1 day")->format("Y-m-d");
            $body = [
                "startRow" => 0,
                "endRow" => 100,
                "sortModel" => [
                    [
                        "sort" => "desc",
                        "colId" => "revenue"
                    ]
                ]
            ];
            $context['data'] = (new Client())->post($this->mpStatsApi."oz/get/seller?path=$seller&d1=$d1&d2=$d2", $this->getHeadersWithBody($body));
            $context['data'] = json_decode($context['data']->getBody()->getContents(), true)['data'];
            $context['seller'] = $seller;
            $context['d1'] = $d1;
            $context['d2'] = $d2;
        }catch (\Exception $exception){}

        return $context;
    }

    public function getCategory($url = null)
    {
        if($url){
            $sales = $this
                    ->entityManager
                    ->getRepository(CategorySales::class)
                    ->findCategories($url, CategoryEnum::OZON);

            $sales = array_map(function (CategorySales $item){
                $item->setColor(explode(', ', $item->getColor())[0]);
                $item->setGraph(explode(',', $item->getGraph()));
                return $item;
            }, $sales);

            $context = [
                'sales' => $sales,
                'path' => $url
            ];
            return $context;
        }
        $categorys = [];
        $categories = $this
            ->entityManager
            ->getRepository(DataCategory::class)
            ->findBy(['entity' => CategoryEnum::OZON])
        ;
        foreach ($categories as $category) {
            /** @var DataCategory $category */
            $array = explode('/', $category->getPath());
            switch (count($array)) {
                case 2:{
                    if (!in_array($array[0], array_column($categorys, 'name'))) {
                        $categorys[] = [
                            'name' => $array[0],
                            'subjects' => [
                                [
                                    'name' => $array[1],
                                    'path' => "$array[0]/$array[1]",
                                    'subjects' => []
                                ]
                            ]
                        ];
                    } else {
                        $index = array_search($array[0], array_column($categorys, 'name'));
                        $categorys[$index]['subjects'][] = [
                            'name' => $array[1],
                            'path' => "$array[0]/$array[1]",
                            'subjects' => []
                        ];
                    }
                    break;
                }
                case 3:{
                    if (!in_array($array[0], array_column($categorys, 'name'))) {
                        $categorys[] = [
                            'name' => $array[0],
                            'subjects' => [
                                [
                                    'name' => $array[1],
                                    'path' => "$array[0]/$array[1]",
                                    'subjects' => [
                                        [
                                            'name' => $array[2],
                                            'path' => "$array[0]/$array[1]/$array[2]",
                                        ]
                                    ]
                                ]
                            ]
                        ];
                    } else {
                        $index = array_search($array[0], array_column($categorys, 'name'));
                        $subIndex = array_search($array[1], array_column($categorys[$index]['subjects'], 'name'));
                        $categorys[$index]['subjects'][$subIndex]['subjects'][] = [
                            'name' => $array[2],
                            'path' => "$array[0]/$array[1]/$array[2]",
                        ];
                    }

                    break;
                }
            }
        }

//        $stocks = $this
//            ->entityManager
//            ->getRepository(WbDataProperty::class)
//            ->getProperty('wbDataStock', $dataWb['wbData']->getId());
//
//        $category = [];
//        foreach ($stocks as $stock) {
//            $stock = json_decode($stock["property"], true);
//            $data = array_column($category, 'name');
//            if(!in_array($stock["category"], $data)){
//                $category[] = [
//                    "name" => $stock["category"],
//                    "subject" => [$stock["subject"]]
//                ];
//            }else{
//                $index = array_search($stock["category"], $data);
//                $subjects = $category[$index]['subject'];
//                if(!in_array($stock["subject"], $subjects)){
//                    $category[$index]["subject"][] = $stock["subject"];
//                }
//            }
//        }
        $context['categories'] = $categorys;
        return $context;
    }
}
