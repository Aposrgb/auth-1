<?php

namespace App\Service;

use App\Entity\WbDataEntity\WbData;
use App\Entity\WbDataEntity\WbDataProperty;

class CabinetWbService extends AbstractService
{
    public function getWbData($token)
    {
        $context = ['token' => $token?$token->getToken():null];
        $wbData = $token?
            $this
                ->entityManager
                ->getRepository(WbData::class)
                ->findOneBy(['apiToken' => $token->getId()])
            :null
        ;
        if(!$wbData){
            return array_merge($context, [
                "processing" => true
            ]);
        }
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
            $data["rent"] = $array["forPay"]/$array["totalPrice"] * 100;
            $data["mardj"] = ($array["totalPrice"] - $array["forPay"]) / $array["totalPrice"] * 100;
        }
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