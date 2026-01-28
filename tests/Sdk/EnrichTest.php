<?php

declare(strict_types=1);

use NameToDomain\PhpSdk\Requests\Enrich\EnrichRequest;
use NameToDomain\PhpSdk\Requests\Enrich\GetEnrichJobRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('can enrich a single company via SDK', function () {
    MockClient::global([
        EnrichRequest::class => MockResponse::fixture('enrich'),
    ]);

    $job = $this->sdk->enrich('Stripe', 'US');

    expect($job->status->value)->toBe('processing')
        ->and($job->totalItems)->toBe(1)
        ->and($job->processedItems)->toBe(0)
        ->and($job->failedItems)->toBe(0);
});

it('can enrich a single company with idempotency key', function () {
    MockClient::global([
        EnrichRequest::class => MockResponse::fixture('enrich'),
    ]);

    $job = $this->sdk->enrich('Stripe', 'US', null, null, 'test-idempotency-key');

    expect($job->status->value)->toBe('processing');
});

it('can get an enrich job via SDK', function () {
    MockClient::destroyGlobal();
    MockClient::global([
        GetEnrichJobRequest::class => MockResponse::fixture('get_enrich_job'),
    ]);

    $result = $this->sdk->enrichJob('01HQJXK8N3YWVF6BCMPG42X1TZ');

    expect($result->job->id)->toBe('01HQJXK8N3YWVF6BCMPG42X1TZ')
        ->and($result->job->status->value)->toBe('completed')
        ->and($result->output)->not->toBeNull()
        ->and($result->output->identifier)->toBe('stripe-001')
        ->and($result->output->result['domain'])->toBe('stripe.com');
});
