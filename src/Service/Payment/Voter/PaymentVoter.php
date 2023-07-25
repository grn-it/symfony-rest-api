<?php

declare(strict_types=1);

namespace App\Service\Payment\Voter;

use App\Entity\Payment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PaymentVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT], true)) {
            return false;
        }
        
        if (!$subject instanceof Payment) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (!$subject instanceof Payment) {
            return false;
        }

        if ($attribute === self::VIEW) {
            if (!$this->isCanView($subject, $user)) {
                return false;
            }
        }

        if ($attribute === self::EDIT) {
            if (!$this->isCanEdit($subject, $user)) {
                return false;
            }
        }

        return true;
    }

    private function isCanView(Payment $payment, User $user): bool
    {
        return $payment->getOrder()->getUser() === $user;
    }

    private function isCanEdit(Payment $payment, User $user): bool
    {
        return $payment->getOrder()->getUser() === $user;
    }
}
