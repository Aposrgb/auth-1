<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\CategorySales;
use App\Entity\DataCategory;
use App\Helper\Enum\CategoryEnum;
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
        protected $mpStatsApiWb,
        protected $mpStatsApiOz

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
        $this->entityManager->getRepository(Category::class)->deleteAll();
        $this->InsertCategory(CategoryEnum::WB, $input->getArgument("token"));
        $this->InsertCategory(CategoryEnum::OZON, $input->getArgument("token"));

        return Command::SUCCESS;
    }

    private function InsertCategory($categoryEntity, $token)
    {
        $api = $categoryEntity == CategoryEnum::WB?$this->mpStatsApiWb:$this->mpStatsApiOz;
        $url = $api."categories";
        $headers = [
            'headers' => ['X-Mpstats-TOKEN' => $token]
        ];
        $client = (new Client());
        $data = $client->request("GET", $url, $headers);

        $data = json_decode(
            $data
                ->getBody()
                ->getContents(),true);

        $wbCategory = new Category();
        $url = $api."category?";
        foreach ($data as $category){
            if($category['path'] == 'Авиабилеты') continue;
            $wbDataCategory = new DataCategory();
            $wbCategory->addWbCategory(
                $wbDataCategory
                    ->setName($category['name'])
                    ->setPath($category['path'])
                    ->setUrl($category['url'])
                    ->setWbCategory($wbCategory)
                    ->setEntity($categoryEntity)
            );
            if( count(explode('/', $category['path'])) != 1 ) continue;
            $query = [
                'path' => $category['path']
            ];
            $response = $client->request("GET", $url.http_build_query($query), $headers);
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
        return 0;
    }
}