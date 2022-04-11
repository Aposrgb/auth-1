<?php

namespace App\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class UserController extends AbstractController
{
    #[Route('/users', name: 'get_users', methods: ["GET"])]
    public function getUsers(): JsonResponse
    {
        return $this->json(["user"]);
    }
    #[Route('/login_check', name: 'login', methods: ["GET"])]
    public function login(): JsonResponse
    {
        return $this->json(["log"]);
    }
}