<?php

namespace App\Models\Enums;


class TransactionStatusEnum extends Enumerate
{
    const PENDING = 1;
    const PAYED = 2;
    const CANCELLED = 3;
}
