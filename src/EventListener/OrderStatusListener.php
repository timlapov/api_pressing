<?php

namespace App\EventListener;

use App\Entity\Order;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

#[AsEntityListener(event: Events::preUpdate, entity: Order::class)]
#[AsEntityListener(event: Events::prePersist, entity: Order::class)]
class OrderStatusListener
{
    private const DELIVERED_STATUS = 'Livré';
    private const READY_STATUS = 'Prêt';

    private MailerInterface $mailer;
    private Environment $twig;
    private LoggerInterface $logger;

    public function __construct(MailerInterface $mailer, Environment $twig, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    public function preUpdate(Order $order, PreUpdateEventArgs $args): void
    {
        if ($args->hasChangedField('orderStatus')) {
            $oldStatus = $args->getOldValue('orderStatus')->getName();
            $newStatus = $args->getNewValue('orderStatus')->getName();

            $this->updateCompletedDate($order, $newStatus);

            if ($newStatus === self::READY_STATUS) {
                $this->sendOrderReadyEmail($order);
            }
        }
    }

    public function prePersist(Order $order, LifecycleEventArgs $args): void
    {
        $this->updateCompletedDate($order);

        if ($order->getOrderStatus()->getName() === self::READY_STATUS) {
            $this->sendOrderReadyEmail($order);
        }
    }

    private function updateCompletedDate(Order $order): void
    {
        if ($order->getOrderStatus()->getName() === self::DELIVERED_STATUS && $order->getCompleted() === null) {
            $order->setCompleted(new \DateTime());
        }
    }

    private function sendOrderReadyEmail(Order $order): void
    {
        $client = $order->getClient();

        if (!$client) {
            $this->logger->warning('Failed to send email: client not found.', ['order_id' => $order->getId()]);
            return;
        }

        // Email template rendering
        try {
            $emailContent = $this->twig->render('emails/order_ready.html.twig', [
                'client' => $client,
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Error when rendering the email template.', [
                'order_id' => $order->getId(),
                'exception' => $e,
            ]);
            return;
        }

        // Creating and sending email
        $email = (new Email())
            ->from('propre-propre@lapov.art')
            ->to($client->getEmail())
            ->subject('Votre commande est prête')
            ->html($emailContent);

        try {
            $this->mailer->send($email);
            $this->logger->info('Email "commande prête" has been sent.', [
                'order_id' => $order->getId(),
                'client_email' => $client->getEmail(),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Error when sending email "commande prête".', [
                'order_id' => $order->getId(),
                'client_email' => $client->getEmail(),
                'exception' => $e,
            ]);
        }
    }
}