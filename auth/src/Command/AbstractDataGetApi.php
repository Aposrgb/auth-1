<?php

namespace App\Command;

use App\Entity\ApiToken;
use App\Entity\Proxy;
use App\Entity\WbDataEntity\WbData;
use App\Entity\WbDataEntity\WbDataProperty;
use App\Helper\Status\ApiTokenStatus;
use App\Repository\ProxyRepository;
use App\Service\WbApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractDataGetApi extends Command
{
    protected array $proxies;
    protected int $countProxies;
    protected int $index = 0;

    public function __construct(
        protected ProxyRepository $proxyRepository,
        protected EntityManagerInterface $entityManager,
        protected WbApiService           $service,
    )
    {
        parent::__construct();
        /** @var Proxy[] $proxies */
        $this->proxies = $this->proxyRepository->findAll();
        $this->countProxies = count($this->proxies);
    }

    private function sendWithProxy($closure)
    {
        while (1) {
            try {
                return $closure();
            } catch (\Exception $ex) {
                if($ex->getCode() == Response::HTTP_TOO_MANY_REQUESTS){
                    if ($this->countProxies == 0) {
                        sleep(120);
                        continue;
                    }
                    if ($this->index >= $this->countProxies) {
                        $this->index = 0;
                    }
                    /** @var Proxy $proxy */
                    $proxy = $this->proxies[$this->index];
                    $this->index++;
                    try{
                        return $closure(proxy: $proxy->getLogPassWithIpPort());
                    }catch (\Exception $ex) {
                        if($ex->getCode() == Response::HTTP_TOO_MANY_REQUESTS) {
                            return $this->sendWithProxy($closure);
                        }
                    }
                }
            }
        }
    }

    protected function insertData(ApiToken $token)
    {
        if ($token->getStatus() != ApiTokenStatus::ACTIVE && $token->getStatus() != ApiTokenStatus::UPDATING) {
            return;
        }
        $this->service->setToken($token->getToken());

        try {
            $sales = $this->service->sales();
        } catch (\Exception $ex) {
            switch ($ex->getCode()) {
                case Response::HTTP_BAD_REQUEST:
                    $token->setStatus(ApiTokenStatus::BLOCK);
                    $this->entityManager->flush();
                    return;
                case Response::HTTP_TOO_MANY_REQUESTS:
                    $sales = $this->sendWithProxy(fn($proxy = null) => $this->service->sales(proxy: $proxy));
            }
        }

        $wbData = $token->getWbData();
        $apiTokenRep = $this
            ->entityManager
            ->getRepository(ApiToken::class);

        if ($wbData) {
            $wbData->setDate(new \DateTime());
        } else {
            $wbData = $apiTokenRep
                ->getTokenWithWbData($token->getToken());

            $wbData = $wbData ? $wbData->getWbData() : new WbData();
            $token->setWbData($wbData);
        }

        $incomes = $this->sendWithProxy(fn($proxy = null) => $this->service->incomes(proxy: $proxy));
        $orders = $this->sendWithProxy(fn($proxy = null) => $this->service->orders(proxy: $proxy));
        $stocks = $this->sendWithProxy(fn($proxy = null) => $this->service->stocks(proxy: $proxy));
        $reports = $this->sendWithProxy(fn($proxy = null) => $this->service->reportDetailByPeriod((new \DateTime())->modify('- 6 month')->format("Y-m-d"), null, 10000, proxy: $proxy));
        $this->entityManager->flush();

        try {
            $apiTokenRep
                ->findAndSet($token->getToken(), $wbData->getId());
        } catch (\Exception $exception) {
        }

        if ($wbData->getId()) {
            $this
                ->entityManager
                ->getRepository(WbDataProperty::class)
                ->removeAllProp($wbData->getId());
        }

        // todo пока удалить метод был удален из wb
        //$excise = $this->service->exciseGoods();

        foreach ($sales as $sale) {
            $wbData->addWbDataSale(
                (new WbDataProperty())
                    ->setProperty(json_encode($sale))
                    ->setWbDataSale($wbData)
            );
        }
        foreach ($incomes as $income) {
            $wbData->addWbDataIncome(
                (new WbDataProperty())
                    ->setProperty(json_encode($income))
                    ->setWbDataIncome($wbData)
            );
        }
        foreach ($orders as $order) {
            $wbData->addWbDataOrder(
                (new WbDataProperty())
                    ->setProperty(json_encode($order))
                    ->setWbDataOrder($wbData)
            );
        }
        foreach ($stocks as $stock) {
            $wbData->addWbDataStock(
                (new WbDataProperty())
                    ->setProperty(json_encode($stock))
                    ->setWbDataStock($wbData)
            );
        }
        foreach ($reports as $report) {
            $wbData->addWbDataReport(
                (new WbDataProperty())
                    ->setProperty(json_encode($report))
                    ->setWbDataReport($wbData)
            );
        }
        $token->setStatus(ApiTokenStatus::ACTIVE);
        $this->entityManager->flush();
    }

    public function deleteOldWbData()
    {
        $repos = $this->entityManager->getRepository(WbData::class);
        $wbDatas = $repos->findAll();
        $wbDataPropRepos =
            $this->entityManager->getRepository(WbDataProperty::class);
        $apiTokenRepos = $this->entityManager->getRepository(ApiToken::class);
        foreach ($wbDatas as $wbData) {
            if ($wbData->getDate()->modify("+1 day") < new \DateTime()) {
                $wbDataPropRepos->removeAllProp($wbData->getId());
                $apiTokenRepos->deleteWbData($wbData->getId());
                $repos->remove($wbData);
            }
        }
        $this->entityManager->flush();
    }


}
