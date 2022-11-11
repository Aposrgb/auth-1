<?php

namespace App\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CancelRegularCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('cancel:data:processing')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rows = explode("\n",shell_exec("ps aux | grep bin/console"));
        foreach ($rows as $row){
            $parsesData = new ArrayCollection(explode(" ", $row));
            $pid = $parsesData[7]!= ''?$parsesData[7]:$parsesData[6];
            switch ($parsesData->last()){
                case "wb:data:start":
                case "bin/console":
                    shell_exec("kill -9 ".$pid);
                    break;
            }
        }
        return Command::SUCCESS;
    }

}