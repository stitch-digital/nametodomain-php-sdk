<?php

declare(strict_types=1);

use NameToDomain\PhpSdk\Requests\Enrich\EnrichBatchRequest;
use NameToDomain\PhpSdk\Requests\Enrich\GetEnrichBatchRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('can create an enrich batch job via SDK', function () {
    MockClient::global([
        EnrichBatchRequest::class => MockResponse::fixture('enrich_batch'),
    ]);

    $job = $this->sdk->enrichBatch([
        ['company' => 'Stripe', 'country' => 'US'],
        ['company' => 'Shopify', 'country' => 'CA'],
    ]);

    expect($job->status->value)->toBe('processing')
        ->and($job->totalItems)->toBe(2)
        ->and($job->processedItems)->toBe(0)
        ->and($job->failedItems)->toBe(0);
});

it('can create an enrich batch job with idempotency key', function () {
    MockClient::global([
        EnrichBatchRequest::class => MockResponse::fixture('enrich_batch'),
    ]);

    $job = $this->sdk->enrichBatch(
        [
            ['company' => 'Stripe', 'country' => 'US'],
        ],
        'test-idempotency-key'
    );

    expect($job->status->value)->toBe('processing');
});

it('can get an enrich batch job via SDK', function () {
    MockClient::destroyGlobal();
    MockClient::global([
        GetEnrichBatchRequest::class => MockResponse::fixture('get_enrich_batch'),
    ]);

    $result = $this->sdk->enrichBatchJob('01HQJXK8N3YWVF6BCMPG42X1TZ');

    expect($result->job->id)->toBe('01HQJXK8N3YWVF6BCMPG42X1TZ')
        ->and($result->job->status->value)->toBe('completed')
        ->and($result->output)->toHaveCount(2)
        ->and($result->pagination)->not->toBeNull()
        ->and($result->pagination['last_page'])->toBe(1);
});

it('can get enrich batch job items via SDK with pagination', function () {
    MockClient::destroyGlobal();
    MockClient::global([
        GetEnrichBatchRequest::class => MockResponse::fixture('get_enrich_batch'),
    ]);

    $paginator = $this->sdk->enrichBatchJobItems('01HQJXK8N3YWVF6BCMPG42X1TZ', 1, 50);

    $itemArray = iterator_to_array($paginator->items());

    expect($itemArray)->toBeArray()
        ->and(count($itemArray))->toBeGreaterThan(0)
        ->and($itemArray[0]->input['company'])->toBe('Stripe')
        ->and($itemArray[0]->result['domain'])->toBeString()
        ->and($itemArray[0]->identifier)->toBe('stripe-001');
});

it('can iterate through enrich batch job items', function () {
    MockClient::destroyGlobal();
    MockClient::global([
        GetEnrichBatchRequest::class => MockResponse::fixture('get_enrich_batch'),
    ]);

    $paginator = $this->sdk->enrichBatchJobItems('01HQJXK8N3YWVF6BCMPG42X1TZ');

    $count = 0;
    foreach ($paginator->items() as $item) {
        expect($item->input)->toBeArray()
            ->and($item->status)->toBeInstanceOf(NameToDomain\PhpSdk\Enums\JobItemStatus::class);
        $count++;
    }

    expect($count)->toBeGreaterThan(0);
});
