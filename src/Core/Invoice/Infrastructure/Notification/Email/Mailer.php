<?php

declare(strict_types=1);

namespace App\Core\Invoice\Infrastructure\Notification\Email;

use App\Common\Mailer\SMPTMailer;
use App\Core\Invoice\Domain\Notification\NotificationInterface;

class Mailer implements NotificationInterface
{
    public function __construct(
        private readonly SMPTMailer $smtpMailer,
    ) {
    }

    public function sendEmail(string $recipient, string $subject, string $message): void
    {
        $this->smtpMailer->send($recipient, $subject, $message);
    }
}
