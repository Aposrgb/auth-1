<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;

class WbApiService
{
    protected $token;

    public function __construct(
        protected $apiUrl,
        protected EntityManagerInterface $entityManager
    )
    {
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    protected function sendRequest(string $path, string $method = 'GET', array $data = [] )
    {
        $data['dateFrom'] = $data['dateFrom']??(new \DateTime())
            ->modify('- 1 month')
            ->format("Y-m-d");

        $data['dateTo'] = (new \DateTime())
            ->format("Y-m-d");

        $data['key'] = $this->token;
        $request = (new Client())
            ->request($method, $this->apiUrl . $path . "?" . http_build_query( $data ));
        if($path == "incomes"){
            var_export($this->apiUrl . $path . "?" . http_build_query( $data ));
        }

        return json_decode(
        $request
            ->getBody()
            ->getContents(),
        true);

    }

    public function incomes(string $dateFrom = null )
    {
        $data = ['dateFrom' => $dateFrom  ?? null];
        return $this->sendRequest('incomes', 'GET', $data );
    }

    public function stocks(string $dateFrom = null )
    {
        $data = ['dateFrom' => $dateFrom  ?? null];
        return $this->sendRequest('stocks', 'GET', $data );
    }

    public function orders(string $dateFrom = null, int $flag = 0 )
    {
        $data = ['dateFrom' => $dateFrom  ?? null, 'flag' => $flag];
        return $this->sendRequest('orders', 'GET', $data );
    }

    public function sales(string $dateFrom = null, int $flag = 0 )
    {
        $data = ['dateFrom' => $dateFrom  ?? null, 'flag' => $flag];
        return $this->sendRequest('sales', 'GET', $data );
    }

    public function reportDetailByPeriod(string $dateFrom = null, string $dateTo = null, int $limit = 100, int $rrdid = 0 )
    {
        $data = ['dateFrom' => $dateFrom  ?? null, 'dateTo' => $dateTo, 'limit' => $limit, 'rrdid' => $rrdid];
        return $this->sendRequest('reportDetailByPeriod', 'GET', $data );
    }

    public function exciseGoods(string $dateFrom = null )
    {
        $data = ['dateFrom' => $dateFrom  ?? null];
        return $this->sendRequest('excise-goods', 'GET', $data );
    }
}