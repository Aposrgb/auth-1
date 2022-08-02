<?php

namespace App\Controller\Api;

use App\Service\WbService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/item')]
class ItemApiController extends AbstractController
{
    public function __construct(
        protected $mpStatsApiWb,
        protected $mpStatsApiOz,
        protected WbService $service
    )
    {
    }

    #[Route('', name: 'api_search_item', methods: ['GET'])]
    public function searchItem(Request $request)
    {
        $sku = $request->query->all()['sku']??null;
        $client = new Client();
        $data = [];
        if(is_numeric($sku)){
            try{
                if($client->get($this->mpStatsApiWb."item/$sku", $this->service->getHeaders())->getStatusCode() == Response::HTTP_OK){
                    $data = [
                        'isWb' => true,
                        'value' => $sku
                    ];
                }
            }catch (GuzzleException $exception){
                try {
                    if($client->get($this->mpStatsApiOz."item/$sku", $this->service->getHeaders())->getStatusCode() == Response::HTTP_OK){
                        $data = [
                            'isWb' => false,
                            'value' => $sku
                        ];
                    }
                }catch (GuzzleException $exception){}
            }
        }else{
            $nmId = $sku;
            $sku = explode('/', $sku)[4]??null;
            if($sku && is_numeric($sku)){
                try{
                    if($client->get($this->mpStatsApiWb."item/$sku", $this->service->getHeaders())->getStatusCode() == Response::HTTP_OK){
                        $data = [
                            'isWb' => true,
                            'value' => $sku
                        ];
                    }
                }catch (GuzzleException $exception){
                    try {
                        if($client->get($this->mpStatsApiOz."item/$sku", $this->service->getHeaders())->getStatusCode() == Response::HTTP_OK){
                            $data = [
                                'isWb' => false,
                                'value' => $sku
                            ];
                        }
                    }catch (GuzzleException $exception){}
                }
            }else{
                $temp = explode("-", $sku);
                $temp = $temp[count($temp)-1];
                if(is_numeric($temp)){
                    try {
                        if($client->get($this->mpStatsApiOz."item/$temp", $this->service->getHeaders())->getStatusCode() == Response::HTTP_OK){
                            $data = [
                                'isWb' => false,
                                'value' => $temp
                            ];
                        }
                    }catch (GuzzleException $exception){}
                }else{
                    $nmId = explode('/', $nmId)[6]??null;
                    if(is_numeric($nmId)){
                        try {
                            if($client->get($this->mpStatsApiOz."item/$nmId", $this->service->getHeaders())->getStatusCode() == Response::HTTP_OK){
                                $data = [
                                    'isWb' => false,
                                    'value' => $nmId
                                ];
                            }
                        }catch (GuzzleException $exception){}
                    }
                }
            }
        }
        return $this->json($data);
    }
}