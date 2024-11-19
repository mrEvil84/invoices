<?php

declare(strict_types=1);

namespace App\Core\User\Application\Command\CreateUser;

use App\Common\EventManager\EventsCollectorTrait;
use App\Core\User\Domain\Exception\UserAlreadyExistsException;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use App\Core\User\Domain\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateUserHandler
{
    use EventsCollectorTrait;

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $user = new User($command->email, false);

        $this->assertUserNotExists($user);

        $this->userRepository->save($user);
        $this->userRepository->flush();
    }

    private function assertUserNotExists(User $user): void
    {
        if ($this->userRepository->emailExists($user->getEmail())) {
            throw new UserAlreadyExistsException('User already exists!');
        }
    }
}
