<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Requests\Enrich;

use NameToDomain\PhpSdk\Dto\EnrichJobResult;
use NameToDomain\PhpSdk\Dto\Job;
use NameToDomain\PhpSdk\Dto\JobItem;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class GetEnrichJobRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $jobId,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/domain/enrich/{$this->jobId}";
    }

    public function createDtoFromResponse(Response $response): EnrichJobResult
    {
        $data = $response->json();
        $payload = $data['data'] ?? $data;

        $job = Job::fromResponse($payload);

        $output = null;
        if (isset($payload['output']) && is_array($payload['output'])) {
            $output = JobItem::fromResponse($payload['output']);
        }

        return new EnrichJobResult(job: $job, output: $output);
    }
}
