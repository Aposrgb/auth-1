<?php

namespace App\Service;

use GuzzleHttp\Client;

class SeoService extends AbstractService
{
    public function selection($array)
    {
        $data = [];
        try{
            $data = json_decode((new Client())->get($this->mpStatsApi . "seo/keywords/selection/ranges?", $this->getHeaders())->getBody()->getContents(), true);
            $data['word'] = $array['word'] ?? '';
            $setFilter = function ($filter, $filterTo) {
                return [
                    "filterType" => "number",
                    "type" => "inRange",
                    "filter" => $filter,
                    "filterTo" => $filterTo
                ];
            };
            $checkFilter = function ($name1) use ($data, $array, $setFilter) {
                $min = ($array[$name1 . 'Min'] ?? '') == '' ? $data[$name1 . '_min'] : $array[$name1 . 'Min'];
                $max = ($array[$name1 . 'Max'] ?? '') == '' ? $data[$name1 . '_max'] : $array[$name1 . 'Max'];
                return $setFilter($min, $max);
            };
            $setBody = function ($name1) use ($data, $array) {
                return [
                    'min' => ($array[$name1 . 'Min'] ?? '') == '' ? $data[$name1 . '_min'] : $array[$name1 . 'Min'],
                    'max' => ($array[$name1 . 'Max'] ?? '') == '' ? $data[$name1 . '_max'] : $array[$name1 . 'Max']
                ];
            };
            $filters = [];
            $filters['word'] = [
                "filterType" => "text",
                "type" => "contains",
                "filter" => $array['word'] ?? ''
            ];
            $arrayNames = [
                "count",
                "brands",
                "brands_with_sells",
                "results",
                "sales",
                "sellers",
                "sellers_with_sells",
                "items",
                "final_price_average",
                "final_price_min",
                "final_price_max",
                "price_median",
                "revenue",
                "product_revenue",
                "items_with_sells",
                "items_with_sells_percent",
                "sellers_with_sells_percent",
                "brands_with_sells_percent"
            ];
            foreach ($arrayNames as $arrayName) {
                $filters[$arrayName] = $checkFilter($arrayName);
                $check = $setBody($arrayName);
                $data[$arrayName . "_min"] = $check['min'];
                $data[$arrayName . "_max"] = $check['max'];
            }
            $body = [
                "startRow" => 0,
                "endRow" => 300,
                "filterModel" => $filters,
                "sortModel" => [["sort" => "desc", "colId" => "revenue"]]
            ];

            $data['sales'] = json_decode((new Client())->post($this->mpStatsApi . "seo/keywords/selection?", $this->getHeadersWithBody($body))->getBody()->getContents(), true)['data'];
            $data['sales'] = array_map(function ($item) {
                $item["items_with_sells_percent"] = (int)$item["items_with_sells_percent"];
                $item["brands_with_sells_percent"] = (int)$item["brands_with_sells_percent"];
                $item["sellers_with_sells_percent"] = (int)$item["sellers_with_sells_percent"];
                return $item;
            }, $data['sales']);
        }
        catch(\Exception $e){}
        return $data;
    }

    public function compareResult($a, $b)
    {
        $a = is_numeric($a)?$a:(explode('/', $a)[4]?? null);
        $b = is_numeric($b)?$b:(explode('/', $b)[4]?? null);
        if(!$a or !$b) return false;
        $client = new Client();
        $data = json_decode($client->get("https://mpstats.io/api/seo/tools/wb-sku-compare?groupA=$a&groupB=$b", $this->getHeaders())->getBody()->getContents(), true);
        try {
            $data['items'][] = json_decode($client->get($this->mpStatsApiWb . "item/$a", $this->getHeaders())->getBody()->getContents(), true);
            $data['items'][] = json_decode($client->get($this->mpStatsApiWb . "item/$b", $this->getHeaders())->getBody()->getContents(), true);
        }catch (\Exception $ex){}
        return $data;
    }

    public function getSkuPost($sku, $query)
    {
        $data = $this->getSku($sku);
        if (strlen($query['seoInput'] > 0)) {
            $data['item']['seoText'] = $query['seoInput'];
            $data['sku'] = json_decode((new Client())->post($this->mpStatsApi . "seo/tools/wb-card-checker?sku=$sku", $this->getHeadersWithBody(['item' => $data['item']]))->getBody()->getContents(), true);
        }
        return $data;
    }

