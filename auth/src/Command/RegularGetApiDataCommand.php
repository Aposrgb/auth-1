<?php

namespace App\Command;

use App\Entity\ApiToken;
use App\Helper\Status\ApiTokenStatus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegularGetApiDataCommand extends AbstractDataGetApi
{
    protected function configure()
    {
        $this
            ->setName('wb:data:start')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        while(1){
            shell_exec("bin/console cancel:data:processing > /dev/null &");

            $allToken = $this
                ->entityManager
                ->getRepository(ApiToken::class)
                ->findBy(['status' => ApiTokenStatus::ACTIVE]);

            foreach ($allToken as $token){
                $this->deleteOldWbData();
                shell_exec("bin/console wb:data:processing ".$token->getToken()." > /dev/null &");
//                $this->insertData($token);
            }

            sleep(2*60*60);
        }
        return Command::SUCCESS;
    }
}