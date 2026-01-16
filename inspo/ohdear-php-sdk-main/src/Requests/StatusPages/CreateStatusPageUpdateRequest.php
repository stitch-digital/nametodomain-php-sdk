<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\StatusPages;

use OhDear\PhpSdk\Dto\StatusPageUpdate;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

final class CreateStatusPageUpdateRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected array $data,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/status-page-updates';
    }

    public function createDtoFromResponse(Response $response): StatusPageUpdate
    {
        return StatusPageUpdate::fromResponse($response->json());
    }

    protected function defaultBody(): array
    {
        return $this->data;
    }
}
