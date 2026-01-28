<?php

declare(strict_types=1);

use NameToDomain\PhpSdk\Requests\Enrich\EnrichBatchRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('can create an enrich batch request', function () {
    MockClient::global([
        EnrichBatchRequest::class => MockResponse::fixture('enrich_batch'),
    ]);

    $request = new EnrichBatchRequest([
        ['company' => 'Stripe', 'country' => 'US'],
        ['company' => 'Shopify', 'country' => 'CA'],
    ]);
    $response = $this->sdk->send($request);
    $dto = $response->dto();

    expect($dto->status->value)->toBe('processing')
        ->and($dto->totalItems)->toBe(2)
        ->and($dto->processedItems)->toBe(0);
});

it('can create an enrich batch request with idempotency key', function () {
    MockClient::global([
        EnrichBatchRequest::class => MockResponse::fixture('enrich_batch'),
    ]);

    $request = new EnrichBatchRequest(
        [
            ['company' => 'Stripe', 'country' => 'US'],
        ],
        'test-idempotency-key'
    );
    $response = $this->sdk->send($request);
    $dto = $response->dto();

    expect($dto->status->value)->toBe('processing');
});

it('handles validation errors for enrich batch request', function () {
    MockClient::destroyGlobal();
    MockClient::global([
        EnrichBatchRequest::class => MockResponse::make(
            body: [
                'message' => 'The given data was invalid.',
                'errors' => [
                    'items' => ['At least one item is required.'],
                ],
            ],
            status: 422,
        ),
    ]);

    $request = new EnrichBatchRequest([]);

    expect(fn () => $this->sdk->send($request))
        ->toThrow(NameToDomain\PhpSdk\Exceptions\ValidationException::class);
});
