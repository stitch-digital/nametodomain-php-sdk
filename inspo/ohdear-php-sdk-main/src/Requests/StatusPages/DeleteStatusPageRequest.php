<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests\StatusPages;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteStatusPageRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected int $statusPageId,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/status-pages/{$this->statusPageId}";
    }
}
