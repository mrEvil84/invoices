<?php

declare(strict_types=1);

namespace App\Core\User\Domain\Repository;

use App\Core\User\Domain\User;

interface UserReadModelRepositoryInterface
{
    /**
     * @return User[]
     */
    public function getInactiveUsers(int $offset, int $limit): array;
}
