<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\StatusPages;

use OhDear\PhpSdk\Dto\StatusPage;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class GetStatusPagesRequest extends Request implements Paginatable
{
    protected Method $method = Method::GET;

    public function __construct(
        protected ?int $teamId = null
    ) {}

    public function resolveEndpoint(): string
    {
        return '/status-pages';
    }

    /** @return array<int, StatusPage> */
    public function createDtoFromResponse(Response $response): array
    {
        return StatusPage::collect($response->json('data'));
    }

    protected function defaultQuery(): array
    {
        $query = [];

        if ($this->teamId !== null) {
            $query['filter']['team_id'] = $this->teamId;
        }

        return $query;
    }
}
