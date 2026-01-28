<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Concerns;

use NameToDomain\PhpSdk\Dto\EnrichBatchJobResult;
use NameToDomain\PhpSdk\Dto\EnrichJobResult;
use NameToDomain\PhpSdk\Dto\Job;
use NameToDomain\PhpSdk\Dto\JobItem;
use NameToDomain\PhpSdk\Requests\Enrich\EnrichBatchRequest;
use NameToDomain\PhpSdk\Requests\Enrich\EnrichRequest;
use NameToDomain\PhpSdk\Requests\Enrich\GetEnrichBatchRequest;
use NameToDomain\PhpSdk\Requests\Enrich\GetEnrichJobRequest;
use Saloon\PaginationPlugin\Paginator;

/** @mixin \NameToDomain\PhpSdk\NameToDomain */
trait SupportsEnrichEndpoints
{
    /**
     * @param  array<int, string>|null  $emails
     */
    public function enrich(
        string $company,
        string $country,
        ?array $emails = null,
        ?string $identifier = null,
        ?string $idempotencyKey = null,
    ): Job {
        $request = new EnrichRequest($company, $country, $emails, $identifier, $idempotencyKey);

        return $this->send($request)->dto();
    }

    public function enrichJob(string $jobId): EnrichJobResult
    {
        $request = new GetEnrichJobRequest($jobId);

        return $this->send($request)->dto();
    }

    /**
     * @param  array<int, array{company: string, country: string, emails?: array<int, string>, identifier?: string}>  $items
     */
    public function enrichBatch(array $items, ?string $idempotencyKey = null): Job
    {
        $request = new EnrichBatchRequest($items, $idempotencyKey);

        return $this->send($request)->dto();
    }

    public function enrichBatchJob(string $jobId, int $page = 1, int $perPage = 50): EnrichBatchJobResult
    {
        $request = new GetEnrichBatchRequest($jobId, $page, $perPage);
        $response = $this->send($request);
        $data = $response->json();
        $payload = $data['data'] ?? $data;

        $job = Job::fromResponse($payload);

        $output = array_map(
            fn (array $item): JobItem => JobItem::fromResponse($item),
            $payload['output'] ?? []
        );

        $pagination = $payload['pagination'] ?? null;

        return new EnrichBatchJobResult(job: $job, output: $output, pagination: $pagination);
    }

    public function enrichBatchJobItems(string $jobId, int $page = 1, int $perPage = 50): Paginator
    {
        $request = new GetEnrichBatchRequest($jobId, $page, $perPage);

        return $this->paginate($request);
    }
}
