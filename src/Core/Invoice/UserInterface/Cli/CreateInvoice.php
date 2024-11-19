<?php

declare(strict_types=1);

namespace App\Core\Invoice\UserInterface\Cli;

use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

#[AsCommand(
    name: 'app:invoice:create',
    description: 'Add new invoice.'
)]
class CreateInvoice extends Command
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->bus->dispatch(new CreateInvoiceCommand(
                $input->getArgument('email'),
                (int)$input->getArgument('amount')
            ));

            $output->writeln('Invoice created!');
            return Command::SUCCESS;
        } catch (Throwable $exception) {
            $output->writeln('Error: ' . $exception->getMessage());
            return Command::FAILURE;
        }
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'Email is required.');
        $this->addArgument('amount', InputArgument::REQUIRED, 'Amount is required.');
    }
}
