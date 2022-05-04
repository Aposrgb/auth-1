<?php

namespace App\Service;

use App\Entity\ApiToken;
use App\Entity\WbDataEntity\WbDataProperty;
use Doctrine\Common\Collections\ArrayCollection;

class CabinetWbService extends AbstractService
{
    private function checkStatusToken($id, $query)
    {
        $tokens = $this
            ->entityManager
            ->getRepository(ApiToken::class)
            ->getTokenWithUser($id, true);

        $token = !key_exists('index', $query) ? ($tokens[0] ?? null) : $tokens[$query['index']];
        return $token ?
            ['wbData' => $token->getWbData(), 'token' => $token, 'tokens' => $tokens]:
            ['wbData' => null, 'token'=> null];
    }

    public function getWarehouses($id, $query)
    {
        $dataWb = $this->checkStatusToken($id, $query);
        $context['token'] = $dataWb['token'];

        if(!$dataWb['wbData']){
            $context["processing"] = true;
            return $context;
        }

        $repos = $this->entityManager->getRepository(WbDataProperty::class);
        $arrayPropNames = ["wbDataStock"];
        $arrayNames = ["stocks"];
        $data = [];
        $city = [];
        for ($i=0;$i<count($arrayPropNames);$i++){
            $data[$arrayNames[$i]] = $repos->getProperty($arrayPropNames[$i], $dataWb['wbData']->getId());
        }

        $data["stock"] = $data["stocks"];
        $data["stocks"] = [];
        foreach ($data["stock"] as $stock){
            $array = json_decode($stock["property"], true);
            $array["img"] = ((int)($array["nmId"]/10000))*10000;
            if(!in_array($array['warehouseName'], array_column($city,'name'))){
                $city[] = ['name' => $array['warehouseName']];
            }
            $isAdd = true;
            $i=0;
            foreach ($data["stocks"] as $stok){
                if($array['nmId'] == $stok['nmId'] && $array['warehouseName'] == $stok['warehouseName']){
                    $data["stocks"][$i] = $array;
                    $isAdd = false;
                }
                $i++;
            }
            if($isAdd){
                $data["stocks"][] = $array;
            }
        }
        foreach ($city as $item){
            $data["stocks"] = array_map(
                function ($items) use ($item){
                    $city = ($items['warehouseName'] == $item['name'])?$items['quantityFull']:0;
                    $items['cities'][] = $city;
                    $items['cityResult'] = ($items['cityResult']??0)+$city;
                    return $items;
            }, $data["stocks"]);
        }
        $data["stock"] = [];
        $context["cities"] = $city;
        $context["count"] = count($data['stocks']);
        $context["tokens"] = $dataWb['tokens'] instanceof ApiToken ?[$dataWb['tokens']]:$dataWb['tokens'];
        return array_merge($context, $data);
    }

    public function getProducts($id, $query)
    {
        $dataWb = $this->checkStatusToken($id, $query);
        $context['token'] = $dataWb['token'];

        if(!$dataWb['wbData']){
            $context["processing"] = true;
            return $context;
        }

        $repos = $this->entityManager->getRepository(WbDataProperty::class);
        $arrayPropNames = ["wbDataOrder"];
        $arrayNames = ["orders"];
        $data = [];

        for ($i=0;$i<count($arrayPropNames);$i++){
            $data[$arrayNames[$i]] = $repos->getProperty($arrayPropNames[$i], $dataWb['wbData']->getId());
        }
        foreach (array_keys($data) as $datas) {
            $data[$datas] = array_map(function ($item) {
                $array = json_decode($item["property"], true);
                $array["img"] = ((int)($array["nmId"]/10000))*10000;
                return $array;
            }, $data[$datas]);
        }
        $context["tokens"] = $dataWb['tokens'] instanceof ApiToken ?[$dataWb['tokens']]:$dataWb['tokens'];
        return array_merge($context, $data);
    }

