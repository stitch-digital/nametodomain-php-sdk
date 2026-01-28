<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Dto;

final class EnrichJobResult
{
    public function __construct(
        public Job $job,
        public ?JobItem $output = null,
    ) {}
}
