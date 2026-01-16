<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\CronCheckDefinitions;

use OhDear\PhpSdk\Dto\CronCheckDefinition;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class GetCronCheckDefinitionsRequest extends Request implements Paginatable
{
    protected Method $method = Method::GET;

    public function __construct(
        protected int $monitorId,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/monitors/{$this->monitorId}/cron-checks";
    }

    /** @return array<int, CronCheckDefinition> */
    public function createDtoFromResponse(Response $response): array
    {
        return CronCheckDefinition::collect($response->json('data'));
    }
}
