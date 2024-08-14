<?php

namespace App\Security\Voter;

use App\Entity\Client;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Client;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Client $client */
        $client = $subject;

        // ... (check conditions and return true to grant permission) ...
        return match($attribute) {
            self::VIEW => $this->canView($client, $user),
            self::EDIT => $this->canEdit($client, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }
    private function canView(Client $client, UserInterface $user): bool
    {
        // logic to determine if the user can view the client
        // for example:
        return $user === $client || in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canEdit(Client $client, UserInterface $user): bool
    {
        // logic to determine if the user can edit the client
        // for example:
        return $user === $client || in_array('ROLE_ADMIN', $user->getRoles());
    }
}
