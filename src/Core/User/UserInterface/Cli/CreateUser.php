<?php

declare(strict_types=1);

namespace App\Core\User\UserInterface\Cli;

use App\Core\User\Application\Command\CreateUser\CreateUserCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Add new user.'
)]
class CreateUser extends Command
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->bus->dispatch(new CreateUserCommand(
                $input->getArgument('email')
            ));

            $output->writeln('User ' . $input->getArgument('email') . ' created!');

            return Command::SUCCESS;
        } catch (\Throwable $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, "Email address is required.");
    }
}
