<?php

declare(strict_types=1);

use NameToDomain\PhpSdk\Requests\Enrich\GetEnrichJobRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('can get an enrich job', function () {
    MockClient::global([
        GetEnrichJobRequest::class => MockResponse::fixture('get_enrich_job'),
    ]);

    $request = new GetEnrichJobRequest('01HQJXK8N3YWVF6BCMPG42X1TZ');
    $response = $this->sdk->send($request);
    $dto = $response->dto();

    expect($dto->job->id)->toBe('01HQJXK8N3YWVF6BCMPG42X1TZ')
        ->and($dto->job->status->value)->toBe('completed')
        ->and($dto->output)->not->toBeNull()
        ->and($dto->output->result['domain'])->toBe('stripe.com');
});

it('handles not found errors for get enrich job', function () {
    MockClient::destroyGlobal();
    MockClient::global([
        GetEnrichJobRequest::class => MockResponse::make(
            body: [
                'message' => 'Resource not found.',
                'error' => 'not_found',
            ],
            status: 404,
        ),
    ]);

    $request = new GetEnrichJobRequest('invalid-id');

    expect(fn () => $this->sdk->send($request))
        ->toThrow(NameToDomain\PhpSdk\Exceptions\NameToDomainException::class);
});
