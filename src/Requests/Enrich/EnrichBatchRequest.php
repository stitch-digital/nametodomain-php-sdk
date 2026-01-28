<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Requests\Enrich;

use NameToDomain\PhpSdk\Dto\Job;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

final class EnrichBatchRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<int, array{company: string, country: string, emails?: array<int, string>, identifier?: string}>  $items
     */
    public function __construct(
        protected array $items,
        protected ?string $idempotencyKey = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/domain/enrich/batch';
    }

    public function createDtoFromResponse(Response $response): Job
    {
        $data = $response->json();

        return Job::fromResponse($data['data']);
    }

    protected function defaultBody(): array
    {
        $items = array_map(function (array $item): array {
            $entry = [
                'company' => $item['company'],
                'country' => $item['country'],
            ];

            if (! empty($item['emails'] ?? [])) {
                $entry['emails'] = $item['emails'];
            }

            if (! empty($item['identifier'] ?? '')) {
                $entry['identifier'] = (string) $item['identifier'];
            }

            return $entry;
        }, $this->items);

        return ['items' => $items];
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
