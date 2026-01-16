<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Requests\Jobs;

use NameToDomain\PhpSdk\Dto\Job;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

final class CreateJobRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<int, array{company: string, country: string}>  $items
     */
    public function __construct(
        protected array $items,
        protected ?string $idempotencyKey = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/jobs';
    }

    public function createDtoFromResponse(Response $response): Job
    {
        $data = $response->json();

        return Job::fromResponse($data['data']);
    }

    protected function defaultBody(): array
    {
        return [
            'items' => $this->items,
        ];
    }

    protected function defaultHeaders(): array
    {
        $headers = [];

        if ($this->idempotencyKey !== null) {
            $headers['Idempotency-Key'] = $this->idempotencyKey;
        }

        return $headers;
    }
}
