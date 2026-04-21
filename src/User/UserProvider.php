<?php

namespace Fastbolt\CommandScheduler\User;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;

final class UserProvider
{
    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    /**
     * @return string|null
     */
    public function getUserIdentifier(): ?string
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return null;
        }

        if ($token instanceof SwitchUserToken) {
            return $token->getOriginalToken()->getUserIdentifier();
        }

        return $token->getUserIdentifier();
    }
}
