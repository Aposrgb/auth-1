<?php

namespace App\Command;

use App\Entity\WbCategory\WbCategory;
use App\Entity\WbCategory\WbCategorySales;
use App\Entity\WbCategory\WbDataCategory;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

class GetCategoryCommand extends Command
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected $mpStatsApi
    )
    {
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setName('category:load')
            ->addArgument('token')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $this->mpStatsApi."categories";
        $headers = [
            'headers' => ['X-Mpstats-TOKEN' => $input->getArgument("token")]
        ];
        $client = (new Client());
        $data = $client->request("GET", $url, $headers);

        if($data->getStatusCode() == Response::HTTP_OK)
            $this->entityManager->getRepository(WbCategory::class)->deleteAll();
        else
            return Command::FAILURE;


        $data = json_decode(
            $data
                ->getBody()
                ->getContents(),true);

        $wbCategory = new WbCategory();
        $url = $this->mpStatsApi."category?";
        foreach ($data as $category){
            if($category['path'] == 'Авиабилеты') continue;
            $wbDataCategory = new WbDataCategory();
            $wbCategory->addWbCategory(
                $wbDataCategory
                    ->setName($category['name'])
                    ->setPath($category['path'])
                    ->setUrl($category['url'])
                    ->setWbCategory($wbCategory)
            );
            if(count(explode('/', $category['path']))>3) continue;
            $date = new \DateTime();
            $response = $client->request("GET",
                $url."path=".$category['path']."&d2=".$date->format('Y-m-d')."&d1=".$date->modify('-1 month')->format('Y-m-d'),
                $headers)
            ;
            $sales = json_decode($response->getBody()->getContents(), true)['data'];
            foreach ($sales as $sale){
                $wbDataCategory->addSale(
                    (new CategorySales())
                        ->setWbDataCategory($wbDataCategory)
                        ->setThumb($sale['thumb'])
                        ->setNmId($sale['id'])
                        ->setName($sale['name'])
                        ->setColor($sale['color']??null)
                        ->setCategory($sale['category'])
                        ->setPosition($sale['category_position']??null)
                        ->setBrand($sale['brand'])
                        ->setSeller($sale['seller'])
                        ->setBalance($sale['balance'])
                        ->setComments($sale['comments'])
                        ->setRating($sale['rating']??null)
                        ->setFinalPrice($sale['final_price'])
                        ->setClientPrice($sale['client_price']??null)
                        ->setDayStock($sale['days_in_stock'])
                        ->setRevenue($sale['revenue'])
                        ->setSales($sale['sales'])
                        ->setGraph(implode(',',$sale['graph']))
                        ->setEntity($categoryEntity)
                );
            }
        }
        $this->entityManager->persist($wbCategory);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
