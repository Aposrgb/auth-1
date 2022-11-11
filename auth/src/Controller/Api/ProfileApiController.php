<?php

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Helper\Exception\ApiException;
use App\Service\WbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/user')]
class ProfileApiController extends AbstractController
{
    public function __construct(
        protected UserPasswordHasherInterface $hasher,
        protected EntityManagerInterface      $entityManager,
        protected WbService                   $service
    )
    {
        parent::__construct($this->hasher, $this->entityManager, $this->mpStatsApi, $this->mpStatsApiOz, $this->mpStatsApiWb);
    }

    #[Route('/changePass', name: 'change_password', methods: ['GET'])]
    public function change(Request $request)
    {
        $query = $request->query->all();
        if ($query['pass'] != $query['checkPass']) {
            throw new ApiException(message: "Пароли не совпадают", code: Response::HTTP_BAD_REQUEST);
        }
        if (strlen($query['pass']) < 4) {
            throw new ApiException(message: "Слишком короткий пароль", code: Response::HTTP_BAD_REQUEST);
        }
        /** @var User $user */
        $user = $this->getUser();
        $user->setPassword($this->hasher->hashPassword($user, $query['pass']));
        $this->entityManager->flush();

        return $this->json(['data' => ['message' => 'ok']]);
    }
}