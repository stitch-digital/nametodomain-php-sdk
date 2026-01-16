<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\Checks;

use OhDear\PhpSdk\Dto\Check;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

final class RequestCheckRunRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected int $checkId,
        protected array $httpClientHeaders = []
    ) {}

    public function resolveEndpoint(): string
    {
        return "/checks/{$this->checkId}/request-run";
    }

    public function createDtoFromResponse(Response $response): Check
    {
        return Check::fromResponse($response->json());
    }

    protected function defaultBody(): array
    {
        $body = [];

        if (! empty($this->httpClientHeaders)) {
            $body['httpClientHeaders'] = $this->httpClientHeaders;
        }

        return $body;
    }
}
