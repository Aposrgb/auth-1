<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('deploy')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        shell_exec("composer install; bin/console doctrine:schema:update -f");
        return Command::SUCCESS;
    }
}