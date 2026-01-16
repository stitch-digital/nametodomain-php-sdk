<?php

declare(strict_types=1);

use NameToDomain\PhpSdk\Requests\Resolve\ResolveRequest;
use NameToDomain\PhpSdk\Tests\TestCase;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

uses(TestCase::class);

it('can resolve a company', function () {
    MockClient::global([
        ResolveRequest::class => MockResponse::fixture('resolve'),
    ]);

    $request = new ResolveRequest('Stripe', 'US');
    $response = $this->sdk->send($request);
    $dto = $response->dto();

    expect($dto->result['domain'])->toBe('stripe.com')
        ->and($dto->input['company'])->toBe('Stripe')
        ->and($dto->input['country'])->toBe('US');
});

it('can resolve a company with idempotency key', function () {
    MockClient::global([
        ResolveRequest::class => MockResponse::fixture('resolve'),
    ]);

    $request = new ResolveRequest('Stripe', 'US', 'test-idempotency-key');
    $response = $this->sdk->send($request);
    $dto = $response->dto();

    expect($dto->result['domain'])->toBe('stripe.com');
});

it('handles validation errors', function () {
    MockClient::global([
        ResolveRequest::class => MockResponse::make(
            body: [
                'message' => 'The given data was invalid.',
                'errors' => [
                    'company' => ['The company name is required.'],
                ],
            ],
            status: 422,
        ),
    ]);

    $request = new ResolveRequest('', 'US');

    expect(fn () => $this->sdk->send($request))
        ->toThrow(NameToDomain\PhpSdk\Exceptions\ValidationException::class);
});
