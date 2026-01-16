<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\Monitors;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteMonitorRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected int $monitorId
    ) {}

    public function resolveEndpoint(): string
    {
        return "/monitors/{$this->monitorId}";
    }
}
