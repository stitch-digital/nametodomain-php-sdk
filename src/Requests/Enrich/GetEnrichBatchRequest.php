<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Requests\Enrich;

use NameToDomain\PhpSdk\Dto\JobItem;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class GetEnrichBatchRequest extends Request implements Paginatable
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $jobId,
        protected int $page = 1,
        protected int $perPage = 50,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/domain/enrich/batch/{$this->jobId}";
    }

    /**
     * @return array<int, JobItem>
     */
    public function createDtoFromResponse(Response $response): array
    {
        $data = $response->json();
        $payload = $data['data'] ?? $data;
        $items = $payload['output'] ?? [];

        return array_map(
            fn (array $item): JobItem => JobItem::fromResponse($item),
            $items
        );
    }

    protected function defaultQuery(): array
    {
        return [
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }
}
