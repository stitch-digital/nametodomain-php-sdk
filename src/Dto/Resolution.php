<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Dto;

use InvalidArgumentException;

final class Resolution
{
    /**
     * @param  array{company: string, country: string, email_domains?: list<string>}  $input
     * @param  array{company_normalized: string|null, domain: string|null, confidence: int|null}  $result
     */
    public function __construct(
        public array $input,
        public array $result,
    ) {}

    public static function fromResponse(array $data): self
    {
        // Handle potential data wrapper
        if (isset($data['data']) && is_array($data['data'])) {
            $data = $data['data'];
        }

        // Validate that we have the expected structure
        if (! isset($data['input']) && ! isset($data['result'])) {
            $receivedKeys = empty($data) ? '(empty array)' : implode(', ', array_keys($data));
            throw new InvalidArgumentException(
                'Invalid response data: missing required keys "input" and "result". '.
                'Received keys: '.$receivedKeys.'. Full data: '.json_encode($data, JSON_PRETTY_PRINT)
            );
        }

        // Ensure we have the required keys, provide defaults if missing
        $input = $data['input'] ?? [];
        $result = $data['result'] ?? [];

        return new self(
            input: $input,
            result: $result,
        );
    }
}
