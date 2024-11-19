<?php

declare(strict_types=1);

namespace App\Core\User\Application\Query\GetInactiveUsersEmails;

class GetInactiveUsersEmailsQuery
{
    public function __construct(private readonly int $offset = 0, private readonly int $limit = 10)
    {
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
