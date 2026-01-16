<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Concerns;

use NameToDomain\PhpSdk\Dto\Job;
use NameToDomain\PhpSdk\Dto\JobItem;
use NameToDomain\PhpSdk\Requests\Jobs\CreateJobRequest;
use NameToDomain\PhpSdk\Requests\Jobs\GetJobItemsRequest;
use NameToDomain\PhpSdk\Requests\Jobs\GetJobRequest;

/** @mixin \NameToDomain\PhpSdk\NameToDomain */
trait SupportsJobEndpoints
{
    /**
     * @param  array<int, array{company: string, country: string}>  $items
     */
    public function createJob(array $items, ?string $idempotencyKey = null): Job
    {
        $request = new CreateJobRequest($items, $idempotencyKey);

        return $this->send($request)->dto();
    }

    public function job(string $jobId): Job
    {
        $request = new GetJobRequest($jobId);

        return $this->send($request)->dto();
    }

    /**
     * Get job items paginator.
     *
     * @return \Saloon\PaginationPlugin\Paginator
     */
    public function jobItems(string $jobId, int $page = 1, int $perPage = 50): \Saloon\PaginationPlugin\Paginator
    {
        $request = new GetJobItemsRequest($jobId, $page, $perPage);

        return $this->paginate($request);
    }
}
