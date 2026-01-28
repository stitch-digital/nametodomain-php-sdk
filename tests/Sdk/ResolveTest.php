<?php

declare(strict_types=1);

use NameToDomain\PhpSdk\Requests\Resolve\ResolveRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

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

it('can resolve a company with emails', function () {
    MockClient::global([
        ResolveRequest::class => MockResponse::fixture('resolve'),
    ]);

    $resolution = $this->sdk->resolve('Stripe', 'US', ['support@stripe.com']);

    expect($resolution->result['domain'])->toBe('stripe.com');
});

it('handles null domain when no match found', function () {
    MockClient::destroyGlobal();
    MockClient::global([
        ResolveRequest::class => MockResponse::fixture('resolve_no_match'),
    ]);

    $resolution = $this->sdk->resolve('Unknown Company', 'US');

    expect($resolution->result['domain'])->toBeNull()
        ->and($resolution->result['confidence'])->toBeNull();
});
