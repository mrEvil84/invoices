<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Invoice\Application\Command\CreateInvoice;

use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceCommand;
use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceHandler;
use App\Core\Invoice\Domain\Exception\InvoiceException;
use App\Core\Invoice\Domain\Exception\UserInactiveException;
use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceRepositoryInterface;
use App\Core\Invoice\Domain\Exception\UserNotFoundException as InvoiceUserNotFoundException;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use App\Core\User\Domain\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateInvoiceHandlerTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;

    private InvoiceRepositoryInterface|MockObject $invoiceRepository;

    private CreateInvoiceHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new CreateInvoiceHandler(
            $this->invoiceRepository = $this->createMock(
                InvoiceRepositoryInterface::class
            ),
            $this->userRepository = $this->createMock(
                UserRepositoryInterface::class
            )
        );
    }

    public function test_handle_success(): void
    {
        $user = $this->createMock(User::class);
        $user->method('isActive')->willReturn(true);
        $user->method('getEmail')->willReturn('test@test.com');

        $invoice = new Invoice(
            $user,
            12500
        );

        $this->userRepository->expects(self::once())
            ->method('emailExists')
            ->willReturn(true);

        $this->userRepository->expects(self::exactly(2))
            ->method('getByEmail')
            ->willReturn($user);

        $this->invoiceRepository->expects(self::once())
            ->method('save')
            ->with($invoice);

        $this->invoiceRepository->expects(self::once())
            ->method('flush');

        $this->handler->__invoke((new CreateInvoiceCommand('test@test.pl', 12500)));
    }

    public function test_handle_user_not_exists(): void
    {
        $this->userRepository->expects(self::once())
            ->method('emailExists')
            ->willReturn(false);

        $this->expectException(InvoiceUserNotFoundException::class);
        $this->handler->__invoke((new CreateInvoiceCommand('test@test.pl', 12500)));
    }

    public function test_handle_invoice_invalid_amount(): void
    {
        $this->expectException(InvoiceException::class);

        $this->handler->__invoke((new CreateInvoiceCommand('test@test.pl', -5)));
    }
    /**
     * @test
     */
    public function shouldCreateInvoiceForActiveUsersOnly(): void
    {
        $user = $this->createMock(User::class);
        $user->expects(self::once())->method('isActive')->willReturn(true);

        $this->userRepository->expects(self::once())
            ->method('emailExists')
            ->willReturn(true);
        $this->userRepository->expects(self::exactly(2))
            ->method('getByEmail')
            ->willReturn($user);

        $this->invoiceRepository->expects(self::once())
            ->method('save');

        $this->invoiceRepository->expects(self::once())
            ->method('flush');


        $this->handler->__invoke((new CreateInvoiceCommand('test@test.pl', 1000)));
    }

    /**
     * @test
     */
    public function shouldNotCreateInvoiceWhenUserIsInactive(): void
    {
        $userEmail = 'test@test.pl';
        $user = $this->createMock(User::class);
        $user->expects(self::once())->method('isActive')->willReturn(false);

        $this->userRepository->expects(self::once())
            ->method('emailExists')
            ->willReturn(true);

        $this->userRepository->expects(self::once())
            ->method('getByEmail')
            ->willReturn($user);

        $this->expectException(UserInactiveException::class);
        $this->expectExceptionMessage('User with email ' . $userEmail . ' is not active.');

        $this->handler->__invoke((new CreateInvoiceCommand($userEmail, 1000)));
    }

    /**
     * @test
     */
    public function shouldNotCreateInvoiceWhenEmailNotExits(): void
    {
        $userEmail = 'test@test.pl';
        $this->userRepository->expects(self::once())
            ->method('emailExists')
            ->willReturn(false);

        $this->expectException(InvoiceUserNotFoundException::class);
        $this->expectExceptionMessage("User with email $userEmail not found.");
        $this->handler->__invoke((new CreateInvoiceCommand($userEmail, 1000)));
    }
}
