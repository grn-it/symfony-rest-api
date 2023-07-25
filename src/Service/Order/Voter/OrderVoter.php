<?php

declare(strict_types=1);

namespace App\Service\Order\Voter;

use App\Entity\Order;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrderVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT], true)) {
            return false;
        }
        
        if (!$subject instanceof Order) {
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

        if (!$subject instanceof Order) {
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
    
    private function isCanView(Order $order, User $user): bool
    {
        return $order->getUser() === $user;
    }
    
    private function isCanEdit(Order $order, User $user): bool
    {
        return $order->getUser() === $user;
    }
}
