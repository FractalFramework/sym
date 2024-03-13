<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailService extends AbstractController
{
    public function __construct(
        private MailerInterface $mailer
    ) {
    }

    public function send(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template . '.html.twig')
            ->context($context);
        $this->mailer->send($email);
    }

    public function send2(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context,
    ): void {
        $message = $this->render($template . '.html.twig', $context)->getContent();
        mail($to, $subject, $message);
    }

}
