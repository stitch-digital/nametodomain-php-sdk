<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Dto;

final class EnrichBatchJobResult
{
    /**
     * @param  array<int, JobItem>  $output
     * @param  array{current_page: int, per_page: int, total: int, last_page: int, from: int|null, to: int|null}|null  $pagination
     */
    public function __construct(
        public Job $job,
        public array $output,
        public ?array $pagination = null,
    ) {}
}
