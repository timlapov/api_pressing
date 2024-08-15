<?php
//
//namespace App\EventListener;
//
//use App\Entity\Order;
//use App\Service\OrderAssignmentService;
//use Doctrine\Persistence\Event\LifecycleEventArgs;
//use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
//use Doctrine\ORM\Events;
//
//#[AsDoctrineListener(event: Events::postPersist)]
//class OrderCreationListener
//{
//    private $orderAssignmentService;
//
//    public function __construct(OrderAssignmentService $orderAssignmentService)
//    {
//        $this->orderAssignmentService = $orderAssignmentService;
//    }
//
//    public function postPersist(LifecycleEventArgs $args): void
//    {
//        $entity = $args->getObject();
//
//        if (!$entity instanceof Order) {
//            return;
//        }
//
//        $this->orderAssignmentService->assignEmployeeToOrder($entity);
//    }
//}


namespace App\EventListener;

use App\Entity\Order;
use App\Entity\Client;
use App\Service\OrderAssignmentService;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::postPersist)]
class OrderCreationListener
{
    private $orderAssignmentService;
    private $security;

    public function __construct(OrderAssignmentService $orderAssignmentService, Security $security)
    {
        $this->orderAssignmentService = $orderAssignmentService;
        $this->security = $security;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Order) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof Client) {
            throw new AccessDeniedException('Only clients can create orders.');
        }

        if ($entity->getClient() !== $user && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You can only create orders for yourself.');
        }

        $entity->setClient($user);
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Order) {
            return;
        }

        $this->orderAssignmentService->assignEmployeeToOrder($entity);
    }
}