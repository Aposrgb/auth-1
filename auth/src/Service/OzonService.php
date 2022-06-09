<?php

namespace App\Service;

use App\Entity\CategorySales;
use App\Entity\DataCategory;
use App\Helper\Enum\CategoryEnum;
use GuzzleHttp\Client;

class OzonService extends AbstractService
{
    public function getApiPrcSegm($query)
    {
        $date = explode(' to ', $query['date']);
        $path = $query['path'];
        $max = "maxPrice=".($query['max']??'');
        $min = "minPrice=".($query['min']??'');
        $segm = "segmentsCnt=".($query['prcSegm']??25);
        $response = (new Client())->get($this->mpStatsApi."oz/get/seller/price_segmentation?d1=$date[0]&d2=$date[1]&path=$path&$max&$min&$segm", $this->getHeaders());
        $response = json_decode($response->getBody()->getContents(), true);
        return [
            'data' => $response,
            'min' => min(array_map(function ($item){return $item['min_range_price'];}, $response)),
            'max' => max(array_map(function ($item){return $item['max_range_price'];}, $response)),
            'prcSegm' => $query['prcSegm']??25
        ];
    }

    public function getApiOnDay($query)
    {
        $date = explode(' to ', $query['date']);
        $path = $query['path'];
        $response = (new Client())->get($this->mpStatsApi."oz/get/seller/by_date?d1=$date[0]&d2=$date[1]&path=$path", $this->getHeaders());
        $response = json_decode($response->getBody()->getContents(), true);
        $data = [];
        foreach ($response as $index => $item){
            $data[] = array_merge(['name' => $index], $item);
        }
        return $data;
    }

    public function getApiBrands($query)
    {
        $date = explode(' to ', $query['date']);
        $path = $query['path'];
        $response = (new Client())->get($this->mpStatsApi."oz/get/seller/brands?d1=$date[0]&d2=$date[1]&path=$path", $this->getHeaders());
        $response = json_decode($response->getBody()->getContents(), true);
        $data = [];
        foreach ($response as $index => $item){
            $data[] = array_merge(['name' => $index], $item);
        }
        return $data;
    }

    public function getApiCategory($query)
    {
        $date = explode(' to ', $query['date']);
        $path = $query['path'];
        $response = (new Client())->get($this->mpStatsApi."oz/get/seller/categories?d1=$date[0]&d2=$date[1]&path=$path", $this->getHeaders());
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
