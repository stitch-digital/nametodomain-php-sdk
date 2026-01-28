# Name To Domain PHP SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stitch-digital/nametodomain-php-sdk.svg?style=flat-square)](https://packagist.org/packages/stitch-digital/nametodomain-php-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/stitch-digital/nametodomain-php-sdk.svg?style=flat-square)](https://packagist.org/packages/stitch-digital/nametodomain-php-sdk)

This package is the official PHP SDK for the [Name To Domain](https://nametodomain.dev) API, built with [Saloon](https://docs.saloon.dev/) v3.

```php
use NameToDomain\PhpSdk\NameToDomain;

// Single resolution (sync)
$result = NameToDomain::make($token)->resolve(
    company: 'Stitch Digital',
    country: 'GB'
);

// Batch enrichment (async)
$job = NameToDomain::make($token)->enrichBatch(
    items: [
        ['company' => 'Stripe', 'country' => 'US'],
        ['company' => 'Spotify', 'country' => 'SE'],
    ]
);
```

Behind the scenes, the SDK uses [Saloon](https://docs.saloon.dev) to make the HTTP requests.

## Installation

```bash
composer require stitch-digital/nametodomain-php-sdk
```

To get started, we highly recommend reading
the [Name To Domain API documentation](https://stitchdigital.gitbook.io/nametodomain).

## Quick Start

```php
use NameToDomain\PhpSdk\NameToDomain;

// Single resolution (sync)
$result = NameToDomain::make($token)->resolve(
    company: 'Stitch Digital',
    country: 'GB'
);

// Batch enrichment (async)
$job = NameToDomain::make($token)->enrichBatch(
    items: [
        ['company' => 'Stripe', 'country' => 'US'],
        ['company' => 'Spotify', 'country' => 'SE'],
    ]
);

// Check batch job status and get results
$batchResult = NameToDomain::make($token)->enrichBatchJob(jobId: $job->id);

// Or iterate over all batch items (paginated)
$items = NameToDomain::make($token)->enrichBatchJobItems(jobId: $job->id)->collect()->all();
```

## Usage

To authenticate, you'll need an API token. You can create one in
the [API Dashboard at Name To Domain](https://nametodomain.dev/app/api-keys).

```php
use NameToDomain\PhpSdk\NameToDomain;

$client = NameToDomain::make('your-api-token');
```

### Setting a timeout

By default, the SDK waits 10 seconds for a response. Override via the constructor (`apiToken`, `baseUrl`, `requestTimeout`):

```php
$client = new \NameToDomain\PhpSdk\NameToDomain(
    'your-api-token',
    'https://nametodomain.dev/api/v1',
    30
);
```

### Handling errors

The SDK will throw an exception if the API returns an error. For validation errors, the SDK will throw a `ValidationException`.

```php
try {
    $client->resolve(company: '', country: 'US');
} catch (\NameToDomain\PhpSdk\Exceptions\ValidationException $exception) {
    $exception->getMessage(); // returns a string describing the errors
    
    $exception->getErrors(); // returns an array with all validation errors
    $exception->getErrorsForField('company'); // get errors for a specific field
}
```

For all other errors, the SDK will throw a `\NameToDomain\PhpSdk\Exceptions\NameToDomainException`.

```php
try {
    $client->enrichJob(jobId: 'invalid-id');
} catch (\NameToDomain\PhpSdk\Exceptions\NameToDomainException $exception) {
    $exception->getMessage();
    $exception->response; // access the Saloon Response object for debugging
}
```

## Resolve

The resolve endpoint allows you to resolve a single company name to its official website domain with a confidence score.

### Resolve a company

You can use the `resolve` method to resolve a company name and country code to its domain.

```php
$result = NameToDomain::make($token)->resolve(
    company: 'Stitch Digital',
    country: 'GB'
);
```

The response includes the original input and the resolution result. If no reliable match is found, the domain and confidence will be `null`.

### Resolve with emails

You can optionally pass email addresses for disambiguation:

```php
$result = NameToDomain::make($token)->resolve(
    company: 'Stripe',
    country: 'US',
    emails: ['support@stripe.com', 'sales@stripe.com']
);
```

## Domain enrichment

Domain enrichment runs asynchronously and returns richer data (favicon, trust signals, web metadata, company classification, email provider hints, etc.). There are single-company and batch flows.

### Enrich a single company

Create an enrichment job for one company. Poll `enrichJob(jobId)` for the result.

```php
$job = NameToDomain::make($token)->enrich(
    company: 'Stripe',
    country: 'US',
    emails: ['support@stripe.com'],
    identifier: 'stripe-001'
);

// Poll for result
$result = NameToDomain::make($token)->enrichJob($job->id);
// When completed, $result->output is a JobItem with the enriched data
```

### Enrich a single company with idempotency key

You can include an idempotency key to safely retry requests:

```php
$job = NameToDomain::make($token)->enrich(
    company: 'Stripe',
    country: 'US',
    idempotencyKey: 'my-unique-idempotency-key'
);
```

### Enrich multiple companies (batch)

Create a batch enrichment job. Each item may include `company`, `country`, and optionally `emails` and `identifier`.

```php
$job = NameToDomain::make($token)->enrichBatch(
    items: [
        ['company' => 'Stripe', 'country' => 'US', 'emails' => ['support@stripe.com'], 'identifier' => 'stripe-001'],
        ['company' => 'Spotify', 'country' => 'SE', 'identifier' => 'spotify-001'],
    ]
);
```

### Enrich batch with idempotency key

```php
$job = NameToDomain::make($token)->enrichBatch(
    items: [['company' => 'Stripe', 'country' => 'US']],
    idempotencyKey: 'my-unique-idempotency-key'
);
```

### Get a single enrich job

Use `enrichJob` to get a single-company enrichment job. The `output` field is only present when the job is completed.

```php
$result = NameToDomain::make($token)->enrichJob('01HQJXK8N3YWVF6BCMPG42X1TZ');
// $result->job and $result->output (JobItem or null)
```

### Get a batch enrich job

Use `enrichBatchJob` to get a batch job with one page of `output` and `pagination` (when completed):

```php
$result = NameToDomain::make($token)->enrichBatchJob('01HQJXK8N3YWVF6BCMPG42X1TZ', page: 1, perPage: 50);
// $result->job, $result->output (JobItem[]), $result->pagination
```

### Get batch enrich job items (paginated)

The `enrichBatchJobItems` method returns a Saloon `Paginator` over all `JobItem` DTOs across pages.

#### Iterating over items

```php
$paginator = NameToDomain::make($token)->enrichBatchJobItems(jobId: $jobId);

foreach ($paginator->items() as $item) {
    if ($item->result && $item->result['domain']) {
        echo "{$item->input['company']}: {$item->result['domain']}\n";
    }
}
```

#### Using Laravel Collections

```php
$items = NameToDomain::make($token)
    ->enrichBatchJobItems(jobId: $jobId)
    ->collect()
    ->all();
```

#### Custom pagination

```php
$paginator = NameToDomain::make($token)->enrichBatchJobItems(
    jobId: $jobId,
    page: 2,
    perPage: 100
);
```

#### Job item structure

Each `JobItem` includes:

- `id`, `identifier` (client-supplied, if provided)
- `input` (company, country, email_domains)
- `status`, `result`, `errorMessage`, `processedAt`

The `result` array can contain `company_normalized`, `domain`, `confidence`, and for enrichment: `favicon_url`, `trust`, `web_metadata`, `company_classification`, `email_provider_hints`.

## Pagination

The SDK uses [Saloon's pagination plugin](https://docs.saloon.dev/installable-plugins/pagination). The `enrichBatchJobItems()` method returns a `Paginator` that yields `JobItem` DTOs across pages. See [Saloon pagination documentation](https://docs.saloon.dev/installable-plugins/pagination) for `items()`, `collect()`, and advanced usage.

## Using Saloon requests directly

You can use the request classes directly for full control:

```php
use NameToDomain\PhpSdk\NameToDomain;
use NameToDomain\PhpSdk\Requests\Resolve\ResolveRequest;

$client = NameToDomain::make('your-api-token');
$request = new ResolveRequest('Stripe', 'US');

$response = $client->send($request)->dto();
```

## Security

If you discover any security related issues, please email support@nametodomain.dev instead of using the issue tracker.

## Credits

- [John Trickett](https://github.com/johntrickett86/)
- [Stitch Digital](https://www.stitch-digital)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
