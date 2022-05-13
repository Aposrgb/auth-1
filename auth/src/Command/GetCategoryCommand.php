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

        $this->InsertCategory(CategoryEnum::WB, $input->getArgument("token"));
        $this->InsertCategory(CategoryEnum::OZON, $input->getArgument("token"));
        $this->entityManager->getRepository(Category::class)->deleteAll();
        $this->entityManager->flush();
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
        }
        $this->entityManager->persist($wbCategory);
        return 0;
    }
}