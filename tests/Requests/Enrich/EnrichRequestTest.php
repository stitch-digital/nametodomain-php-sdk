<?php

declare(strict_types=1);

use NameToDomain\PhpSdk\Requests\Enrich\EnrichRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('can create an enrich request', function () {
    MockClient::global([
        EnrichRequest::class => MockResponse::fixture('enrich'),
    ]);

    $request = new EnrichRequest('Stripe', 'US');
    $response = $this->sdk->send($request);
    $dto = $response->dto();

    expect($dto->status->value)->toBe('processing')
        ->and($dto->totalItems)->toBe(1)
        ->and($dto->processedItems)->toBe(0);
});

it('can create an enrich request with idempotency key', function () {
    MockClient::global([
        EnrichRequest::class => MockResponse::fixture('enrich'),
    ]);

    $request = new EnrichRequest('Stripe', 'US', null, null, 'test-idempotency-key');
    $response = $this->sdk->send($request);
    $dto = $response->dto();

    expect($dto->status->value)->toBe('processing');
});

it('handles validation errors for enrich request', function () {
    MockClient::destroyGlobal();
    MockClient::global([
        EnrichRequest::class => MockResponse::make(
            body: [
                'message' => 'The given data was invalid.',
                'errors' => [
                    'company' => ['The company name is required.'],
                ],
            ],
            status: 422,
        ),
    ]);

    $request = new EnrichRequest('', 'US');

    expect(fn () => $this->sdk->send($request))
        ->toThrow(NameToDomain\PhpSdk\Exceptions\ValidationException::class);
});
