<?php

declare(strict_types=1);

namespace App\Core\User\UserInterface\Cli;

use App\Common\Bus\QueryBusInterface;
use App\Core\User\Application\DTO\UserDTO;
use App\Core\User\Application\Query\GetInactiveUsersEmails\GetInactiveUsersEmailsQuery;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'app:user:get-inactive-users-emails',
    description: 'Add new user.'
)]
class GetInactiveUsersEmails extends Command
{
    private const OFFSET = 0;
    private const LIMIT = 10;

    public function __construct(private readonly QueryBusInterface $bus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $users = $this->bus->dispatch(new GetInactiveUsersEmailsQuery(
                $input->getArgument('offset') ?? self::OFFSET,
                $input->getArgument('limit') ?? self::LIMIT
            ));

            if (count($users) > 0) {
                $output->writeln('Inactive users emails : ');

                /** @var UserDTO $user */
                foreach ($users as $user) {
                    $output->writeln($user->getEmail());
                }
            } else {
                $output->writeln('No inactive users found.');
            }

            return Command::SUCCESS;
        } catch (Throwable $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
    }

    protected function configure(): void
    {
        $this->addArgument('offset', InputArgument::OPTIONAL, "Default offset is set to 0");
        $this->addArgument('limit', InputArgument::OPTIONAL, "Default limit is set to 10");
    }
}
