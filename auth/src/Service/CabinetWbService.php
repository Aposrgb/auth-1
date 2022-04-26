<?php

namespace App\Service;

use App\Entity\ApiToken;
use App\Entity\WbDataEntity\WbData;
use App\Entity\WbDataEntity\WbDataProperty;
use Doctrine\Common\Collections\ArrayCollection;

class CabinetWbService extends AbstractService
{
    public function getOrders($token)
    {
        if(!$token) return ["token" => null];
        $context = ['token' => true];

        $wbData = $this
            ->entityManager
            ->getRepository(WbData::class)
            ->findOneBy(['apiToken' => $token->getId()])
        ;

        if(!$wbData) return  ["processing" => true];

        $repos = $this->entityManager->getRepository(WbDataProperty::class);
        $arrayPropNames = ["wbDataOrder"];
        $arrayNames = ["orders"];
        $data = [];

        for ($i=0;$i<count($arrayPropNames);$i++){
            $data[$arrayNames[$i]] = $repos->getProperty($arrayPropNames[$i], $wbData->getId());
        }

        foreach ($data as $datas) {
            $data[array_search($datas, $data)] = array_map(function ($item) {
                $array = json_decode($item["property"], true);
                $img = ((int)($array["nmId"]/10000))*10000;
                return array_merge(
                    $array, ["img" => $img]
                );
            }, $datas);
        }

        return array_merge($context, $data);
    }

    public function addApiToken($user, $name, $key)
    {
        $error = '';
        if(!$key || !$name){
            $error = "Не заполнено поле";
        }else if($key and $name){
            $repos = $this->entityManager->getRepository(ApiToken::class);
            $token = $repos->findBy(['name' => $name, 'apiUser' => $user->getId()]);
            $token = $token || $repos->findBy(['token' => $key]);
            if($token){
                $error = "Уже есть такой токен";
            }else{
                $user->addApiToken((new ApiToken())
                    ->setApiUser($user)
                    ->setName($name)
                    ->setToken($key)
                );
                $this->entityManager->flush();
                shell_exec("php ../bin/console wb:data:processing $key > /dev/null &");
            }
        }
        return $error;
    }

    public function getTest($token)
    {
        $wbData = $token?
            $this
                ->entityManager
                ->getRepository(WbData::class)
                ->findOneBy(['apiToken' => $token->getId()])
            :null
        ;
        $repos = $this->entityManager->getRepository(WbDataProperty::class);
        $arrayPropNames = ["wbDataSale", "wbDataIncome","wbDataOrder", "wbDataStock", "wbDataReport", "wbDataExcise"];
        $arrayNames = ["sales","incomes","orders","stocks","reports","excise"];
        $data = [];
        for ($i=0;$i<count($arrayPropNames);$i++){
            $data[$arrayNames[$i]] = $repos->getProperty($arrayPropNames[$i], $wbData->getId());
        }
        $data = new ArrayCollection($data);
        foreach ($data as $datas) {
            $data[$data->indexOf($datas)] = array_map(function ($item) {
                return json_decode($item["property"], true);
            }, $datas);
        }
        return $data;
    }

    public function getWbData($token)
    {
        if(!$token) return ["token" => null];
        $context = ['token' => true];

        $wbData = $this
            ->entityManager
            ->getRepository(WbData::class)
            ->findOneBy(['apiToken' => $token->getId()])
        ;
        if(!$wbData) return  ["processing" => true];

        $repos = $this->entityManager->getRepository(WbDataProperty::class);
        $arrayPropNames = ["wbDataSale", "wbDataIncome","wbDataOrder", "wbDataStock", "wbDataReport", "wbDataExcise"];
        $arrayNames = ["sales","incomes","orders","stocks","reports","excise"];
        $data = [];

        for ($i=0;$i<count($arrayPropNames);$i++){
            $data[$arrayNames[$i]] = $repos->getProperty($arrayPropNames[$i], $wbData->getId());
        }

        return array_merge(
            $context,
            $this->sales($data["sales"]),
            $this->orders($data["orders"]),
            $this->stocks($data["stocks"])
        );
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