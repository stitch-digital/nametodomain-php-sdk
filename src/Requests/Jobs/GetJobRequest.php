<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Requests\Jobs;

use NameToDomain\PhpSdk\Dto\Job;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class GetJobRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $jobId
    ) {}

    public function resolveEndpoint(): string
    {
        return "/jobs/{$this->jobId}";
    }

    public function createDtoFromResponse(Response $response): Job
    {
        $data = $response->json();

        return Job::fromResponse($data['data']);
    }
}
