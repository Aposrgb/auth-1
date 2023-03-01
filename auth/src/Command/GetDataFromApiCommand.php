<?php

namespace App\Command;

use App\Entity\ApiToken;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetDataFromApiCommand extends AbstractDataGetApi
{
    protected function configure()
    {
        $this
            ->setName('wb:data:processing')
            ->addArgument("apiToken")
            ->addArgument("apiUser")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $this
            ->entityManager
            ->getRepository(ApiToken::class)
            ->findOneBy([
                "token" => $input->getArgument("apiToken"),
                "apiUser" => $input->getArgument("apiUser")
            ]);

        $this->insertData($token);
        return Command::SUCCESS;
    }
}