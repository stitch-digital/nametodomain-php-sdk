<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\Monitors;

use OhDear\PhpSdk\Dto\Monitor;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

final class CreateMonitorRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected array $data
    ) {}

    public function resolveEndpoint(): string
    {
        return '/monitors';
    }

    public function createDtoFromResponse(Response $response): Monitor
    {
        return Monitor::fromResponse($response->json());
    }

    protected function defaultBody(): array
    {
        return $this->data;
    }
}
