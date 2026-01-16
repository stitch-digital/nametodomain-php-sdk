<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Helpers;

use DateTimeImmutable;
use InvalidArgumentException;

final class Helpers
{
    public static function convertDateFormat(string $date): string
    {
        $dateTime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date);

        if ($dateTime === false) {
            throw new InvalidArgumentException("Invalid date format. Expected 'Y-m-d H:i:s' format.");
        }

        return $dateTime->format('YmdHis');
    }
}
