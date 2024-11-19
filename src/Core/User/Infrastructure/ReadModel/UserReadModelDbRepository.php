<?php

declare(strict_types=1);

namespace App\Core\User\Infrastructure\ReadModel;

use App\Core\User\Domain\Repository\UserReadModelRepositoryInterface;
use App\Core\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;

class UserReadModelDbRepository implements UserReadModelRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return User[]
     */
    public function getInactiveUsers(int $offset, int $limit): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.isActive = :is_active')
            ->setParameter(':is_active', false)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
