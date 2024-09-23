<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/api')]
class ResetPasswordController extends AbstractController
{
    private ResetPasswordHelperInterface $resetPasswordHelper;
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;

    public function __construct(
        ResetPasswordHelperInterface $resetPasswordHelper,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ) {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    #[Route('/reset-password', name: 'api_forgot_password_request', methods: ['POST'])]
    public function request(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return $this->json(['message' => 'Email is required'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->entityManager->getRepository(Client::class)->findOneBy([
            'email' => $email,
        ]);

        // Do not reveal whether a user account was found or not.
        if ($user) {
            try {
                $resetToken = $this->resetPasswordHelper->generateResetToken($user);

                // Send email
                $this->sendResetEmail($user, $resetToken->getToken());
            } catch (ResetPasswordExceptionInterface $e) {
                // Do not reveal why reset email was not sent
            }
        }

        // Always return a success response
        return $this->json(['message' => 'Si un compte avec cet e-mail existe, un e-mail de réinitialisation du mot de passe a été envoyé.'], Response::HTTP_OK);
    }

    #[Route('/reset-password/reset', name: 'api_forgot_password', methods: ['POST'])]
    public function reset(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;
        $plainPassword = $data['password'] ?? null;

        if (!$token || !$plainPassword) {
            return $this->json(['message' => 'Le token et le mot de passe sont obligatoires'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->json(['message' => 'Token invalide ou expiré'], Response::HTTP_BAD_REQUEST);
        }

        $user->setPassword($plainPassword);

        // Remove the reset request token
        $this->resetPasswordHelper->removeResetRequest($token);

        $this->entityManager->flush();

        return $this->json(['message' => 'Le mot de passe a été réinitialisé avec succès'], Response::HTTP_OK);
    }

    private function sendResetEmail(Client $user, string $resetToken)
    {
        // Get the frontend URL from parameters
        $frontendUrl = $this->getParameter('frontend_url');

        $resetUrl = $frontendUrl . '/client/reset-password?token=' . urlencode($resetToken);

        $email = (new Email())
            ->from(new Address('info@propre-propre.fr', 'ProprePropre'))
            ->to($user->getEmail())
            ->subject('Votre demande de réinitialisation de mot de passe')
            ->text(sprintf(
                "Bonjour %s,\n\nPour réinitialiser votre mot de passe, veuillez cliquer sur le lien ci-dessous :\n\n%s\n\nCe lien expirera dans %d heure(s).\n\nSi vous n'avez pas demandé de réinitialisation de mot de passe, veuillez ignorer cet e-mail.",
                $user->getName(),
                $resetUrl,
                $this->resetPasswordHelper->getTokenLifetime() / 3600
            ));

        $this->mailer->send($email);
    }
}