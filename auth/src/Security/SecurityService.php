<?php

namespace App\Security;

use App\Entity\Device;
use App\Entity\User;
use App\Helper\DTO\UserDTO;
use App\Helper\Exception\ApiException;
use App\Helper\Status\UserStatus;
use App\Service\AbstractService;
use Symfony\Component\HttpFoundation\Response;

class SecurityService extends AbstractService
{
    public function login(UserDTO $userDTO)
    {
        $user = $this
            ->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => $userDTO->getUsername()]);
        $password = $user && $this->hasher->isPasswordValid($user, $userDTO->getPassword());
        if(!$password)
            throw new ApiException(message: "Email or password incorrect", code: Response::HTTP_BAD_REQUEST);

        if ($user->getStatus() == UserStatus::ARCHIVE) {
            throw new ApiException(message: "Your account not found", code: Response::HTTP_BAD_REQUEST);
        }

        $device = new Device();
        $user->addDevice($device);

        $this->entityManager->flush();
        return $device->getToken();
    }
}