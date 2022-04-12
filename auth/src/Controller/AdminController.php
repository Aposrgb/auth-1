<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(
        protected UserPasswordHasherInterface $hasher,
        protected EntityManagerInterface $entityManager
    )
    {
    }

    #[Route("/admin", name: 'admin', methods: ["GET"])]
    public function admin()
    {
        return $this->render('admin/admin.html.twig');
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
                    ->setUsername($name)
                    ->setDateExpired($date == 'month'?
                        ((new \DateTime())->modify("+ 7 day")):
                        ((new \DateTime())->modify("+ 10 year"))
                    );
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }else{
                $error = true;
            }
        }else{
            $error = true;
        }
        return $this->render('admin/admin.html.twig', [
            "error" => $error
        ]);
    }
}