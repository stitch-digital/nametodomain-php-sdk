<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\LighthouseReports;

use OhDear\PhpSdk\Dto\LighthouseReport;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class GetLighthouseReportsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected int $monitorId,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/monitors/{$this->monitorId}/lighthouse-reports";
    }

    public function createDtoFromResponse(Response $response): array
    {
        return LighthouseReport::collect($response);
    }
}
