<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\CronCheckDefinitions;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteCronCheckDefinitionRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected int $cronCheckDefinitionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/cron-checks/{$this->cronCheckDefinitionId}";
    }
}
