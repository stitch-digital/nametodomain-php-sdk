<?php

declare(strict_types=1);

use NameToDomain\PhpSdk\Requests\Jobs\CreateJobRequest;
use NameToDomain\PhpSdk\Tests\TestCase;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

uses(TestCase::class);

it('can create a job', function () {
    MockClient::global([
        CreateJobRequest::class => MockResponse::fixture('create_job'),
    ]);

    $request = new CreateJobRequest([
        ['company' => 'Stripe', 'country' => 'US'],
        ['company' => 'Shopify', 'country' => 'CA'],
    ]);
    $response = $this->sdk->send($request);
    $dto = $response->dto();

    expect($dto->status->value)->toBe('pending')
        ->and($dto->totalItems)->toBe(2)
        ->and($dto->processedItems)->toBe(0);
});

it('can create a job with idempotency key', function () {
    MockClient::global([
        CreateJobRequest::class => MockResponse::fixture('create_job'),
    ]);

    $request = new CreateJobRequest(
        [
            ['company' => 'Stripe', 'country' => 'US'],
        ],
        'test-idempotency-key'
    );
    $response = $this->sdk->send($request);
    $dto = $response->dto();

    expect($dto->status->value)->toBe('pending');
});

it('handles validation errors', function () {
    MockClient::global([
        CreateJobRequest::class => MockResponse::make(
            body: [
                'message' => 'The given data was invalid.',
                'errors' => [
                    'items' => ['At least one item is required.'],
                ],
            ],
            status: 422,
        ),
    ]);

    $request = new CreateJobRequest([]);

    expect(fn () => $this->sdk->send($request))
        ->toThrow(NameToDomain\PhpSdk\Exceptions\ValidationException::class);
});
