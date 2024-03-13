<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\MailService;
use App\Service\UserService;
use App\Form\RegisterFormType;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use Symfony\Component\Form\FormError;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegisterController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly EmailVerifier $emailVerifier,
        private MailService $mailService,
    ) {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
    ): Response {
        $user = new User();
        $form = $this->createForm(RegisterFormType::class, $user);
        $form->handleRequest($request);

        $plainPassword = $form->get('plainPassword')->getData();
        $confirmPassword = $form->get('confirmPassword')->getData();

        if ($plainPassword != $confirmPassword)
            $form->get('confirmPassword')->addError(new FormError('Les mots de passe ne correspondent pas.'));

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->saveUser($user, $form->get('plainPassword')->getData());
            //send signed email
            $this->emailVerifier->sendEmailConfirmation(
                'app_register_verif',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('users@posts.com', 'posts'))
                    ->to($user->getEmail())
                    ->subject('Posts : confirmez votre Email')
                    ->htmlTemplate('security/confirmation_email.html.twig')
            );

            $this->addFlash('flash-register', 'Votre inscription est enregistrée. 
            Veuillez vérifier votre e-mail pour la confirmer.');
        }
        return $this->render('security/register.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_register_verif')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //receive confirmation link, set isVerified=true
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('login-error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_login');
        }
        $this->addFlash('login-success', 'Votre Email a été vérifié avec succès.');
        return $this->redirectToRoute('app_login');
    }

}
