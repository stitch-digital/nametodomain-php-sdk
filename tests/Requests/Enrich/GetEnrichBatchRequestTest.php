<?php

declare(strict_types=1);

use NameToDomain\PhpSdk\Requests\Enrich\GetEnrichBatchRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('can get enrich batch job items', function () {
    MockClient::global([
        GetEnrichBatchRequest::class => MockResponse::fixture('get_enrich_batch'),
    ]);

    $request = new GetEnrichBatchRequest('01HQJXK8N3YWVF6BCMPG42X1TZ', 1, 50);
    $response = $this->sdk->send($request);
    $items = $request->createDtoFromResponse($response);

    expect($items)->toBeArray()
        ->and(count($items))->toBeGreaterThan(0)
        ->and($items[0]->input['company'])->toBe('Stripe')
        ->and($items[0]->identifier)->toBe('stripe-001');
});

it('can get enrich batch job items with custom pagination', function () {
    MockClient::global([
        GetEnrichBatchRequest::class => MockResponse::fixture('get_enrich_batch'),
    ]);

    $request = new GetEnrichBatchRequest('01HQJXK8N3YWVF6BCMPG42X1TZ', 2, 25);
    $response = $this->sdk->send($request);
    $items = $request->createDtoFromResponse($response);

    expect($items)->toBeArray();
});
