<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk;

use NameToDomain\PhpSdk\Concerns\SupportsEnrichEndpoints;
use NameToDomain\PhpSdk\Concerns\SupportsResolveEndpoints;
use NameToDomain\PhpSdk\Exceptions\NameToDomainException;
use NameToDomain\PhpSdk\Exceptions\ValidationException;
use NameToDomain\PhpSdk\Requests\Enrich\GetEnrichBatchRequest;
use ReflectionClass;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\PaginationPlugin\PagedPaginator;
use Saloon\PaginationPlugin\Paginator;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Saloon\Traits\Plugins\HasTimeout;
use Throwable;

final class NameToDomain extends Connector implements HasPagination
{
    use AcceptsJson;
    use AlwaysThrowOnErrors;
    use HasTimeout;
    use SupportsEnrichEndpoints;
    use SupportsResolveEndpoints;

    public function __construct(
        protected readonly string $apiToken,
        protected readonly string $baseUrl = 'https://nametodomain.dev/api/v1',
        protected int $requestTimeout = 10,
    ) {
        //
    }

    public function getRequestException(Response $response, ?Throwable $senderException): Throwable
    {
        if ($response->status() === 422) {
            return new ValidationException($response);
        }

        return new NameToDomainException(
            $response,
            $senderException?->getMessage() ?? 'Request failed',
            $senderException?->getCode() ?? 0,
        );
    }

    public function resolveBaseUrl(): string
    {
        return mb_rtrim($this->baseUrl, '/');
    }

    public function paginate(Request $request): Paginator
    {
        $perPageLimit = null;

        if ($request instanceof GetEnrichBatchRequest) {
            $reflection = new ReflectionClass($request);
            $perPageProperty = $reflection->getProperty('perPage');
            $perPageProperty->setAccessible(true);
            $perPageLimit = $perPageProperty->getValue($request);
        }

        $paginator = new class(connector: $this, request: $request) extends PagedPaginator
        {
            protected function isLastPage(Response $response): bool
            {
                $currentPage = $response->json('data.pagination.current_page');
                $lastPage = $response->json('data.pagination.last_page');

                if ($currentPage === null || $lastPage === null) {
                    return true;
                }

                return $currentPage >= $lastPage;
            }

            protected function getPageItems(Response $response, Request $request): array
            {
                return $request->createDtoFromResponse($response);
            }

            protected function getTotalPages(Response $response): int
            {
                return $response->json('data.pagination.last_page', 1);
            }
        };

        if ($perPageLimit !== null) {
            $paginator->setPerPageLimit($perPageLimit);
        }

        return $paginator;
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->apiToken);
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
}
