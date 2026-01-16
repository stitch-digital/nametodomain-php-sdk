# Name To Domain PHP SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stitch-digital/nametodomain-php-sdk.svg?style=flat-square)](https://packagist.org/packages/stitch-digital/nametodomain-php-sdk)
![Tests](https://github.com/stitch-digital/nametodomain-php-sdk/workflows/run-tests/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/stitch-digital/nametodomain-php-sdk.svg?style=flat-square)](https://packagist.org/packages/nametodomain/nametodomain-php-sdk)

This package is the official PHP SDK for the [Name To Domain](https://nametodomain.dev) API, built with [Saloon](https://docs.saloon.dev/) v3.

```php
use NameToDomain\PhpSdk\NameToDomain;

$client = new NameToDomain('your-api-token');

// Single resolution
$resolution = $client->resolve('Stripe', 'US');
echo $resolution->result['domain']; // 'stripe.com'

// Create batch job
$job = $client->createJob([
    ['company' => 'Stripe', 'country' => 'US'],
    ['company' => 'Spotify', 'country' => 'SE'],
]);

// Get job items (paginated)
foreach ($client->jobItems($job->id) as $item) {
    echo $item->result['domain'];
}
```

Behind the scenes, the SDK uses [Saloon](https://docs.saloon.dev) to make the HTTP requests.

## Installation

```bash
composer require nametodomain/nametodomain-php-sdk
```

## Name To Domain documentation

To get started, we highly recommend reading
the [Name To Domain API documentation](https://stitchdigital.gitbook.io/nametodomain).

## Usage

To authenticate, you'll need an API token. You can create one in
the [API Dashboard at Name To Domain](https://nametodomain.dev/app/api-keys).

```php
use NameToDomain\PhpSdk\NameToDomain;

$client = new NameToDomain('your-api-token');
```

### Setting a timeout

By default, the SDK will wait for a response from Name To Domain for 10 seconds. You can change this by passing a `timeoutInSeconds` option to the constructor:

```php
$client = new NameToDomain('your-api-token', timeoutInSeconds: 30);
```

### Handling errors

The SDK will throw an exception if the API returns an error. For validation errors, the SDK will throw a `ValidationException`.

```php
try {
    $client->resolve('', 'US');
} catch (\NameToDomain\PhpSdk\Exceptions\ValidationException $exception) {
    $exception->getMessage(); // returns a string describing the errors
    
    $exception->errors(); // returns an array with all validation errors
}
```

For all other errors, the SDK will throw a `\NameToDomain\PhpSdk\Exceptions\NameToDomainException`.

### Resolve

The resolve endpoint allows you to resolve a single company name to its official website domain with a confidence score.

#### Resolve a company

You can use the `resolve` method to resolve a company name and country code to its domain.

```php
// returns NameToDomain\PhpSdk\Dto\Resolution
$resolution = $client->resolve('Stripe', 'US');

echo $resolution->result['domain']; // 'stripe.com'
echo $resolution->result['confidence']; // 98
echo $resolution->result['company_normalized']; // 'Stripe, Inc.'
```

The response includes the original input and the resolution result. If no reliable match is found, the domain and confidence will be `null`.

```php
$resolution = $client->resolve('Unknown Company', 'US');

if ($resolution->result['domain'] === null) {
    echo 'No reliable match found';
}
```

#### Resolve with idempotency key

You can include an idempotency key to safely retry requests:

```php
$resolution = $client->resolve('Stripe', 'US', 'my-unique-idempotency-key');
```

### Jobs

Jobs allow you to process multiple company resolutions in batch. Each job can contain up to 1,000 items and is processed asynchronously.

#### Creating a job

You can use the `createJob` method to create a batch job.

```php
// returns NameToDomain\PhpSdk\Dto\Job
$job = $client->createJob([
    ['company' => 'Stripe', 'country' => 'US'],
    ['company' => 'Spotify', 'country' => 'SE'],
    ['company' => 'Shopify', 'country' => 'CA'],
]);

echo $job->id; // Job ULID (e.g., "01HQJXK8N3YWVF6BCMPG42X1TZ")
echo $job->status->value; // 'pending'
echo $job->totalItems; // 3
```

You can find a list of attributes you can pass to the `createJob` method in the [Name To Domain API documentation](https://stitch-digital.gitbook.io/nametodomain/).

#### Creating a job with idempotency key

You can include an idempotency key to safely retry job creation:

```php
$job = $client->createJob([
    ['company' => 'Stripe', 'country' => 'US'],
], 'my-unique-idempotency-key');
```

#### Getting a single job

You can use the `job` method to get a single job.

```php
// returns NameToDomain\PhpSdk\Dto\Job
$job = $client->job('01HQJXK8N3YWVF6BCMPG42X1TZ');

echo $job->status->value; // 'completed'
echo $job->processedItems; // 100
echo $job->failedItems; // 3
```

#### Getting job items

You can use the `jobItems` method to retrieve paginated items for a batch job with their resolution results.

```php
// returns an iterator of NameToDomain\PhpSdk\Dto\JobItem
$items = $client->jobItems('01HQJXK8N3YWVF6BCMPG42X1TZ');

foreach ($items as $item) {
    if ($item->result['domain']) {
        echo "{$item->input['company']}: {$item->result['domain']}\n";
    }
}
```

You can customize pagination by passing page and per_page parameters (maximum 250 items per page):

```php
$items = $client->jobItems('01HQJXK8N3YWVF6BCMPG42X1TZ', page: 2, perPage: 100);
```

Each item includes the original input, processing status, and result (domain + confidence). Failed items include an error message.

```php
foreach ($items as $item) {
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

### Using Saloon requests directly

This SDK uses [Saloon](https://docs.saloon.dev) to make the HTTP requests. Instead of using the `NameToDomain` class, you can use the underlying request classes directly. This way, you have full power to customize the requests.

```php
use NameToDomain\PhpSdk\Requests\Resolve\ResolveRequest;

$request = new ResolveRequest('Stripe', 'US');

// raw response from the Name To Domain API
$response = $client->send($request);
```

Take a look at the [Saloon documentation](https://docs.saloon.dev) to learn more about how to customize the requests.

## Security

If you discover any security related issues, please email support@nametodomain.dev instead of using the issue tracker.

## Credits

- [John Trickett](https://github.com/johntrickett86/)
- [Stitch Digital](https://www.stitch-digital)

## License

The MIT License (MIT).