    public function getOrders($id, $query)
    {
        $dataWb = $this->checkStatusToken($id, $query);
        $context['token'] = $dataWb['token'];

        if(!$dataWb['wbData']){
            $context["processing"] = true;
            return $context;
        }

        $repos = $this->entityManager->getRepository(WbDataProperty::class);
        $arrayPropNames = ["wbDataOrder"];
        $arrayNames = ["orders"];
        $data = [];

        for ($i=0;$i<count($arrayPropNames);$i++){
            $data[$arrayNames[$i]] = $repos->getProperty($arrayPropNames[$i], $dataWb['wbData']->getId());
        }
        foreach (array_keys($data) as $datas) {
            $data[$datas] = array_map(function ($item) {
                $array = json_decode($item["property"], true);
                $array["img"] = ((int)($array["nmId"]/10000))*10000;
                return $array;
            }, $data[$datas]);
        }
        $context["tokens"] = $dataWb['tokens'] instanceof ApiToken ?[$dataWb['tokens']]:$dataWb['tokens'];
        return array_merge($context, $data);
    }

    public function deleteApiToken(ApiToken $token)
    {
        $apiTokens = $this
            ->entityManager
            ->getRepository(ApiToken::class)
            ->getTokenWithWbData($token->getToken(), false);

        if(count($apiTokens) == 1){
            $wbData = $token->getWbData();
            if($wbData){
                $this->entityManager->remove($wbData);
                $this->entityManager->getRepository(WbDataProperty::class)->removeAllProp($wbData->getId());
            }
        }

        $this->entityManager->remove($token);
        $this->entityManager->flush();
    }

    public function addApiToken($user, $name, $key)
    {
        $error = '';
        if(!$key || !$name){
            $error = "Не заполнено поле";
        }else if($key and $name){
            $token = $this
                ->entityManager
                ->getRepository(ApiToken::class)
                ->findBy([
                    'name' => $name,
                    'apiUser' => $user->getId(),
                    'token' => $key
                ]);
            if($token){
                $error = "Уже есть такой токен";
            }else{
                $user->addApiToken((new ApiToken())
                    ->setApiUser($user)
                    ->setName($name)
                    ->setToken($key)
                );
                $this->entityManager->flush();
                shell_exec("php ../bin/console wb:data:processing $key ".$user->getId()." > /dev/null &");
            }
        }
        return $error;
    }

    public function getWbData($id, $query)
    {
        $dataWb = $this->checkStatusToken($id, $query);
        $context['token'] = $dataWb['token'];

        if(!$dataWb['wbData']){
            $context["processing"] = true;
            return $context;
        }

        $repos = $this->entityManager->getRepository(WbDataProperty::class);
        $arrayPropNames = ["wbDataSale", "wbDataIncome","wbDataOrder", "wbDataStock", "wbDataReport", "wbDataExcise"];
        $arrayNames = ["sales","incomes","orders","stocks","reports","excise"];
        $data = [];

        for ($i=0;$i<count($arrayPropNames);$i++){
            $data[$arrayNames[$i]] = $repos->getProperty($arrayPropNames[$i], $dataWb['wbData']->getId());
        }
        $context["tokens"] = $dataWb['tokens'] instanceof ApiToken ?[$dataWb['tokens']]:$dataWb['tokens'];
        $context["sales"] = $this->salesOnDay($data);
        return array_merge(
            $context,
            $this->sales($data["sales"]),
            $this->orders($data["orders"]),
            $this->stocks($data["stocks"])
        );
    }

