<?php

namespace App\Service;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WbApiService extends AbstractController
{
    public function __construct(
        protected $apiUrl,
    )
    {
    }

    protected function sendRequest( $token, string $path, string $method = 'GET', array $data = [] )
    {
        $data['dateFrom'] = (new \DateTime())
            ->modify('- 1 month')
            ->format("Y-m-d");

        $data['dateTo'] = (new \DateTime())
            ->format("Y-m-d");

        $data['key'] = $token;
        $request = (new Client())
            ->request($method, $this->apiUrl . $path . "?" . http_build_query( $data ));

        return json_decode(
                $request
                ->getBody()
                ->getContents(),
            true);

    }

    public function incomes( $token, string $dateFrom = null )
    {
        $data = ['dateFrom' => $dateFrom ?? $this->dateFrom ?? null];
        return $this->sendRequest( $token,'incomes', 'GET', $data );
    }

    public function stocks( $token, string $dateFrom = null )
    {
        $data = ['dateFrom' => $dateFrom ?? $this->dateFrom ?? null];
        return $this->sendRequest( $token,'stocks', 'GET', $data );
    }

    public function orders( $token, string $dateFrom = null, int $flag = 0 )
    {
        $data = ['dateFrom' => $dateFrom ?? $this->dateFrom ?? null, 'flag' => $flag];
        return $this->sendRequest( $token,'orders', 'GET', $data );
    }

    public function sales( $token, string $dateFrom = null, int $flag = 0 )
    {
        $data = ['dateFrom' => $dateFrom ?? $this->dateFrom ?? null, 'flag' => $flag];
        return $this->sendRequest( $token,'sales', 'GET', $data );
    }

    public function reportDetailByPeriod( $token, string $dateFrom = null, string $dateTo = null, int $limit = 100, int $rrdid = 0 )
    {
        $data = ['dateFrom' => $dateFrom ?? $this->dateFrom ?? null, 'dateTo' => $dateTo, 'limit' => $limit, 'rrdid' => $rrdid];
        return $this->sendRequest( $token,'reportDetailByPeriod', 'GET', $data );
    }

    public function exciseGoods( $token, string $dateFrom = null )
    {
        $data = ['dateFrom' => $dateFrom ?? $this->dateFrom ?? null];
        return $this->sendRequest( $token,'exciseGoods', 'GET', $data );
    }
}