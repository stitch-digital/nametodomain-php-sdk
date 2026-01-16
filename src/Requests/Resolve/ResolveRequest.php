<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Requests\Resolve;

use NameToDomain\PhpSdk\Dto\Resolution;
use RuntimeException;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

final class ResolveRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $company,
        protected string $country,
        protected ?string $idempotencyKey = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/resolve';
    }

    public function createDtoFromResponse(Response $response): Resolution
    {
        $data = $response->json();

        // @phpstan-ignore-next-line
        if (! is_array($data)) {
            throw new RuntimeException(
                'Invalid response format: expected array, got '.gettype($data).'. Response body: '.$response->body()
            );
        }

        // If response is empty or doesn't have expected structure, log for debugging
        if (empty($data) || (! isset($data['input']) && ! isset($data['result']) && ! isset($data['data']))) {
            throw new RuntimeException(
                'Unexpected response structure. Expected keys: input, result. Got: '.json_encode(array_keys($data)).'. Full response: '.$response->body()
            );
        }

        return Resolution::fromResponse($data);
    }

    protected function defaultBody(): array
    {
        return [
            'company' => $this->company,
            'country' => $this->country,
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
