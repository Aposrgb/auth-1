<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\WbDataEntity\WbData;
use App\Entity\WbDataEntity\WbDataProperty;

class WbService extends AbstractService
{
    public function getCategory(User $user)
    {
        $token = $user->getApiToken()->last();
        
        $wbData = $this
            ->entityManager
            ->getRepository(WbData::class)
            ->findOneBy(['apiToken' => $token->getId()]);

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
        return $category;
    }
}