<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Enums;

enum JobItemStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
}
