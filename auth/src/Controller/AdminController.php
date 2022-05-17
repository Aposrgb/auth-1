<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Helper\Status\UserStatus;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(
        protected UserPasswordHasherInterface $hasher,
        protected EntityManagerInterface $entityManager,
        protected $mpStatsApiWb
    )
    {
    }

    #[Route("/admin", name: 'admin', methods: ["GET"])]
    public function admin()
    {
        return $this->render('admin/admin.html.twig', [
            "users" => $this->entityManager->getRepository(User::class)->findAll()
        ]);
    }

    #[Route("/admin", name: 'admin_post', methods: ["POST"])]
    public function adminPost(Request $request)
    {
        $name = $request->request->get("name");
        $password =$request->request->get("password");
        $date = $request->request->get("dateExpired");
        $error = null;
        if($name && $password && $date){
            if(!$this->entityManager->getRepository(User::class)->findOneBy(['username' => $name])){
                $user = (new User());
                $user->setPassword($this->hasher->hashPassword($user ,$password))
                    ->setStatus(UserStatus::ACTIVE)
                    ->setUsername($name)
                    ->setDateExpired($date == 'always'?
                        ((new \DateTime())->modify("+ 10 year")):
                        ((new \DateTime())->modify("+ $date day"))
                    );
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }else{
                $error = true;
            }
        }else{
            $error = true;
        }
        return $this->render("admin", [
            "error" => $error,
            "users" => $this->entityManager->getRepository(User::class)->findAll()
        ]);
    }
    #[Route("/admin/token", name: 'admin_token', methods: ["GET"])]
    public function token(Request $request)
    {
        $token = $this->entityManager->getRepository(Token::class)->find(1)??null;
        $context = ['token'  => $token?->getToken()];
        try {
            (new Client())->request("GET", $this->mpStatsApiWb.'categories', [
                'headers' => ['X-Mpstats-TOKEN' => $token?->getToken() ]
            ]);
        }catch (\Exception $exception){
            if($exception->getCode() == Response::HTTP_TOO_MANY_REQUESTS){
                $context['error'] = "Что-то произошло не так, попробуйте позже (Слишком много запросов)";
            }else if($exception->getCode() == Response::HTTP_UNAUTHORIZED){
                $context['error'] = "Не валидный токен";
            }else{
                $context['error'] = "Неизвестная ошибка, попробуйте позже";
            }
        }
        return $this->render('admin/adminToken.html.twig', $context);
    }
    #[Route("/admin/token", name: 'admin_token_post', methods: ["POST"])]
    public function setToken(Request $request)
    {
        $context = ['token' => $request->request->all()['token']];
        if(strlen($context['token'])<30){
            $context['error'] = "Токен слишком короткий";
        }else{
            try {
                (new Client())->request("GET", $this->mpStatsApiWb.'categories', [
                    'headers' => ['X-Mpstats-TOKEN' => $context['token'] ]
                ]);
                $this->entityManager->getRepository(Token::class)->removeAll();
                $this->entityManager->persist(
                    (new Token())
                        ->setToken($context['token'])
                        ->setId(1)
                );
                $this->entityManager->flush();
                shell_exec("../bin/console category:load > /dev/null &");
            }catch (ClientException $exception){

            }
        }
        return $this->render('admin/adminToken.html.twig', $context);
    }
}