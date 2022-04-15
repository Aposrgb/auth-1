<?php

namespace App\Command;

use App\Entity\Client;
use App\Entity\User;
use App\Helper\Status\UserStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommand extends Command
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected UserPasswordHasherInterface $hasher
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDescription("Create a new user")
            ->setHelp('This command allows you to create a user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();

        $helper = $this->getHelper('question');

        $question = new Question(
            '<question>Please type username</question>: ',
        );

        $user->setUsername($helper->ask($input, $output, $question));

        $question = new Question(
            '<question>Please type password</question>: ',
        );

        $password = $helper->ask($input, $output, $question);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $user->setStatus(UserStatus::ACTIVE);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}