<?php

declare(strict_types=1);

use NameToDomain\PhpSdk\Requests\Jobs\GetJobRequest;
use NameToDomain\PhpSdk\Tests\TestCase;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

uses(TestCase::class);

it('can get a job', function () {
    MockClient::global([
        GetJobRequest::class => MockResponse::fixture('get_job'),
    ]);

    $request = new GetJobRequest('01HQJXK8N3YWVF6BCMPG42X1TZ');
    $response = $this->sdk->send($request);
    $dto = $response->dto();

    expect($dto->id)->toBe('01HQJXK8N3YWVF6BCMPG42X1TZ')
        ->and($dto->status->value)->toBe('completed')
        ->and($dto->totalItems)->toBe(100);
});

it('handles not found errors', function () {
    MockClient::global([
        GetJobRequest::class => MockResponse::make(
            body: [
                'message' => 'Resource not found.',
                'error' => 'not_found',
            ],
            status: 404,
        ),
    ]);

    $request = new GetJobRequest('invalid-id');

    expect(fn () => $this->sdk->send($request))
        ->toThrow(NameToDomain\PhpSdk\Exceptions\NameToDomainException::class);
});
