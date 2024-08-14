<?php

namespace App\Security\Voter;

use App\Entity\Employee;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class EmployeeVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Employee;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Employee $employee */
        $employee = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($employee, $user),
            self::EDIT => $this->canEdit($employee, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(Employee $employee, UserInterface $user): bool
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        return $user instanceof Employee && $user->getId() === $employee->getId();
    }

    private function canEdit(Employee $employee, UserInterface $user): bool
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        return $user instanceof Employee && $user->getId() === $employee->getId();
    }
}
