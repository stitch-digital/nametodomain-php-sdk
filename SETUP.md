# Setting Up Name To Domain PHP SDK in Laravel Application

This guide will help you set up the `nametodomain/nametodomain-php-sdk` package in your Laravel application using a local symlink for development.

## Prerequisites

- Laravel application in the same parent directory as this package
- Composer installed
- The SDK package is located at: `../nametodomain/packages/nametodomain-php-sdk`

## Step 1: Add Local Repository to composer.json

Add a local repository configuration to your Laravel application's `composer.json` file:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../nametodomain/packages/nametodomain-php-sdk"
        }
    ]
}
```

## Step 2: Install the Package

Run the following command in your Laravel application directory:

```bash
composer require nametodomain/nametodomain-php-sdk:@dev
```

The `@dev` constraint allows Composer to use the local development version. Composer will automatically create a symlink to the package directory.

## Step 3: Verify Installation

You can verify the package is installed correctly by checking:

```bash
composer show nametodomain/nametodomain-php-sdk
```

The output should show the package is installed and the path should point to the symlinked directory.

## Step 4: Use the SDK in Your Application

```php
use NameToDomain\PhpSdk\NameToDomain;

// In your service, controller, or wherever you need it
$client = new NameToDomain(config('services.nametodomain.api_token'));

// Resolve a company
$resolution = $client->resolve('Stripe', 'US');
echo $resolution->result['domain']; // 'stripe.com'

// Create a batch job
$job = $client->createJob([
    ['company' => 'Stripe', 'country' => 'US'],
    ['company' => 'Spotify', 'country' => 'SE'],
]);

// Get job items
foreach ($client->jobItems($job->id) as $item) {
    echo $item->result['domain'];
}
```

## Step 5: Add API Token to Configuration (Optional)

Add your Name To Domain API token to your Laravel configuration:

**config/services.php:**
```php
'nametodomain' => [
    'api_token' => env('NAMETODOMAIN_API_TOKEN'),
],
```

**.env:**
```
NAMETODOMAIN_API_TOKEN=your-api-token-here
```

## Troubleshooting

### Symlink Not Created

If Composer doesn't create a symlink automatically, you can manually create it:

```bash
cd vendor/nametodomain
rm -rf nametodomain-php-sdk
ln -s ../../../nametodomain/packages/nametodomain-php-sdk nametodomain-php-sdk
```

### Package Not Found

If Composer can't find the package, verify the path in your `composer.json` repository configuration is correct relative to your Laravel application's root directory.

### Autoload Issues

After making changes to the SDK package, you may need to regenerate the autoloader:

```bash
composer dump-autoload
```

## Development Workflow

Since the package is symlinked, any changes you make to the SDK source code will be immediately available in your Laravel application without needing to reinstall or update the package.

## Running SDK Tests

To run the SDK's test suite:

```bash
cd ../nametodomain/packages/nametodomain-php-sdk
composer install
composer test
```

## Package Structure

The SDK package is located at:
```
../nametodomain/packages/nametodomain-php-sdk/
├── src/
│   ├── NameToDomain.php
│   ├── Concerns/
│   ├── Requests/
│   ├── Dto/
│   ├── Enums/
│   └── Exceptions/
├── tests/
├── composer.json
└── README.md
```

## Notes

- The package uses PHP 8.1+ and requires Saloon v3.14+
- All dependencies will be installed automatically when you run `composer require`
- The package follows PSR-4 autoloading standards
- For production use, you would typically publish this package to Packagist instead of using a local symlink
