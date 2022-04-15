<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractService
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SerializerInterface $serializer,
        protected UserPasswordHasherInterface $hasher
    )
    {
    }

}