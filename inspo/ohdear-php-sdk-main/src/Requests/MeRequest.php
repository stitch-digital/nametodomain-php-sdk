<?php

declare(strict_types=1);

namespace OhDear\PhpSdk\Requests;

use OhDear\PhpSdk\Dto\User;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class MeRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
    ) {}

    public function resolveEndpoint(): string
    {
        return '/me';
    }

    public function createDtoFromResponse(Response $response): User
    {
        return User::fromResponse($response->json());
    }
}
