<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\Monitors;

use OhDear\PhpSdk\Dto\Monitor;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class GetMonitorsRequest extends Request implements Paginatable
{
    protected Method $method = Method::GET;

    public function __construct(
        protected ?int $teamId = null
    ) {}

    public function resolveEndpoint(): string
    {
        return '/monitors';
    }

    /** @return array<int, Monitor> */
    public function createDtoFromResponse(Response $response): array
    {
        return Monitor::collect($response->json('data'));
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
