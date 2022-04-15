<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="User")
 */
class UserController extends AbstractController
{
    #[Route('/users', name: 'get_users', methods: ["GET"])]
    public function getUsers(): JsonResponse
    {
        return $this->json(["user"]);
    }

}