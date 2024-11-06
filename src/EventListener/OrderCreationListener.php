<?php

namespace App\EventListener;

use App\Entity\Order;
use App\Entity\Client;
use App\Entity\ServiceCoefficient;
use App\Service\OrderAssignmentService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::postPersist)]
class OrderCreationListener
{
    private $orderAssignmentService;
    private $security;
    private $entityManager;
    private $mailer;
    private $twig;


    public function __construct(
        OrderAssignmentService $orderAssignmentService,
        Security $security,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        Environment $twig
    ) {
        $this->orderAssignmentService = $orderAssignmentService;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->twig = $twig;
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

        // We get the last coefficients
        $repository = $this->entityManager->getRepository(ServiceCoefficient::class);
        $latestCoefficients = $repository->findOneBy([], ['id' => 'DESC']);

        if ($latestCoefficients) {
            $coefficientsArray = [
                'expressCoefficient' => $latestCoefficients->getExpressCoefficient(),
                'ironingCoefficient' => $latestCoefficients->getIroningCoefficient(),
                'perfumingCoefficient' => $latestCoefficients->getPerfumingCoefficient(),
            ];
            $entity->setServiceCoefficients($coefficientsArray);
        } else {
            $coefficientsArray = [
                'expressCoefficient' => 1.0,
                'ironingCoefficient' => 1.0,
                'perfumingCoefficient' => 1.0,
            ];
        }
        $entity->setServiceCoefficients($coefficientsArray);

        // Calculate and set the price for each item
        foreach ($entity->getItems() as $item) {
            // Set the subcategory coefficient
            $subcategoryCoefficient = $item->getSubcategory()->getPriceCoefficient();
            $item->setSubcategoryCoefficient($subcategoryCoefficient);
            // Calculate and set the price
            $calculatedPrice = $item->getCalculatedPrice();
            $item->setPrice($calculatedPrice);
        }
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Order) {
            return;
        }

        // Assign employee to order
        $this->orderAssignmentService->assignEmployeeToOrder($entity);

        // Send email notification to client
        $this->sendOrderConfirmationEmail($entity);
    }

    private function sendOrderConfirmationEmail(Order $order): void
    {
        $client = $order->getClient();

        // Render the email content using Twig
        $emailContent = $this->twig->render('emails/order_confirmation.html.twig', [
            'client' => $client,
            'order' => $order,
        ]);

        // Create the email
        $email = (new Email())
            ->from('propre-propre@lapov.art')
            ->to($client->getEmail())
            ->subject('Confirmation de votre commande')
            ->html($emailContent);

        // Send the email
        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {

        }
    }

}