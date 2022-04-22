<?php

namespace App\Command;

use App\Entity\ApiToken;
use App\Entity\WbDataEntity\WbData;
use App\Entity\WbDataEntity\WbDataProperty;
use App\Service\WbApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
class GetDataFromApiCommand extends Command
{

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected WbApiService $service
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('wb:data:processing')
            ->addArgument("apiToken")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $input->getArgument("apiToken");
        $this->service->setToken($token);

        $sales = $this->service->sales();
        $incomes = $this->service->incomes();
        $orders = $this->service->orders();
        $stocks = $this->service->stocks();
        $reports = $this->service->reportDetailByPeriod();
        $excise = $this->service->exciseGoods();

        $apiToken = $this
            ->entityManager
            ->getRepository(ApiToken::class)
            ->findOneBy(['token' => $token])
        ;

        $wbData = $this
            ->entityManager
            ->getRepository(WbData::class)
            ->findOneBy(['apiToken' => $apiToken->getId()])
        ;

        if($wbData){
            $this
                ->entityManager
                ->getRepository(WbDataProperty::class)
                ->removeAllProp($wbData->getId());

            $wbData->setDate(new \DateTime());
        }else{
            $wbData = (new WbData())
                ->setApiToken($apiToken);
            $this->entityManager->persist($wbData);
        }
        foreach ($sales as $sale){
            $wbData->addWbDataSale(
                (new WbDataProperty())
                    ->setProperty(json_encode($sale))
                    ->setWbDataSale($wbData)
            );
        }
        foreach ($incomes as $income){
            $wbData->addWbDataIncome(
                (new WbDataProperty())
                    ->setProperty(json_encode($income))
                    ->setWbDataIncome($wbData)
            );
        }
        foreach ($orders as $order){
            $wbData->addWbDataOrder(
                (new WbDataProperty())
                    ->setProperty(json_encode($order))
                    ->setWbDataOrder($wbData)
            );
        }
        foreach ($stocks as $stock){
            $wbData->addWbDataStock(
                (new WbDataProperty())
                    ->setProperty(json_encode($stock))
                    ->setWbDataStock($wbData)
            );
        }
        foreach ($reports as $report){
            $wbData->addWbDataReport(
                (new WbDataProperty())
                    ->setProperty(json_encode($report))
                    ->setWbDataReport($wbData)
            );
        }
        foreach ($excise as $exice){
            $wbData->addWbDataExcise(
                (new WbDataProperty())
                    ->setProperty(json_encode($exice))
                    ->setWbDataExcise($wbData)
            );
        }
        $this->entityManager->flush();
        return Command::SUCCESS;
    }
}