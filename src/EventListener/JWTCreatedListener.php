<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_created', method: 'onJWTCreated')]
class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        $payload = $event->getData();

        if ($user instanceof \App\Entity\Client) {
            // Заменяем 'username' на 'email'
            $payload['email'] = $user->getEmail();
            unset($payload['username']);
            // Добавляем id пользователя
            $payload['id'] = $user->getId();
        }

        $event->setData($payload);

    }
}