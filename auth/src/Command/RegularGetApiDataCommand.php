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
            $allToken = $this
                ->entityManager
                ->getRepository(ApiToken::class)
                ->findBy(['status' => ApiTokenStatus::ACTIVE]);

            $tokens = array_map(
                function (ApiToken $item){
                    return ['token' => $item->getToken(), 'user' => $item->getApiUser()->getId()];
            }, $allToken);

            $allToken = null;
            $callStack = [];
            foreach ($tokens as $token){
                if(!in_array($token["token"], $callStack)){
                    $callStack[] = $token["token"];
                    shell_exec("bin/console wb:data:processing ".$token["token"]." ".$token["user"]." > /dev/null &");
                }
            }
            $this->deleteOldWbData();

            sleep(2*60*60);
        }
        return Command::SUCCESS;
    }
}