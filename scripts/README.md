# Real API Test Scripts

This directory contains scripts for running **real** API calls against the NameToDomain API from your local machine. They are intended for manual verification during development—for example, to confirm that a new endpoint or change behaves correctly against the live (or staging) API.

The main script is `real-test.php`, which exercises the resolve and job endpoints using credentials and options from `tests/TestSupport/.env`.

---

## Prerequisites

- PHP 8.1+ with the project’s dependencies installed (`composer install`)
- A valid NameToDomain API token
- [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) (in `require-dev`)

---

## One-Time Setup

### 1. Create your local `.env` file

From the project root:

```bash
cp tests/TestSupport/.env.example tests/TestSupport/.env
```

The file `tests/TestSupport/.env` is listed in `.gitignore` and will not be committed.

### 2. Set your API token

Edit `tests/TestSupport/.env` and set a real token:

```env
NAME_TO_DOMAIN_API_TOKEN=your-actual-api-token-here
```

### 3. Optional variables

| Variable | Required | Description |
|----------|----------|-------------|
| `NAME_TO_DOMAIN_API_TOKEN` | Yes | Your NameToDomain API token. |
| `NAME_TO_DOMAIN_BASE_URL` | No | API base URL. Default: `https://nametodomain.dev/api/v1`. Override for a staging or local API. |
| `NAME_TO_DOMAIN_JOB_ID` | No | When set, the script **skips** `createJob` and uses this ID for `job()` and `jobItems()`. Use this to re-run only the job fetch and job-items steps against an existing job. |

---

## Running `real-test.php`

From the project root:

```bash
php scripts/real-test.php
```

The script will:

1. Load `tests/TestSupport/.env` (if it exists).
2. Check that `NAME_TO_DOMAIN_API_TOKEN` is set; if not, it prints a short message and exits with code `1`.
3. Call, in order:
   - **Resolve** — `resolve('Stitch Digital', 'GB')` and `dump()` the `Resolution`.
   - **Jobs** — Either create a new job or use `NAME_TO_DOMAIN_JOB_ID` if set, then `job($jobId)` and `jobItems($jobId)`, `dump()`-ing the results.

Output uses PHP’s `dump()` (from `symfony/var-dumper`, available via dev dependencies such as Pest). If `dump()` is not available in your environment, you can replace it with `print_r()` or `var_export()` in the script.

---

## What the script does (in code)

| Step | SDK method | Purpose |
|------|------------|---------|
| 1 | `resolve('Stitch Digital', 'GB')` | Single company/country resolution. |
| 2 | `createJob([['company' => 'Stitch Digital', 'country' => 'GB']])` | Creates a batch job (skipped if `NAME_TO_DOMAIN_JOB_ID` is set). |
| 3 | `job($jobId)` | Fetches job status and metadata. |
| 4 | `jobItems($jobId)` | Iterates over job items (paginated); each item is `dump()`-ed. |

---

## How to modify and work with the script

### Changing companies and countries

Edit the arguments passed to `resolve` and `createJob`:

```php
$resolution = $nametodomain->resolve('Your Company Name', 'US');
// ...
$job = $nametodomain->createJob([
    ['company' => 'Company A', 'country' => 'GB'],
    ['company' => 'Company B', 'country' => 'DE'],
]);
```

### Testing only resolve

Comment out or remove the jobs block (from `if ($existingJobId)` through the `foreach ($nametodomain->jobItems(...))` loop). The script will then only run `resolve` and `dump($resolution)`.

### Testing only job and job items (no new job)

Set `NAME_TO_DOMAIN_JOB_ID` in `tests/TestSupport/.env` to an existing job ID. The script skips `createJob` and uses that ID for `job()` and `jobItems()`.

### Using a different base URL (staging, local)

Set in `tests/TestSupport/.env`:

```env
NAME_TO_DOMAIN_BASE_URL=https://staging.nametodomain.dev/api/v1
```

The script passes this into the `NameToDomain` constructor when it is non-empty.

### Adding an idempotency key

The SDK supports optional idempotency keys:

- `resolve(string $company, string $country, ?string $idempotencyKey = null)`
- `createJob(array $items, ?string $idempotencyKey = null)`

You can pass a third argument, for example:

```php
$resolution = $nametodomain->resolve('Stitch Digital', 'GB', 'my-unique-key-123');
$job = $nametodomain->createJob([['company' => 'Stitch Digital', 'country' => 'GB']], 'job-key-456');
```

### Pagination and job items

`jobItems($jobId)` returns a Saloon paginator. The script iterates over all pages. To limit to the first page only, you can replace the `foreach` with:

```php
$paginator = $nametodomain->jobItems($jobId, page: 1, perPage: 10);
foreach ($paginator as $item) {
    dump($item);
    break; // or use a counter to stop after N items
}
```

Or use the paginator’s `getPage()` / `getIterator()` as needed.

### Replacing `dump()` with something else

If `dump()` is not available or you prefer plain text:

```php
// Instead of: dump($resolution);
print_r($resolution);
// or
var_export($resolution, true);
```

### Running against a real API from the SDK repo

1. Ensure `vendor/` is up to date: `composer install`.
2. Use `tests/TestSupport/.env` for secrets and `NAME_TO_DOMAIN_BASE_URL`; do not commit `.env`.
3. Run: `php scripts/real-test.php`.

You can duplicate `real-test.php` (e.g. `real-test-resolve-only.php`) and trim or extend it for specific workflows; keep using `tests/TestSupport/.env` and the same bootstrap (Dotenv + `NameToDomain` instantiation) so credentials stay in one place.

---

## Troubleshooting

| Situation | What to do |
|-----------|------------|
| `NAME_TO_DOMAIN_API_TOKEN is required...` | Create `tests/TestSupport/.env` from `.env.example` and set `NAME_TO_DOMAIN_API_TOKEN`. |
| `Call to undefined function dump()` | Ensure dev deps are installed (`composer install`) so `symfony/var-dumper` is present, or replace `dump()` with `print_r()` / `var_export()`. |
| 401 / 403 from the API | Check that the token in `.env` is valid and has the right permissions. |
| 422 or validation errors | Confirm `company` and `country` (and optional `idempotencyKey`) match the API’s expectations. |
| `NAME_TO_DOMAIN_JOB_ID` not used | The script treats empty string as “not set”. Use a non-empty job ID. |

---

## File layout

```
scripts/
├── README.md         # This file
└── real-test.php     # Script that runs resolve + job + jobItems

tests/TestSupport/
├── .env              # Your local env (gitignored); create from .env.example
└── .env.example      # Template with NAME_TO_DOMAIN_* variables
```
