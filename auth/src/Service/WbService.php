<?php

namespace App\Service;

use App\Entity\ApiToken;
use App\Entity\WbDataEntity\WbDataProperty;
use App\Helper\Status\ApiTokenStatus;

class WbService extends AbstractService
{
    public function getCategory($id)
    {
        $token = $this->entityManager->getRepository(ApiToken::class)->findOneBy([
            'apiUser' => $id,
            'status' => ApiTokenStatus::ACTIVE
        ]);

        if(!$token) return ["token" => null];
        $context = ['token' => true];

        $wbData = $token->getWbData();

        if(!$wbData){
            $context["processing"] = true;
            return $context;
        }

        $stocks = $this
            ->entityManager
            ->getRepository(WbDataProperty::class)
            ->getProperty('wbDataStock', $wbData->getId());

        $category = [];
        foreach ($stocks as $stock) {
            $stock = json_decode($stock["property"], true);
            $data = array_column($category, 'name');
            if(!in_array($stock["category"], $data)){
                $category[] = [
                    "name" => $stock["category"],
                    "subject" => [$stock["subject"]]
                ];
            }else{
                $index = array_search($stock["category"], $data);
                $subjects = $category[$index]['subject'];
                if(!in_array($stock["subject"], $subjects)){
                    $category[$index]["subject"][] = $stock["subject"];
                }
            }
        }
        $context['categories'] = $category;
        return $context;
    }
}