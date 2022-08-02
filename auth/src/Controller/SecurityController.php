<?php

namespace App\Controller;

use App\Entity\User;
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

    #[Route(path: '/demo', name: 'demo')]
    public function demo(): Response
    {
        $user =  (new User())
            ->setUsername("demo_".rand(100000, 10000000).microtime());
        $this->entityManager->persist(
            $user
                ->setPassword($this->hasher->hashPassword($user, 12345))
                ->setStatus(UserStatus::DEMO)
                ->setDateExpired((new \DateTime())->modify("+ 15 min"))
        );
        $this->entityManager->flush();
        $user->setPassword(12345);
        return $this->render('demo/login.html.twig', [
            'user' => $user
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $client = $this->getUser();
        
        if(!$client){
            $users = $this->entityManager->getRepository(User::class)->findArchive();
            $users = array_column($users, "address");
            foreach ($users as $user){
                if($request->getClientIp() == $user){
                    return $this->render('security/login.html.twig', ['logout' => true, 'blocked' => true ]);
                }
            }
        }

        if($client && $client->getStatus() > UserStatus::DEMO){
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
        if($this->getUser()->getStatus() != UserStatus::DEMO && $this->getUser()->getStatus() != UserStatus::ARCHIVE)
            $this->getUser()->setAllowIpAddress(null);
        else
            $this->getUser()->setStatus(UserStatus::ARCHIVE);

        $this->entityManager->flush();
        $request->getSession()->invalidate();
        $tokenStorage->setToken();
        return $this->redirectToRoute("app_login");
    }
}
