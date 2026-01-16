<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Dto;

use NameToDomain\PhpSdk\Enums\JobStatus;

final class Job
{
    public function __construct(
        public string $id,
        public JobStatus $status,
        public int $totalItems,
        public int $processedItems,
        public int $failedItems,
        public string $createdAt,
        public ?string $completedAt = null,
    ) {}

    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['id'],
            status: JobStatus::from($data['status']),
            totalItems: $data['total_items'],
            processedItems: $data['processed_items'],
            failedItems: $data['failed_items'],
            createdAt: $data['created_at'],
            completedAt: $data['completed_at'] ?? null,
        );
    }
}
