<?php

namespace App\EventListener;

use App\Entity\Client;
use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsDoctrineListener(Events::prePersist)]
class HashPasswordListener
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {
    }

    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Client) {
            $entity->setRoles(['ROLE_USER']);
            $entity->setPassword($this->hasher->hashPassword($entity, $entity->getPassword()));
        } elseif ($entity instanceof Employee) {
            $entity->setPassword($this->hasher->hashPassword($entity, $entity->getPassword()));
        }
    }
}