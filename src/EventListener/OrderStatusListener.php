<?php

namespace App\EventListener;

use App\Entity\Order;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preUpdate, entity: Order::class)]
#[AsEntityListener(event: Events::prePersist, entity: Order::class)]
class OrderStatusListener
{
    private const DELIVERED_STATUS = 'Livré';

    public function preUpdate(Order $order, PreUpdateEventArgs $args): void
    {
        if ($args->hasChangedField('orderStatus')) {
            $this->updateCompletedDate($order);
        }
    }

    public function prePersist(Order $order, LifecycleEventArgs $args): void
    {
        $this->updateCompletedDate($order);
    }

    private function updateCompletedDate(Order $order): void
    {
        if ($order->getOrderStatus()->getName() === self::DELIVERED_STATUS && $order->getCompleted() === null) {
            $order->setCompleted(new \DateTime());
        }
    }
}