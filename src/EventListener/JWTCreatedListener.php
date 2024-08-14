<?php
//
//namespace App\EventListener;
//
//use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
//use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
//
//#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_created', method: 'onJWTCreated')]
//class JWTCreatedListener
//{
//    public function onJWTCreated(JWTCreatedEvent $event): void
//    {
//        $user = $event->getUser();
//        $payload = $event->getData();
//
//        if ($user instanceof \App\Entity\Client) {
//            // Заменяем 'username' на 'email'
//            $payload['email'] = $user->getEmail();
//            unset($payload['username']);
//            // Добавляем id пользователя
//            $payload['id'] = $user->getId();
//        }
//
//        $event->setData($payload);
//
//    }
//}


namespace App\EventListener;

use App\Entity\Client;
use App\Entity\Employee;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_created', method: 'onJWTCreated')]
class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        $payload = $event->getData();

        if ($user instanceof UserInterface) {

            $payload['email'] = $user->getUserIdentifier();
            unset($payload['username']);

            if ($user instanceof Client) {
                $this->addClientData($user, $payload);
            } elseif ($user instanceof Employee) {
                $this->addEmployeeData($user, $payload);
            }
        }

        $event->setData($payload);
    }

    private function addClientData(Client $user, array &$payload): void
    {
        $payload['id'] = $user->getId();
        $payload['name'] = $user->getName();
        $payload['surname'] = $user->getSurname();
        $payload['user_type'] = 'client';
    }

    private function addEmployeeData(Employee $user, array &$payload): void
    {
        $payload['id'] = $user->getId();
        $payload['name'] = $user->getName();
        $payload['surname'] = $user->getSurname();
        $payload['user_type'] = 'employee';
    }
}