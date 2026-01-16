<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\MaintenancePeriods;

use OhDear\PhpSdk\Dto\MaintenancePeriod;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

final class StartMaintenancePeriodRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected int $monitorId,
        protected ?int $stopMaintenanceAfterSeconds = null,
        protected ?string $name = null
    ) {}

    public function resolveEndpoint(): string
    {
        return "/monitors/{$this->monitorId}/start-maintenance";
    }

    public function createDtoFromResponse(Response $response): MaintenancePeriod
    {
        return MaintenancePeriod::fromResponse($response->json());
    }

    protected function defaultBody(): array
    {
        $body = [];

        if ($this->stopMaintenanceAfterSeconds !== null) {
            $body['stop_maintenance_after_seconds'] = $this->stopMaintenanceAfterSeconds;
        }

        if ($this->name !== null) {
            $body['name'] = $this->name;
        }

        return $body;
    }
}
