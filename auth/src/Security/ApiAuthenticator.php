<?php

namespace App\Security;

use App\Entity\Device;
use App\Helper\Exception\ApiExceptionHandler;
use App\Helper\Status\DeviceStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiAuthenticator extends AbstractAuthenticator
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('token');
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get('token');
        if (null === $apiToken)
            ApiExceptionHandler::errorApiHandlerMessage(customMessage: "No API token provided", code:Response::HTTP_UNAUTHORIZED);

        /** @var Device $device */
        $device = $this->entityManager->getRepository(Device::class)->findOneBy([
            'token' => $apiToken,
            'status' => DeviceStatus::ACTIVE,
        ]);

        if (!$device) {
            ApiExceptionHandler::errorApiHandlerMessage(customMessage:'Токен авторизации пустой или не существует', code:Response::HTTP_FORBIDDEN);
        }

        if ($device->getDateExpires() <= new \DateTime()) {
            $device->setStatus(DeviceStatus::ARCHIVE);
            $this->entityManager->persist($device);
            $this->entityManager->flush();
            ApiExceptionHandler::errorApiHandlerMessage(customMessage:'Ошибка авторизации, токен истек',  code: Response::HTTP_UNAUTHORIZED);
        }

        return new SelfValidatingPassport(new UserBadge($device->getUser()->getUserIdentifier()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        ApiExceptionHandler::errorApiHandlerMessage(
            customMessage: $exception->getMessage(), code: Response::HTTP_UNAUTHORIZED
        );
    }
}