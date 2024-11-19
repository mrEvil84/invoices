<?php

declare(strict_types=1);

namespace App\Core\User\Application\EventListener;

use App\Core\Invoice\Domain\Notification\NotificationInterface;
use App\Core\User\Domain\Event\UserCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserCreatedEventSubscriberListener implements EventSubscriberInterface
{
    public function __construct(private readonly NotificationInterface $mailer)
    {
    }

    public function send(UserCreatedEvent $event): void
    {
        $this->mailer->sendEmail(
            $event->getUser()->getEmail(),
            'Rejestracja w systemie.',
            'Zarejestrowano konto w systemie. <b>' . $event->getUser()->getEmail() . '</b> Aktywacja konta trwa do 24h'
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatedEvent::class => 'send'
        ];
    }
}
