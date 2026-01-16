<?php

declare(strict_types=1);

use NameToDomain\PhpSdk\Requests\Jobs\CreateJobRequest;
use NameToDomain\PhpSdk\Requests\Jobs\GetJobItemsRequest;
use NameToDomain\PhpSdk\Requests\Jobs\GetJobRequest;
use NameToDomain\PhpSdk\Tests\TestCase;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

uses(TestCase::class);

it('can create a job via SDK', function () {
    MockClient::global([
        CreateJobRequest::class => MockResponse::fixture('create_job'),
    ]);

    $job = $this->sdk->createJob([
        ['company' => 'Stripe', 'country' => 'US'],
        ['company' => 'Shopify', 'country' => 'CA'],
    ]);

    expect($job->status->value)->toBe('pending')
        ->and($job->totalItems)->toBe(2)
        ->and($job->processedItems)->toBe(0)
        ->and($job->failedItems)->toBe(0);
});

it('can create a job with idempotency key', function () {
    MockClient::global([
        CreateJobRequest::class => MockResponse::fixture('create_job'),
    ]);

    $job = $this->sdk->createJob(
        [
            ['company' => 'Stripe', 'country' => 'US'],
        ],
        'test-idempotency-key'
    );

    expect($job->status->value)->toBe('pending');
});

it('can get a job via SDK', function () {
    MockClient::global([
        GetJobRequest::class => MockResponse::fixture('get_job'),
    ]);

    $job = $this->sdk->job('01HQJXK8N3YWVF6BCMPG42X1TZ');

    expect($job->id)->toBe('01HQJXK8N3YWVF6BCMPG42X1TZ')
        ->and($job->status->value)->toBe('completed')
        ->and($job->totalItems)->toBe(100)
        ->and($job->processedItems)->toBe(100);
});

it('can get job items via SDK with pagination', function () {
    MockClient::global([
        GetJobItemsRequest::class => MockResponse::fixture('get_job_items'),
    ]);

    $items = $this->sdk->jobItems('01HQJXK8N3YWVF6BCMPG42X1TZ', 1, 50);

    $itemArray = iterator_to_array($items);

    expect($itemArray)->toBeArray()
        ->and(count($itemArray))->toBeGreaterThan(0)
        ->and($itemArray[0]->input['company'])->toBe('Stripe')
        ->and($itemArray[0]->result['domain'])->toBeString();
});

it('can iterate through job items', function () {
    MockClient::global([
        GetJobItemsRequest::class => MockResponse::fixture('get_job_items'),
    ]);

    $items = $this->sdk->jobItems('01HQJXK8N3YWVF6BCMPG42X1TZ');

    $count = 0;
    foreach ($items as $item) {
        expect($item->input)->toBeArray()
            ->and($item->status)->toBeInstanceOf(NameToDomain\PhpSdk\Enums\JobItemStatus::class);
        $count++;
    }

    expect($count)->toBeGreaterThan(0);
});
