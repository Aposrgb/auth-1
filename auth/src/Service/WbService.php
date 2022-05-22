<?php

namespace App\Service;

use App\Entity\DataCategory;
use App\Helper\Enum\CategoryEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

class WbService extends AbstractService
{
    public function search($word)
    {
        $client = new Client();
        if(is_numeric($word)){
            if($client->get($this->mpStatsApiWb."item/$word", $this->getHeaders())->getStatusCode() == Response::HTTP_OK){
                return $word;
            }
        }else{
            $word = explode('/', $word)[4]??null;
            if($word && is_numeric($word)){
                if($client->get($this->mpStatsApiWb."item/$word", $this->getHeaders())->getStatusCode() == Response::HTTP_OK){
                    return $word;
                }
            }
        }
        return false;
    }

    public function inSimilar($sku)
    {
        $date = new \DateTime();
        $category = $this->mpStatsApiWb . "in_similar?path=$sku&" . "d2=" . $date->modify('-1 day')->format('Y-m-d') . "&d1=" . $date->modify('-60 day')->format('Y-m-d');
        $client = new Client();
        $sales = json_decode($client->request("GET", $category, $this->getHeaders())->getBody()->getContents(), true)['data'];
        $sales = array_map(function ($item) {
            $item['color'] = (explode(', ', $item['color'])[0]);
            $item['nmId'] = $item['id'];
            $item['position'] = $item['category_position'];
            $item['finalPrice'] = $item['final_price'];
            $item['clientPrice'] = $item['client_price'];
            $item['dayStock'] = $item['days_in_stock'];
            return $item;
        }, $sales);
        $path = json_decode($client->request('GET', $this->mpStatsApiWb . "item/$sku", $this->getHeaders())->getBody()->getContents(), true)['item'];
        $context = [
            'sales' => $sales,
            'sku' => $sku,
            'path' => $path['name']
        ];
        return $context;
    }

    public function similar($sku)
    {
        $date = new \DateTime();
        $category = $this->mpStatsApiWb . "similar?path=$sku&" . "d2=" . $date->modify('-1 day')->format('Y-m-d') . "&d1=" . $date->modify('-60 day')->format('Y-m-d');
        $client = new Client();
        $sales = json_decode($client->request("GET", $category, $this->getHeaders())->getBody()->getContents(), true)['data'];
        $sales = array_map(function ($item) {
            $item['color'] = (explode(', ', $item['color'])[0]);
            $item['nmId'] = $item['id'];
            $item['position'] = $item['category_position'];
            $item['finalPrice'] = $item['final_price'];
            $item['clientPrice'] = $item['client_price'];
            $item['dayStock'] = $item['days_in_stock'];
            return $item;
        }, $sales);
        $path = json_decode($client->request('GET', $this->mpStatsApiWb . "item/$sku", $this->getHeaders())->getBody()->getContents(), true)['item'];
        $context = [
            'sales' => $sales,
            'sku' => $sku,
            'path' => $path['name']
        ];
        return $context;

    }

    public function searchBrand($brand)
    {
        $date = new \DateTime();
        $category = $this->mpStatsApiWb . "brand?path=$brand&" . "d2=" . $date->modify('-1 day')->format('Y-m-d') . "&d1=" . $date->modify('-60 day')->format('Y-m-d');
        $sales = json_decode((new Client())->request("GET", $category, $this->getHeaders())->getBody()->getContents(), true)['data'];
        $sales = array_map(function ($item) {
            $item['color'] = (explode(', ', $item['color'])[0]);
            $item['nmId'] = $item['id'];
            $item['position'] = $item['category_position'];
            $item['finalPrice'] = $item['final_price'];
            $item['clientPrice'] = $item['client_price'];
            $item['dayStock'] = $item['days_in_stock'];
            return $item;
        }, $sales);

        $context = [
            'sales' => $sales,
            'path' => $brand
        ];
        return $context;
    }

    public function getKeywords($sku, $query)
    {
        $context = ['sku' => $sku];
        $client = new Client();
        try{
            $date = $query['date']??null;
            $date = $date?explode(' to ', $date):null;
            $context['d2'] = $date?$date[1]:(new \DateTime())->modify('-1 day')->format('Y-m-d');
            $context['d1'] = $date?$date[0]:(new \DateTime())->modify('-61 day')->format('Y-m-d');
            $data = $client->get($this->mpStatsApi."wb/get/item/$sku/by_keywords?full=true&d1=".$context['d1']."&d2=".$context['d2'], $this->getHeaders());
            $data = json_decode($data->getBody()->getContents(), true);
            $context['date'] = $data['days'];
            $context['words'] = [];
            $pos = [];
            $avg = [];
            foreach ($data['words'] as $name => $word){
                $pos[] = $word['pos'];
                $avg[] = $word['avgPos'];
                $context['words'][] = [
                    'word' => $name,
                    'pos' => $word['pos'],
                    'count' => $word['count'],
                    'total' => $word['total'],
                    'avgPos' => $word['avgPos']
                ];
            }
            $data = $client->get($this->mpStatsApi."wb/get/item/$sku", $this->getHeaders())->getBody()->getContents();
            $context['item'] = json_decode($data, true)['item'];
        }catch (Exception $exception){}
        return $context;
    }

