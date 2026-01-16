<?php

declare(strict_types=1);

use NameToDomain\PhpSdk\Requests\Resolve\ResolveRequest;
use NameToDomain\PhpSdk\Tests\TestCase;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

uses(TestCase::class);

it('can resolve a company via SDK', function () {
    MockClient::global([
        ResolveRequest::class => MockResponse::fixture('resolve'),
    ]);

    $resolution = $this->sdk->resolve('Stripe', 'US');

    expect($resolution->result['domain'])->toBe('stripe.com')
        ->and($resolution->input['company'])->toBe('Stripe')
        ->and($resolution->input['country'])->toBe('US')
        ->and($resolution->result['confidence'])->toBeInt();
});

it('can resolve a company with idempotency key', function () {
    MockClient::global([
        ResolveRequest::class => MockResponse::fixture('resolve'),
    ]);

    $resolution = $this->sdk->resolve('Stripe', 'US', 'test-idempotency-key');

    expect($resolution->result['domain'])->toBe('stripe.com');
});

it('handles null domain when no match found', function () {
    MockClient::global([
        ResolveRequest::class => MockResponse::fixture('resolve_no_match'),
    ]);

    $resolution = $this->sdk->resolve('Unknown Company', 'US');

    expect($resolution->result['domain'])->toBeNull()
        ->and($resolution->result['confidence'])->toBeNull();
});
