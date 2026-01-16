<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Dto;

use NameToDomain\PhpSdk\Enums\JobItemStatus;

final class JobItem
{
    /**
     * @param  array{company: string, country: string}  $input
     * @param  array{company_normalized: string|null, domain: string|null, confidence: int|null}|null  $result
     */
    public function __construct(
        public ?string $id,
        public array $input,
        public JobItemStatus $status,
        public ?array $result = null,
        public ?string $errorMessage = null,
        public ?string $processedAt = null,
    ) {}

    public static function fromResponse(array $data): self
    {
        // Handle missing fields gracefully - the API might not return all fields
        // The JobItemResource currently only returns input and result
        $id = $data['id'] ?? null;
        $input = $data['input'] ?? [];
        $status = isset($data['status']) && is_string($data['status'])
            ? JobItemStatus::from($data['status'])
            : JobItemStatus::Pending;
        $result = $data['result'] ?? null;
        $errorMessage = $data['error_message'] ?? null;
        $processedAt = $data['processed_at'] ?? null;

        return new self(
            id: $id,
            input: $input,
            status: $status,
            result: $result,
            errorMessage: $errorMessage,
            processedAt: $processedAt,
        );
    }
}
