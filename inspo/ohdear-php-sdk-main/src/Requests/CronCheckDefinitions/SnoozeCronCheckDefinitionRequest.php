<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\CronCheckDefinitions;

use OhDear\PhpSdk\Dto\CronCheckDefinition;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

final class SnoozeCronCheckDefinitionRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected int $cronCheckDefinitionId,
        protected int $minutes,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/cron-checks/{$this->cronCheckDefinitionId}/snooze";
    }

    public function createDtoFromResponse(Response $response): CronCheckDefinition
    {
        return CronCheckDefinition::fromResponse($response->json());
    }

    protected function defaultBody(): array
    {
        return [
            'minutes' => $this->minutes,
        ];
    }
}
