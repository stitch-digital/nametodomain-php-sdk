<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Dto;

use NameToDomain\PhpSdk\Enums\JobItemStatus;

final class JobItem
{
    /**
     * @param  array{company: string, country: string, email_domains?: list<string>}  $input
     * @param  array{company_normalized?: string|null, domain?: string|null, confidence?: int|null, favicon_url?: string|null, trust?: array<string, mixed>, web_metadata?: array<string, mixed>, company_classification?: array<string, mixed>, email_provider_hints?: array<string, mixed>}|null  $result
     */
    public function __construct(
        public ?string $id,
        public ?string $identifier,
        public array $input,
        public JobItemStatus $status,
        public ?array $result = null,
        public ?string $errorMessage = null,
        public ?string $processedAt = null,
    ) {}

    public static function fromResponse(array $data): self
    {
        $id = $data['id'] ?? null;
        $identifier = $data['identifier'] ?? null;
        $input = $data['input'] ?? [];
        $status = isset($data['status']) && is_string($data['status'])
            ? JobItemStatus::from($data['status'])
            : JobItemStatus::Pending;
        $result = $data['result'] ?? null;
        $errorMessage = $data['error_message'] ?? null;
        $processedAt = $data['processed_at'] ?? null;

        return new self(
            id: $id,
            identifier: $identifier,
            input: $input,
            status: $status,
            result: $result,
            errorMessage: $errorMessage,
            processedAt: $processedAt,
        );
    }
}
