<?php

namespace App\EventListener;

use App\Entity\Item;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postLoad)]
#[AsDoctrineListener(event: Events::prePersist)]
class EntityManagerInjector
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $this->injectEntityManager($args);
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->injectEntityManager($args);
    }

    private function injectEntityManager(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Item || $entity instanceof Order) {
            $entity->setEntityManager($this->entityManager);
        }
    }
}