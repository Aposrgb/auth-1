<?php

namespace App\Service;

use App\Entity\WbCategory\WbDataCategory;

class WbService extends AbstractService
{
    public function getCategory()
    {
        $categorys = [];
        $categories = $this->entityManager->getRepository(WbDataCategory::class)->findAll();
        foreach ($categories as $category) {
            /** @var WbDataCategory $category */
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
                                    'subjects' => []
                                ]
                            ]
                        ];
                    } else {
                        $index = array_search($array[0], array_column($categorys, 'name'));
                        $categorys[$index]['subjects'][] = [
                            'name' => $array[1],
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
                                    'subjects' => [$array[2]]
                                ]
                            ]
                        ];
                    } else {
                        $index = array_search($array[0], array_column($categorys, 'name'));
                        $subIndex = array_search($array[1], array_column($categorys[$index]['subjects'], 'name'));
                        $categorys[$index]['subjects'][$subIndex]['subjects'][] = $array[2];
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
