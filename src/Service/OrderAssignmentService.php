<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;

class OrderAssignmentService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function assignEmployeeToOrder(Order $order): void
    {
        $employeeRepository = $this->entityManager->getRepository(Employee::class);

        $activeEmployees = $employeeRepository->createQueryBuilder('e')
            ->leftJoin('e.orders', 'o')
            ->where('e.isActive = :isActive')
            ->setParameter('isActive', true)
            ->select('e', 'COUNT(o) as orderCount')
            ->groupBy('e.id')
            ->orderBy('orderCount', 'ASC')
            ->getQuery()
            ->getResult();

        if (empty($activeEmployees)) {
            throw new \RuntimeException('No active employees available to assign the order');
        }

        $selectedEmployee = $activeEmployees[0][0];
        $order->setEmployee($selectedEmployee);

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}