    private function salesOnDay($datas)
    {
        $data = [];
        $date = new \DateTime();
        $sales = $datas["sales"];
        $orders = $datas["orders"];
        for($i=0;$i<28;$i++){
            $data[$i] = [
                'date' => $date->format('d.m.Y'),
                'salesQ' => 0,
                'rubS' => 0,
                'profitS' => 0,
                'costPriceS' => 0,
                'commissionS' => 0,
                'orderQ' => 0,
                'rubOr' => 0,
                'returnQ' => 0,
                'rubRet' => 0,
                'logisticToC' => 0,
                'logisticFromC' => 0,
                'fine' => 0,
            ];
            foreach ($sales as $sale){
                $array = json_decode($sale["property"], true);
                if($array["quantity"] == 0) continue;
                $dateSale = (new \DateTime($array['date']))->format('d.m.Y');
                $dateSale = explode('.',$dateSale);
                $dateFormat = explode('.', $date->format('d.m.Y'));
                $isDate = true;
                for($j=0;$j<3;$j++){
                    $isDate = $isDate && ($dateSale[$j] == $dateFormat[$j]);
                }
                if(!$isDate) continue;
                if($array["quantity"] < 0){
                    $data[$i]["rubRet"] += $array["priceWithDisc"];
                    $data[$i]["returnQ"] += -$array["quantity"];
                }
                $data[$i]['salesQ'] += $array["quantity"];
                $rub = $array["finishedPrice"]*$array["quantity"];
                $data[$i]['rubS'] += $rub;
                $comm = (($rub) - ($array["forPay"]*$array["quantity"])) ;
                $data[$i]['profitS'] += ($rub) - $comm;
                $data[$i]['commissionS'] += $comm;
            }
            foreach ($orders as $order){
                $array = json_decode($order["property"], true);
                if($array["quantity"] == 0) continue;
                $dateSale = (new \DateTime($array['date']))->format('d.m.Y');
                $dateSale = explode('.',$dateSale);
                $dateFormat = explode('.', $date->format('d.m.Y'));
                $isDate = true;
                for($j=0;$j<3;$j++){
                    $isDate = $isDate && ($dateSale[$j] == $dateFormat[$j]);
                }
                if(!$isDate) continue;
                $data[$i]['orderQ'] += $array["quantity"];
                $data[$i]['rubOr'] += $array["totalPrice"];
            }
            $date->modify("-1 day");
        }
        return $data;
    }

    private function sales($sales)
    {
        $data["summaPrice"] = 0;
        $data["summaLength"] = 0;
        $data["summaProfit"] = 0;
        $data["summaComm"] = 0;
        $data["returnedLength"] = 0;
        $data["returnedPrice"] = 0;
        $data["rent"] = 0;
        $data["mardj"] = 0;
        foreach ($sales as $sale){
            $array = json_decode($sale["property"], true);
            if($array["quantity"] == 0) continue;
            if($array["quantity"] < 0){
                $data["returnedPrice"] += $array["priceWithDisc"];
                $data["returnedLength"] += $array["quantity"];
            }
            $data["summaPrice"] += $array["priceWithDisc"]*$array["quantity"];
            $data["summaLength"] += $array["quantity"];
            $data["summaProfit"] += $array["finishedPrice"]*$array["quantity"];
            $data["summaComm"] += ($array["finishedPrice"]*$array["quantity"]) - ($array["forPay"]*$array["quantity"]);
            $data["rent"] = $array["forPay"]/($array["totalPrice"]>0?$array["totalPrice"]:1) * 100;
            $data["mardj"] = ($array["totalPrice"] - $array["forPay"]) / ($array["totalPrice"]>0?$array["totalPrice"]:1) * 100;
        }
        $data["rent"] = $data["rent"] > 0 && $data["rent"] < 100? $data["rent"] : 28;
        $data["mardj"] = $data["mardj"] > 0 && $data["mardj"] < 100? $data["mardj"] : 52;
        return $data;
    }
    private function orders($orders)
    {
        $data["ordersPrice"] = 0;
        $data["ordersLength"] = 0;
        foreach ($orders as $order){
            $array = json_decode($order["property"], true);
            if($array["quantity"] == 0) continue;
            $data["ordersPrice"] += ($array["totalPrice"]*$array["quantity"]);
            $data["ordersLength"] += $array["quantity"];
        }
        return $data;
    }
    private function stocks($stocks)
    {
        $data["costPrice"] = 0;
        $data["retailPrice"] = 0;
        foreach ($stocks as $stock){
            $array = json_decode($stock["property"], true);
            if($array["quantity"] == 0) continue;
            $data["costPrice"] += ($array["Price"]*$array["quantity"]*$array["Discount"])/100;
            $data["retailPrice"] += ($array["Price"]*$array["quantity"]);
        }
        return $data;
    }
}
