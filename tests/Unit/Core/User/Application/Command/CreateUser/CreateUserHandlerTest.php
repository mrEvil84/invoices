<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\User\Application\Command\CreateUser;

use App\Core\User\Application\Command\CreateUser\CreateUserCommand;
use App\Core\User\Application\Command\CreateUser\CreateUserHandler;
use App\Core\User\Domain\Exception\UserAlreadyExistsException;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateUserHandlerTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;

    private CreateUserHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new CreateUserHandler(
            $this->userRepository = $this->createMock(
                UserRepositoryInterface::class
            )
        );
    }

    /**
     * @test
     */
    public function shouldCreateUser(): void
    {
        $this->userRepository->expects($this->once())
            ->method('emailExists')
            ->with('test@test.com')
            ->willReturn(false);

        $this->userRepository->expects($this->once())
            ->method('save');

        $this->userRepository->expects($this->once())
            ->method('flush');

        $this->handler->__invoke(new CreateUserCommand('test@test.com'));
    }

    /**
     * @test
     */
    public function shouldNotCreateUserWhenEmailAlreadyExists(): void
    {
        $this->userRepository->expects($this->once())
            ->method('emailExists')
            ->with('test@test.com')
            ->willReturn(true);

        $this->expectException(UserAlreadyExistsException::class);
        $this->expectExceptionMessage('User already exists!');

        $this->handler->__invoke(new CreateUserCommand('test@test.com'));
    }
}