    public function getSku($sku)
    {
        $client = new Client();
        $data = [];
        if (is_numeric($sku)) {
            $data = json_decode($client->get($this->mpStatsApi . "seo/tools/wb-card-checker?sku=$sku", $this->getHeaders())->getBody()->getContents(), true);
            try {
                $data['img'] = json_decode($client->get($this->mpStatsApiWb . "item/$sku", $this->getHeaders())->getBody()->getContents(), true)['photos'][0];
            } catch (\Exception $exception) {
            }
        } else {
            $sku = explode('/', $sku)[4] ?? null;
            if ($sku && is_numeric($sku)) {
                $data = json_decode($client->get($this->mpStatsApi . "seo/tools/wb-card-checker?sku=$sku", $this->getHeaders())->getBody()->getContents(), true);
                try {
                    $data['img'] = json_decode($client->get($this->mpStatsApiWb . "item/$sku", $this->getHeaders())->getBody()->getContents(), true)['photos'][0];
                } catch (\Exception $exception) {
                }
            }
        }
        return $data;
    }

    public function getPosition($sku)
    {
        $client = new Client();
        $data = $this->getKeywordIdentity($sku);
        if ($data) {
            $date = (new \DateTime());
            $data['dates'] = [];
            for ($i = 0; $i < 30; $i++) {
                $data['dates'][] = $date->modify("-1 day")->format('d.m');
            }
            $data['words'] = array_splice($data['words'], 0, 25);
            $data['words'] = array_map(
                function ($item) use ($client) {
                    try {
                        $item['results'] = json_decode(
                            $client
                                ->get($this->mpStatsApi . "seo/keywords/" . $item['word'], $this->getHeaders())
                                ->getBody()
                                ->getContents(),
                            true)['results'];
                    } catch (\Exception $exception) {
                    }
                    return $item;
                },
                $data['words']);
        }
        return $data;
    }

    public function getKeywordWord($word)
    {
        $client = new Client();
        $date = (new \DateTime())->modify("-1 day");
        $body = [
            "query" => $word,
            "type" => "word",
            "d2" => $date->format('Y-m-d'),
            "d1" => $date->modify('-29 day')->format('Y-m-d'),
        ];
        return json_decode(
            $client
                ->post($this->mpStatsApi . "seo/keywords/expanding", $this->getHeadersWithBody($body))
                ->getBody()
                ->getContents(),
            true
        );
    }

    public function getKeywordKey($key)
    {
        $client = new Client();
        $date = (new \DateTime())->modify("-1 day");
        $body = [
            "query" => $key,
            "type" => "keyword",
            "d2" => $date->format('Y-m-d'),
            "d1" => $date->modify('-29 day')->format('Y-m-d'),
        ];
        return json_decode(
            $client
                ->post($this->mpStatsApi . "seo/keywords/expanding", $this->getHeadersWithBody($body))
                ->getBody()
                ->getContents(),
            true
        );
    }

    public function getKeywordIdentity($identity, $query=[])
    {
        $client = new Client();
        $date = $query['date']??null;
        $date = $date?explode(' to ', $date):null;
        $date = $date??(new \DateTime())->modify("-1 day");
        $body = [
            "type" => "sku",
            "d2" => !($date instanceof \DateTime) ?$date[1]:$date->format('Y-m-d'),
            "d1" => !($date instanceof \DateTime) ?$date[0]:$date->modify('-29 day')->format('Y-m-d'),
        ];
        $data = [];
        if (is_numeric($identity)) {
            $body["query"] = $identity;
            $data = json_decode($client->post($this->mpStatsApi . "seo/keywords/expanding", $this->getHeadersWithBody($body))->getBody()->getContents(), true);
            try {
                $data['img'] = json_decode($client->get($this->mpStatsApiWb . "item/$identity", $this->getHeaders())->getBody()->getContents(), true)['photos'][0];
            } catch (\Exception$exception) {
            }
            $data['d1'] = $body['d1'];
            $data['d2'] = $body['d2'];
        } else {
            $identity = explode('/', $identity)[4] ?? null;
            if ($identity && is_numeric($identity)) {
                $body["query"] = $identity;
                $data = json_decode($client->post($this->mpStatsApi . "seo/keywords/expanding", $this->getHeadersWithBody($body))->getBody()->getContents(), true);
                try {
                    $data['img'] = json_decode($client->get($this->mpStatsApiWb . "item/$identity", $this->getHeaders())->getBody()->getContents(), true)['photos'][0];
                } catch (\Exception$exception) {
                }
                $data['d1'] = $body['d1'];
                $data['d2'] = $body['d2'];
            }
        }
        return $data;
    }

    public function getKeyword($name)
    {
        $client = new Client();
        $context = json_decode($client->get($this->mpStatsApi . "seo/keywords/$name", $this->getHeaders())->getBody()->getContents(), true);
        $context = array_map(function ($item) {
            return (int)$item;
        }, $context);
        $context['keywords'] = json_decode($client->get($this->mpStatsApi . "seo/keywords/$name/serp", $this->getHeaders())->getBody()->getContents(), true)["items"];
        $date = (new \DateTime())->modify("-30 day");
        for ($i = 0; $i < 30; $i++) {
            $context['dates'][] = $date->format('d.m');
            $date->modify("+1 day");
        }
        return $context;
    }
}
