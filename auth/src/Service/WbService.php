<?php

namespace App\Service;

use App\Entity\DataCategory;
use App\Helper\Enum\CategoryEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

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
            $fbs = $query['fbs']??0;
            $date = $date?explode(' to ', $date):null;
            $context['d2'] = $date?$date[1]:(new \DateTime())->modify('-1 day')->format('Y-m-d');
            $context['d1'] = $date?$date[0]:(new \DateTime())->modify('-61 day')->format('Y-m-d');
            $getUrl = function ($url, $isOneDate) use ($sku, $context, $fbs) {
                return ($this->mpStatsApiWb . "item/" . $sku . "$url?fbs=$fbs&") . (!$isOneDate ?
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
            $sales = $requestToArray('/sales');
            $context['sales'] = $sales[0];
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
            $context['days'] = $byKeywords['days'];
            foreach (array_keys($byKeywords['words']) as $word) {
                $context['keywords'][] = [
                    'name' => $word,
                    'pos' => array_splice($byKeywords['words'][$word]['pos'], count($byKeywords['words'][$word]['pos']) / 2 + 1),
                    'count' => $byKeywords['words'][$word]['count'],
                    'total' => $byKeywords['words'][$word]['total'],
                    'avgPos' => $byKeywords['words'][$word]['avgPos']
                ];
            }
            $context['by_keywords'] = [];
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
            $context['fbs'] = $fbs;
            $context['salesG'] = [];
            $context['balanceG'] = [];
            $context['priceG'] = [];
            $context['summaG'] = [];
            $context['keyG'] = [];
            $context['posG'] = [];
            $context['catG'] = [];
            $context['dayG'] = [];
            foreach (array_reverse($context['by_keywords']) as $sale){
                $context['salesG'][] = $sale['sale'];
                $context['balanceG'][] = $sale['balance'];
                $context['priceG'][] = $sale['final_price'];
                $context['summaG'][] = $sale['summa'];
                $context['dayG'][] = $sale['day'];
            }
            foreach (array_reverse($sales) as $item){
                $context['keyG'][] = $item['visibility'];
                $context['posG'][] = $item['position'];
                $context['catG'][] = (int)($item['categories_cnt']??0);
            }
            $context['graphic1'][0] = [];
            $context['graphic1'][1] = [];
            $context['graphic2'][0] = [];
            $context['graphic2'][1] = [];
            foreach ($context['balance_by_region'] as $item){
                $context['graphic1'][0][] = $item["store"];
                $context['graphic1'][1][] = $item["balance"];
            }
            foreach ($context['sales_by_region'] as $item){
                $context['graphic2'][0][] = $item["store"];
                $context['graphic2'][1][] = $item["sales"];
            }
        } catch (Exception $exception) {
        }
        return $context;
    }

    public function getCategory($url, $query)
    {
        if ($url) {
            if(!key_exists('date', $query)){
                $d1= (new \DateTime())->modify('-1 day')->format('Y-m-d');
                $d2= (new \DateTime())->modify('-60 day')->format('Y-m-d');
            }else{
                $date = explode(' to ', $query['date']);
                $d1= $date[0];
                $d2= $date[1];
            }
            $fbs = $query['fbs']??0;
            $category = $this->mpStatsApiWb . "category?path=$url&" . "d2=" . $d2 . "&d1=" . $d1 . "&fbs=".$fbs;
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
                'path' => $url,
                'd1' => $d1,
                'd2' => $d2,
                'fbs' => $fbs
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
