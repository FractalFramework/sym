<?php

namespace App\Controller;

use App\Service\MailService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailController extends AbstractController
{
    public function __construct(
        private MailService $mailService
    ) {
    }

    #[Route('/testmail0', name: 'test_mail0')]
    public function test0(): Response
    {
        $this->mailService->send(
            $from = 'd@d.d',
            $to = 'a@a.a',
            $subject = 'subject',
            $template = 'mail',
            $context = [],
        );

        return $this->render('emails/mail.html.twig', [
        ]);
    }

    #[Route('/testmail', name: 'test_mail')]
    public function test(): Response
    {
        $this->mailService->send2(
            $from = 'd@d.d',
            $to = 'a@a.a',
            $subject = 'subject',
            $template = 'emails/mail',
            $context = [],
        );

        return $this->render('emails/mail.html.twig', [
        ]);
    }
}
