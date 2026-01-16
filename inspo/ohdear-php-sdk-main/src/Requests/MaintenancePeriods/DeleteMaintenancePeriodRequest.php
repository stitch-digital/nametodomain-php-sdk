<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\MaintenancePeriods;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteMaintenancePeriodRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected int $maintenancePeriodId
    ) {}

    public function resolveEndpoint(): string
    {
        return "/maintenance-periods/{$this->maintenancePeriodId}";
    }
}
