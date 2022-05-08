<?php

namespace App\Command;

use App\Entity\WbCategory\WbCategory;
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
        protected EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setName('wb:category')
            ->addArgument('token')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = "http://mpstats.io/api/wb/get/categories";
        $data = (new Client())
            ->request("GET", $url, ['headers' => ['X-Mpstats-TOKEN' => $input->getArgument("token")]]);

        if($data->getStatusCode() == Response::HTTP_OK){
            $this->entityManager->getRepository(WbCategory::class)->deleteAll();
        }

        $data = json_decode(
            $data
                ->getBody()
                ->getContents(),true);

        $wbCategory = new WbCategory();
        foreach ($data as $category){
            if($category['path'] == 'Авиабилеты') continue;
            $wbCategory->addWbCategory(
                (new WbDataCategory())
                    ->setName($category['name'])
                    ->setPath($category['path'])
                    ->setUrl($category['url'])
                    ->setWbCategory($wbCategory)
            );
        }
        $this->entityManager->persist($wbCategory);
        $this->entityManager->flush();
        return Command::SUCCESS;
    }
}