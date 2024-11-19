<?php

declare(strict_types=1);

namespace App\Core\User\Application\Query\GetInactiveUsersEmails;

use App\Core\User\Application\DTO\UserDTO;
use App\Core\User\Domain\Repository\UserReadModelRepositoryInterface;
use App\Core\User\Domain\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetInactiveUsersEmailsHandler
{
    public function __construct(
        private readonly UserReadModelRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(GetInactiveUsersEmailsQuery $query): array
    {
        $users = $this->userRepository->getInactiveUsers(
            $query->getOffset(),
            $query->getLimit()
        );

        return array_map(function (User $user) {
            return new UserDTO(
                $user->getId(),
                $user->getEmail(),
                $user->isActive()
            );
        }, $users);
    }
}
