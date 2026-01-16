# Name To Domain PHP SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stitch-digital/nametodomain-php-sdk.svg?style=flat-square)](https://packagist.org/packages/stitch-digital/nametodomain-php-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/stitch-digital/nametodomain-php-sdk.svg?style=flat-square)](https://packagist.org/packages/stitch-digital/nametodomain-php-sdk)

This package is the official PHP SDK for the [Name To Domain](https://nametodomain.dev) API, built with [Saloon](https://docs.saloon.dev/) v3.

```php
use NameToDomain\PhpSdk\NameToDomain;

// Single resolution
$result = NameToDomain::make($token)->resolve(
    company: 'Stitch Digital',
    country: 'GB'
);

// Create batch job
$job = NameToDomain::make($token)->createJob(
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

// Single resolution
$result = NameToDomain::make($token)->resolve(
    company: 'Stitch Digital',
    country: 'GB'
);

// Create batch job
$job = NameToDomain::make($token)->createJob(
    items: [
        ['company' => 'Stripe', 'country' => 'US'],
        ['company' => 'Spotify', 'country' => 'SE'],
    ]
);

// Check job status
$jobStatus = NameToDomain::make($token)->job(jobId: $job->id);

// Get all job items
$items = NameToDomain::make($token)->jobItems(jobId: $job->id)->collect()->all();
```

## Usage

To authenticate, you'll need an API token. You can create one in
the [API Dashboard at Name To Domain](https://nametodomain.dev/app/api-keys).

```php
use NameToDomain\PhpSdk\NameToDomain;

$client = NameToDomain::make('your-api-token');
```

### Setting a timeout

By default, the SDK will wait for a response from Name To Domain for 10 seconds. You can change this by passing a `requestTimeout` option:

```php
$client = NameToDomain::make(
    apiToken: 'your-api-token',
    requestTimeout: 30
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
    $client->job(jobId: 'invalid-id');
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

### Resolve with idempotency key

You can include an idempotency key to safely retry requests:

```php
$result = NameToDomain::make($token)->resolve(
    company: 'Stripe',
    country: 'US',
    idempotencyKey: 'my-unique-idempotency-key'
);
```

## Jobs

Jobs allow you to process multiple company resolutions in batch.

### Creating a job

You can use the `createJob` method to create a batch job.

```php
$records = [
    ['company' => 'Stripe', 'country' => 'US'],
    ['company' => 'Spotify', 'country' => 'SE'],
];

$job = NameToDomain::make($token)->createJob(
    items: $records
);
```

### Creating a job with idempotency key

You can include an idempotency key to safely retry job creation:

```php
$job = NameToDomain::make($token)->createJob(
    items: [
        ['company' => 'Stripe', 'country' => 'US'],
    ],
    idempotencyKey: 'my-unique-idempotency-key'
);
```

### Getting a single job

You can use the `job` method to get a single job and check its status.

```php
$jobId = '01KF3TH8MCBFAZ98MFYB6TS63H';

$job = NameToDomain::make($token)->job(jobId: $jobId);
```

### Getting job items

The `jobItems` method returns a Saloon `Paginator` instance, giving you full flexibility in how you consume the paginated results. This leverages Saloon's powerful pagination features.

#### Iterating over items

The simplest way to get all job items is to iterate over them using the `items()` method:

```php
$paginator = NameToDomain::make($token)->jobItems(jobId: $jobId);

foreach ($paginator->items() as $item) {
    // $item is a NameToDomain\PhpSdk\Dto\JobItem
    if ($item->result && $item->result['domain']) {
        echo "{$item->input['company']}: {$item->result['domain']}\n";
    }
}
```

#### Using Laravel Collections

If you're using Laravel (or have `illuminate/collections` installed), you can use the `collect()` method to get a `LazyCollection`:

```php
// Get all items as an array
$items = NameToDomain::make($token)
    ->jobItems(jobId: $jobId)
    ->collect()
    ->all();

// Use collection methods for filtering, mapping, etc.
$domains = NameToDomain::make($token)
    ->jobItems(jobId: $jobId)
    ->collect()
    ->filter(fn($item) => $item->result && $item->result['domain'])
    ->map(fn($item) => $item->result['domain'])
    ->all();
```

The `LazyCollection` is memory-efficient and only loads one page at a time, making it perfect for processing large batches.

#### Iterating over responses

You can also iterate directly over the paginator to get each page as a Saloon `Response`:

```php
$paginator = NameToDomain::make($token)->jobItems(jobId: $jobId);

foreach ($paginator as $response) {
    $status = $response->status();
    $data = $response->json();
    // Process each page's response
}
```

#### Custom pagination parameters

You can specify the starting page and items per page:

```php
$paginator = NameToDomain::make($token)->jobItems(
    jobId: $jobId,
    page: 2,
    perPage: 100
);
```

#### Job item structure

Each job item includes the original input, processing status, and result:

```php
foreach ($paginator->items() as $item) {
    // $item is a NameToDomain\PhpSdk\Dto\JobItem
    // Structure:
    // $item->id => '01HQJXK8N4ABCD1234567890XY' (or null if not available)
    // $item->input => ['company' => 'Stripe', 'country' => 'US']
    // $item->status => NameToDomain\PhpSdk\Enums\JobItemStatus
    // $item->result => ['company_normalized' => '...', 'domain' => '...', 'confidence' => 98] (or null)
    // $item->errorMessage => null (or error message if failed)
    // $item->processedAt => '2026-01-16T16:34:45+00:00' (or null)
    
    echo "Status: {$item->status->value}\n";
    
    if ($item->result) {
        echo "Domain: {$item->result['domain']}\n";
        echo "Confidence: {$item->result['confidence']}\n";
    }
    
    if ($item->errorMessage) {
        echo "Error: {$item->errorMessage}\n";
    }
}
```

## Pagination

The SDK uses [Saloon's pagination plugin](https://docs.saloon.dev/installable-plugins/pagination) to handle paginated responses. The `jobItems()` method returns a `Paginator` instance that provides several ways to consume the results.

### Why use a Paginator?

Saloon's paginators are memory-efficient - they only keep one page in memory at a time. This means you can iterate through thousands of pages and millions of results without running out of memory.

### Available methods

#### `items()` - Iterate over individual items

Returns a generator that yields each `JobItem` DTO across all pages:

```php
$paginator = NameToDomain::make($token)->jobItems(jobId: $jobId);

foreach ($paginator->items() as $item) {
    // Process each JobItem
    echo $item->result['domain'];
}
```

#### `collect()` - Get a LazyCollection

Returns a Laravel `LazyCollection` (requires `illuminate/collections`):

```php
// Get all items as an array
$allItems = $paginator->collect()->all();

// Use collection methods
$domains = $paginator->collect()
    ->filter(fn($item) => $item->result && $item->result['domain'])
    ->map(fn($item) => $item->result['domain'])
    ->values()
    ->all();
```

#### Direct iteration - Get each page as a Response

Iterate directly over the paginator to get each page:

```php
foreach ($paginator as $response) {
    $items = $response->dto(); // Get items from this page
    // Process the page
}
```

### Advanced pagination features

For more advanced pagination features like asynchronous pagination, request pooling, and custom pagination logic, see the [Saloon pagination documentation](https://docs.saloon.dev/installable-plugins/pagination).

## Using Saloon requests directly

This SDK uses [Saloon](https://docs.saloon.dev) to make the HTTP requests. Instead of using the `NameToDomain` class, you can use the underlying request classes directly. This way, you have full power to customize the requests.

```php
use NameToDomain\PhpSdk\NameToDomain;
use NameToDomain\PhpSdk\Requests\Resolve\ResolveRequest;

$client = NameToDomain::make('your-api-token');
$request = ResolveRequest::make('Stripe', 'US');

// Get raw response from the Name To Domain API
$response = $client->send($request)->json();

// Or get the DTO directly
$response = $client->send($request)->dto();
```

Take a look at the [Saloon documentation](https://docs.saloon.dev) to learn more about how to customize the requests.

## Security

If you discover any security related issues, please email support@nametodomain.dev instead of using the issue tracker.

## Credits

- [John Trickett](https://github.com/johntrickett86/)
- [Stitch Digital](https://www.stitch-digital)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
