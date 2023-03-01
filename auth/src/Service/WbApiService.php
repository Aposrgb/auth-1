<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;

class WbApiService
{
    protected $token;

    public function __construct(
        protected                        $apiUrl,
        protected EntityManagerInterface $entityManager
    )
    {
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    protected function sendRequest(string $path, string $method = 'GET', array $data = [], ?string $proxy = null)
    {
        $data['dateFrom'] = $data['dateFrom'] ?? (new \DateTime())
            ->modify('- 1 month')
            ->format("Y-m-d");

        $data['dateTo'] = (new \DateTime())
            ->format("Y-m-d");

        $options = [
            'headers' => ['Authorization' => 'Bearer ' . $this->token],
        ];

        if ($proxy) {
            $options['proxy'] = 'http://' . $proxy;
        }

        $request = (new Client())
            ->request($method, $this->apiUrl . $path . "?" . http_build_query($data), $options);

        return json_decode(
            $request
                ->getBody()
                ->getContents(),
            true);

    }

    public function incomes(string $dateFrom = null, ?string $proxy = null)
    {
        $data = ['dateFrom' => $dateFrom ?? null];
        return $this->sendRequest('incomes', 'GET', $data, $proxy);
    }

    public function stocks(string $dateFrom = null, ?string $proxy = null)
    {
        $data = ['dateFrom' => $dateFrom ?? null];
        return $this->sendRequest('stocks', 'GET', $data, $proxy);
    }

    public function orders(string $dateFrom = null, int $flag = 0, ?string $proxy = null)
    {
        $data = ['dateFrom' => $dateFrom ?? null, 'flag' => $flag];
        return $this->sendRequest('orders', 'GET', $data, $proxy);
    }

    public function sales(string $dateFrom = null, int $flag = 0, ?string $proxy = null)
    {
        $data = ['dateFrom' => $dateFrom ?? null, 'flag' => $flag];
        return $this->sendRequest('sales', 'GET', $data, $proxy);
    }

    public function reportDetailByPeriod(string $dateFrom = null, string $dateTo = null, int $limit = 100, int $rrdid = 0, ?string $proxy = null)
    {
        $data = ['dateFrom' => $dateFrom ?? null, 'dateTo' => $dateTo, 'limit' => $limit, 'rrdid' => $rrdid];
        return $this->sendRequest('reportDetailByPeriod', 'GET', $data, $proxy);
    }

    public function exciseGoods(string $dateFrom = null, ?string $proxy = null)
    {
        $data = ['dateFrom' => $dateFrom ?? null];
        return $this->sendRequest('excise-goods', 'GET', $data, $proxy);
    }
}