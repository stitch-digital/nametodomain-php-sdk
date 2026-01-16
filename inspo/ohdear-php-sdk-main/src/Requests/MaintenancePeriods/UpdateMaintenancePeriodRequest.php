<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\MaintenancePeriods;

use OhDear\PhpSdk\Dto\MaintenancePeriod;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

final class UpdateMaintenancePeriodRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        protected int $maintenancePeriodId,
        protected array $maintenancePeriodData
    ) {}

    public function resolveEndpoint(): string
    {
        return "/maintenance-periods/{$this->maintenancePeriodId}";
    }

    public function createDtoFromResponse(Response $response): MaintenancePeriod
    {
        return MaintenancePeriod::fromResponse($response->json());
    }

    protected function defaultBody(): array
    {
        return $this->maintenancePeriodData;
    }
}
