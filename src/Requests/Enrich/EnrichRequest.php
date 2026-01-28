<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Requests\Enrich;

use NameToDomain\PhpSdk\Dto\Job;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

final class EnrichRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<int, string>|null  $emails
     */
    public function __construct(
        protected string $company,
        protected string $country,
        protected ?array $emails = null,
        protected ?string $identifier = null,
        protected ?string $idempotencyKey = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/domain/enrich';
    }

    public function createDtoFromResponse(Response $response): Job
    {
        $data = $response->json();

        return Job::fromResponse($data['data']);
    }

    protected function defaultBody(): array
    {
        $body = [
            'company' => $this->company,
            'country' => $this->country,
        ];

        if ($this->emails !== null && $this->emails !== []) {
            $body['emails'] = $this->emails;
        }

        if ($this->identifier !== null && $this->identifier !== '') {
            $body['identifier'] = $this->identifier;
        }

        return $body;
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
