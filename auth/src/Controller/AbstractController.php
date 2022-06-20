<?php

namespace App\Controller;

use App\Helper\Status\UserStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public function __construct(
        protected UserPasswordHasherInterface $hasher,
        protected EntityManagerInterface $entityManager,
        protected $mpStatsApi,
        protected $mpStatsApiOz,
        protected $mpStatsApiWb
    )
    {
    }

    public function checkStatusUser()
    {
        $client = $this->getUser();
        if(!in_array('ROLE_ADMIN', $client->getRoles())) {
            if($client->getDateExpired() < new \DateTime()){
                $client->setStatus(UserStatus::BLOCK);
                $this->entityManager->flush();
            }
        }
        if($client->getStatus() != UserStatus::ACTIVE){
            return $this->redirectToRoute('app_login');
        }
        return null;
    }

}