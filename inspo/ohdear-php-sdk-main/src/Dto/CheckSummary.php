<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Dto;

final class CheckSummary
{
    public function __construct(
        public string $result,
        public ?string $summary,
    ) {}

    public static function fromResponse(array $data): self
    {
        return new self(
            result: $data['result'],
            summary: $data['summary'],
        );
    }
}
