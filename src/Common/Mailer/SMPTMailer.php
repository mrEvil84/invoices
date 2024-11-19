<?php

namespace App\Common\Mailer;

use Symfony\Component\Mailer\MailerInterface;
use App\Common\Mailer\MailerInterface as AppMailerInterface;
use Symfony\Component\Mime\Email;

class SMPTMailer implements AppMailerInterface
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    public function send(string $recipient, string $subject, string $message): void
    {
        $email = (new Email())
            ->from('allecurier@example.com')
            ->to($recipient)
            ->subject($subject)
            ->text($message)
            ->html('<p>' . $message . '</p>');

        $this->mailer->send($email);
    }
}