    public function getOrderByRegion($sku, $query)
    {
        $context = ['sku' => $sku];
        $client = new Client();
        try{
            $date = $query['date']??null;
            $date = $date?explode(' to ', $date):null;
            $context['d2'] = $date?$date[1]:(new \DateTime())->modify('-1 day')->format('Y-m-d');
            $context['d1'] = $date?$date[0]:(new \DateTime())->modify('-61 day')->format('Y-m-d');
            $data = $client->get($this->mpStatsApi."wb/get/item/$sku/orders_by_region?d1=".$context['d1']."&d2=".$context['d2'], $this->getHeaders());
            $data = json_decode($data->getBody()->getContents(), true);
            $context['data'] = [];
            foreach ($data as $key => $value){
                $keys = [];
                $arrKeys = array_keys($value);
                $arrValues = array_values($value);
                for ($i=0;$i<count($arrKeys);$i++){
                    $keys[] = [
                        "name" => $arrKeys[$i],
                        "value" => $arrValues[$i]
                    ];
                }
                $keys = (new ArrayCollection($keys))
                    ->matching(Criteria::create()->orderBy(['name' => Criteria::ASC]))
                    ->getValues()
                ;
                $context['data'][] = [
                    'date' => date_create($key)->format('d.m'),
                    'keys' => array_map(function ($item){return $item['name'];}, $keys),
                    'value' => array_map(function ($item){return $item['value'];}, $keys)
                ];
            }
            $data = $client->get($this->mpStatsApi."wb/get/item/$sku", $this->getHeaders())->getBody()->getContents();
            $context['item'] = json_decode($data, true)['item'];
        }catch (Exception $exception){}
        return $context;
    }

    public function getItem($sku, $query)
    {
        $context = ['sku' => $sku];
        $client = new Client();
        try {
            $date = $query['date']??null;
            $date = $date?explode(' to ', $date):null;
            $context['d2'] = $date?$date[1]:(new \DateTime())->modify('-1 day')->format('Y-m-d');
            $context['d1'] = $date?$date[0]:(new \DateTime())->modify('-61 day')->format('Y-m-d');
            $getUrl = function ($url, $isOneDate) use ($sku, $context) {
                return ($this->mpStatsApiWb . "item/" . $sku . "$url?") . (!$isOneDate ?
                        "d2=" . $context['d2'] . "&d1=" . $context['d1'] :
                        "d=" . $context['d2']);
            };
            $requestToArray = function ($url, $bool = false) use ($client, $getUrl) {
                return json_decode(
                    $client
                        ->get($getUrl($url, $bool), $this->getHeaders())
                        ->getBody()
                        ->getContents(),
                    true);
            };
            $context['sales'] = $requestToArray('/sales')[0];
            $context['sales']['category'] = $query['name']??'';
            $context['item'] = $requestToArray('');
            $context['photos'] = $context['item']['photos'];
            $context['item'] = $context['item']['item'];
            $context['similar'] = $requestToArray('/similar');
            $context['balance_by_region'] = $requestToArray('/balance_by_region', true);
            $context['balance_by_size'] = $requestToArray('/balance_by_size', true);
            $context['sales_by_region'] = $requestToArray('/sales_by_region');
            $context['sales_by_size'] = $requestToArray('/sales_by_size');
            $context['result'] = 0;
            $context['summa'] = 0;
            $context['count'] = 0;
            $byKeywords = $requestToArray('/by_keywords');
            $context['keywords'] = [];
            $context['days'] = array_splice($byKeywords['days'], count($byKeywords['days']) / 2);
            foreach (array_keys($byKeywords['words']) as $word) {
                $context['keywords'][] = [
                    'name' => $word,
                    'pos' => array_splice($byKeywords['words'][$word]['pos'], count($byKeywords['words'][$word]['pos']) / 2 + 1),
                    'count' => $byKeywords['words'][$word]['count'],
                    'total' => $byKeywords['words'][$word]['total'],
                    'avgPos' => $byKeywords['words'][$word]['avgPos']
                ];
            }
            for ($i = 0; $i < count($byKeywords['sales']); $i++) {
                $context['by_keywords'][] = [
                    'sale' => $byKeywords['sales'][$i],
                    'day' => $byKeywords['days'][$i],
                    'balance' => $byKeywords['balance'][$i],
                    'final_price' => $byKeywords['final_price'][$i],
                    'client_price' => (int)($byKeywords['final_price'][$i] * 100 / (100 - $context['sales']['discount'])),
                    'summa' => $byKeywords['sales'][$i] * $byKeywords['final_price'][$i]
                ];
                $context['result'] += $byKeywords['sales'][$i];
                $context['summa'] += $byKeywords['sales'][$i] * $byKeywords['final_price'][$i];
                if ($byKeywords['sales'][$i] != 0) $context['count']++;
            }
            $context['average'] = (int)($context['result'] / count($byKeywords['sales']));
            $context['summa_average'] = (int)($context['summa'] / count($byKeywords['sales']));
            $context['by_keywords'] = array_reverse($context['by_keywords']);

        } catch (Exception $exception) {
        }
        return $context;
    }

    public function getCategory($url = null)
    {
        if ($url) {
            $date = new \DateTime();
            $category = $this->mpStatsApiWb . "category?path=$url&" . "d2=" . $date->modify('-1 day')->format('Y-m-d') . "&d1=" . $date->modify('-60 day')->format('Y-m-d');
            $sales = json_decode((new Client())->get($category, $this->getHeaders())->getBody()->getContents(), true)['data'];
            $sales = array_map(function ($item) {
                $item['color'] = (explode(', ', $item['color'])[0]);
                $item['nmId'] = $item['id'];
                $item['position'] = $item['category_position'];
                $item['finalPrice'] = $item['final_price'];
                $item['clientPrice'] = $item['client_price'];
                $item['dayStock'] = $item['days_in_stock'];
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
            ->findBy(['entity' => CategoryEnum::WB]);
        foreach ($categories as $category) {
            /** @var DataCategory $category */
            $array = explode('/', $category->getPath());
            switch (count($array)) {
                case 2:
                {
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
                case 3:
                {
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
//        var_export($categorys);
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
