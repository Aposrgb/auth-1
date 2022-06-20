<?php

namespace App\Controller;

use App\Helper\Status\UserStatus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    #[Route(path: '/', name: 'redirect_login')]
    public function redirectToLogin(Request $request)
    {
        return $this->redirectToRoute("app_login");
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $client = $this->getUser();

        if($client && $client->getStatus() != UserStatus::ACTIVE){
            return $this->render('security/login.html.twig', ['logout' => true, 'deactivated' => true ]);
        }

        if($client && $client->getAllowIpAddress()){
            if($request->getClientIp() != $client->getAllowIpAddress()){
                return $this->render('security/login.html.twig', ['logout' => true, 'ipAddr' => true ]);
            }
        }

        if ($client) {
            $client->setAllowIpAddress($request->getClientIp());
            $this->entityManager->flush();
            return $this->redirectToRoute('index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(TokenStorageInterface $tokenStorage, Request $request): Response
    {
        $this->getUser()->setAllowIpAddress(null);
        $this->entityManager->flush();
        $request->getSession()->invalidate();
        $tokenStorage->setToken();
        return $this->redirectToRoute("app_login");
    }
}
