# Changelog

All notable changes to this project will be documented in this file.

## [2.0.0] - 2025-01-XX

### Breaking changes

This release aligns the SDK with the refactored Name To Domain API. The following endpoints and methods have changed.

#### Resolve (sync)

- **Endpoint:** `POST /resolve` has been replaced by `POST /domain`.
- **`resolve(company, country, ?idempotencyKey)`** has been replaced by **`resolve(company, country, ?array $emails = null)`**.
- Idempotency is no longer supported for the resolve endpoint.
- Optional `emails` array can be provided for disambiguation.

#### Jobs replaced by Domain enrichment

The previous jobs API (`POST /jobs`, `GET /jobs/{id}`, `GET /jobs/{id}/items`) has been replaced by:

- **Single enrichment:** `POST /domain/enrich`, `GET /domain/enrich/{jobId}`
- **Batch enrichment:** `POST /domain/enrich/batch`, `GET /domain/enrich/batch/{jobId}`

**Method mapping:**

| Removed                    | Replacement                                                                 |
| -------------------------- | --------------------------------------------------------------------------- |
| `createJob(items, ?idempotencyKey)` | `enrichBatch(items, ?idempotencyKey)` – items may include `emails?`, `identifier?` |
| `job(jobId)`               | `enrichJob(jobId)` for single enrich, or `enrichBatchJob(jobId, page, perPage)` for batch |
| `jobItems(jobId, page, perPage)` | `enrichBatchJobItems(jobId, page, perPage)` – results and pagination come from `GET /domain/enrich/batch/{jobId}` |

**New methods:**

- **`enrich(company, country, ?emails, ?identifier, ?idempotencyKey)`** – create a single company enrichment job (`POST /domain/enrich`). Returns `Job`. Idempotency supported.
- **`enrichJob(jobId)`** – get a single enrichment job and its `output` when completed. Returns `EnrichJobResult` (`job` + `?output` `JobItem`).
- **`enrichBatchJob(jobId, page, perPage)`** – get a batch enrichment job with `output` and `pagination` when completed. Returns `EnrichBatchJobResult`.

#### DTOs and requests

- **`JobItem`** now includes `?string $identifier`. The `result` array may contain enrichment fields such as `favicon_url`, `trust`, `web_metadata`, `company_classification`, `email_provider_hints`.
- **`EnrichJobResult`** and **`EnrichBatchJobResult`** have been added.
- **Removed:** `CreateJobRequest`, `GetJobRequest`, `GetJobItemsRequest`.
- **Added:** `EnrichRequest`, `EnrichBatchRequest`, `GetEnrichJobRequest`, `GetEnrichBatchRequest`.

### Added

- `resolve(company, country, ?array $emails)` – optional emails for disambiguation.
- `enrich()`, `enrichJob()`, `enrichBatch()`, `enrichBatchJob()`, `enrichBatchJobItems()` – full support for the domain enrichment API.
