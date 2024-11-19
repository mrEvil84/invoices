<?php

declare(strict_types=1);

namespace App\Core\Invoice\Application\Command\CreateInvoice;

use App\Core\Invoice\Domain\Exception\UserInactiveException;
use App\Core\Invoice\Domain\Exception\UserNotFoundException;
use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceRepositoryInterface;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateInvoiceHandler
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(CreateInvoiceCommand $command): void
    {
        $this->assertEmailExists($command->email);
        $this->assertUserIsActive($command->email);

        $this->invoiceRepository->save(new Invoice(
            $this->userRepository->getByEmail($command->email),
            $command->amount
        ));

        $this->invoiceRepository->flush();
    }

    private function assertEmailExists(string $email): void
    {
        if (!$this->userRepository->emailExists($email)) {
            throw new UserNotFoundException("User with email {$email} not found.");
        }
    }

    private function assertUserIsActive(string $email): void
    {
        $user = $this->userRepository->getByEmail($email);
        if (!$user->isActive()) {
            throw new UserInactiveException("User with email {$email} is not active.");
        }
    }
}
