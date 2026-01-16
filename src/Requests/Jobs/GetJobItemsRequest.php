<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Requests\Jobs;

use NameToDomain\PhpSdk\Dto\JobItem;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class GetJobItemsRequest extends Request implements Paginatable
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $jobId,
        protected int $page = 1,
        protected int $perPage = 50,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/jobs/{$this->jobId}/items";
    }

    /**
     * @return array<int, JobItem>
     */
    public function createDtoFromResponse(Response $response): array
    {
        $data = $response->json();
        $items = $data['output'] ?? [];

        return array_map(
            fn (array $item) => JobItem::fromResponse($item),
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
