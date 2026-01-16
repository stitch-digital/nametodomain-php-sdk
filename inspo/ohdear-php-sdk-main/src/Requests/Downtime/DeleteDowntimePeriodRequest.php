<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\Downtime;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteDowntimePeriodRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected int $downtimePeriodId,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/downtime/{$this->downtimePeriodId}";
    }
}
