<?php

declare(strict_types=1);

use NameToDomain\PhpSdk\Requests\Jobs\GetJobItemsRequest;
use NameToDomain\PhpSdk\Tests\TestCase;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

uses(TestCase::class);

it('can get job items', function () {
    MockClient::global([
        GetJobItemsRequest::class => MockResponse::fixture('get_job_items'),
    ]);

    $request = new GetJobItemsRequest('01HQJXK8N3YWVF6BCMPG42X1TZ', 1, 50);
    $response = $this->sdk->send($request);
    $items = $request->createDtoFromResponse($response);

    expect($items)->toBeArray()
        ->and(count($items))->toBeGreaterThan(0)
        ->and($items[0]->input['company'])->toBe('Stripe');
});

it('can get job items with custom pagination', function () {
    MockClient::global([
        GetJobItemsRequest::class => MockResponse::fixture('get_job_items'),
    ]);

    $request = new GetJobItemsRequest('01HQJXK8N3YWVF6BCMPG42X1TZ', 2, 25);
    $response = $this->sdk->send($request);
    $items = $request->createDtoFromResponse($response);

    expect($items)->toBeArray();
});
