<?php

declare(strict_types=1);

namespace App\Core\Invoice\UserInterface\Cli;

use App\Common\Bus\QueryBusInterface;
use App\Core\Invoice\Application\DTO\InvoiceDTO;
use App\Core\Invoice\Application\Query\GetInvoicesByStatusAndAmountGreater\GetInvoicesByStatusAndAmountGreaterQuery;
use App\Core\Invoice\Domain\Status\InvoiceStatus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use ValueError;

#[AsCommand(
    name: 'app:invoice:get-by-status-and-amount',
    description: 'Pobieranie identyfikatorów faktur dla wybranego statusu i kwot większych od'
)]
class GetInvoices extends Command
{
    public function __construct(private readonly QueryBusInterface $bus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $invoices = $this->bus->dispatch(new GetInvoicesByStatusAndAmountGreaterQuery(
                (int)$input->getArgument('amount'),
                InvoiceStatus::from($input->getArgument('status'))
            ));

            if (count($invoices) > 0) {
                $output->writeln('Founded invoices ids : ');

                /** @var InvoiceDTO $invoice */
                foreach ($invoices as $invoice) {
                    $output->writeln($invoice->id);
                }
            } else {
                $output->writeln('No invoices found.');
            }

            return Command::SUCCESS;
        } catch (ValueError $exception) {
            $output->writeln('Invalid invoice status');
            $output->writeln($exception->getMessage());
            $output->writeln('Valid statuses: [' .  InvoiceStatus::validStatuses() . ' ] ');
            return Command::FAILURE;
        } catch (Throwable $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
    }

    protected function configure(): void
    {
        $this->addArgument('status', InputArgument::REQUIRED, 'Invoice status required.');
        $this->addArgument('amount', InputArgument::REQUIRED, 'Invoice amount required.');
    }
}
