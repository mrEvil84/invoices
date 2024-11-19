<?php

namespace App\Core\Invoice\Domain\Status;

enum InvoiceStatus: string
{
    case NEW = 'new';
    case PAID = 'paid';
    case CANCELED = 'canceled';

    public static function validStatuses(): string
    {
        return 'new,paid,canceled';
    }
}
