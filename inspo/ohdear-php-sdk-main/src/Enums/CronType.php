<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Enums;

enum CronType: string
{
    case Simple = 'simple';
    case Cron = 'cron';
}
