<?php

namespace App\Service;

use App\Entity\DataCategory;
use App\Entity\Token;
use App\Helper\Enum\CategoryEnum;
use Exception;
use GuzzleHttp\Client;

class WbService extends AbstractService
{
    public function getItem($sku, $category)
    {
        $context = ['sku' => $sku];
        $token = $this->entityManager->getRepository(Token::class)->findAll()[0]??null;
        $headers = [
            'headers' => ['X-Mpstats-TOKEN' => $token?$token->getToken():$token]
        ];
        $client = new Client();
        try {
            $context['d2'] = (new \DateTime())->modify('-1 day')->format('Y-m-d');
            $context['d1'] = (new \DateTime())->modify('-61 day')->format('Y-m-d');
            $getUrl = function ($url, $isOneDate) use ($sku, $context) {
                return ($this->mpStatsApiWb . "item/" . $sku . "$url?") . (!$isOneDate ?
                        "d2=" . $context['d2'] . "&d1=" . $context['d1'] :
                        "d=" . $context['d2']);
            };
            $requestToArray = function ($url, $bool = false) use ($client, $getUrl, $headers) {
                return json_decode(
                    $client
                        ->get($getUrl($url, $bool), $headers)
                        ->getBody()
                        ->getContents(),
                    true);
            };
            $context['sales'] = $requestToArray('/sales')[0];
            $context['sales']['category'] = $category;
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
            $context['days'] = array_splice($byKeywords['days'], count($byKeywords['days'])/2);
            foreach(array_keys($byKeywords['words']) as $word){
                $context['keywords'][] = [
                    'name'=>$word,
                    'pos' => array_splice($byKeywords['words'][$word]['pos'], count($byKeywords['words'][$word]['pos'])/2+1),
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
                if($byKeywords['sales'][$i]!=0) $context['count']++;
            }
            $context['average'] = (int)($context['result']/count($byKeywords['sales']));
            $context['summa_average'] = (int)($context['summa']/count($byKeywords['sales']));
            $context['by_keywords'] = array_reverse($context['by_keywords']);

        } catch (Exception $exception) {
        }
        return $context;
    }

    public function getCategory($url = null)
    {
        if ($url) {
            $token = $this->entityManager->getRepository(Token::class)->findAll()[0]??null;
            $headers = [
                'headers' => ['X-Mpstats-TOKEN' => $token?$token->getToken():$token]
            ];
            $date = new \DateTime();
            $category = $this->mpStatsApiWb . "category?path=$url&" . "d2=" . $date->modify('-1 day')->format('Y-m-d') . "&d1=" . $date->modify('-60 day')->format('Y-m-d');
            $sales = json_decode((new Client())->request("GET", $category, $headers)->getBody()->getContents(), true)['data'];
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
