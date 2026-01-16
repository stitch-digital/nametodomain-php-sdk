<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\CronCheckDefinitions;

use OhDear\PhpSdk\Dto\CronCheckDefinition;
use OhDear\PhpSdk\Enums\CronType;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

final class UpdateCronCheckDefinitionRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        protected int $cronCheckDefinitionId,
        protected array $data,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/cron-checks/{$this->cronCheckDefinitionId}";
    }

    public function createDtoFromResponse(Response $response): CronCheckDefinition
    {
        return CronCheckDefinition::fromResponse($response->json());
    }

    protected function defaultBody(): array
    {
        $data = $this->data;

        // Convert CronType enum to string value for API
        if (isset($data['type']) && $data['type'] instanceof CronType) {
            $data['type'] = $data['type']->value;
        }

        return $data;
    }
}
