<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\UserFormType;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{

    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly EntityManagerInterface $entityManager,
        private UserFormType $userFormType,
        private UserService $userService,
        private Security $security,
    ) {

    }

    #[Route('/login', name: 'app_login')]
    public function login(
        AuthenticationUtils $authenticationUtils
    ): Response {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        if ($this->security->isGranted('ROLE_USER')) {
            $this->addFlash('login-success', 'Vous êtes connecté avec succès.');
        } elseif ($error) {
            $this->addFlash('login-error', 'Cet utilisateur n\'existe pas ou bien le mot de passe est incorrect.');
        }
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
        ]);
    }

    #[Route('/user', name: 'app_user')]
    public function user(): Response
    {
        return $this->render('admin/user.html.twig', []);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        $this->addFlash('home-flash', 'Vous êtes déconnecté.');
        //intercepted by firewall in security.config
    }

}
