<?php

declare(strict_types=1);

namespace App\Core\User\Application\DTO;

class UserDTO
{
    public function __construct(
        private readonly int $id,
        private readonly string $email,
        private readonly bool $isActive,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
