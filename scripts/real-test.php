<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use NameToDomain\PhpSdk\NameToDomain;

require_once __DIR__.'/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../tests/TestSupport');
$dotenv->safeLoad();

$token = $_ENV['NAME_TO_DOMAIN_API_TOKEN'] ?? null;
$baseUrl = $_ENV['NAME_TO_DOMAIN_BASE_URL'] ?? null;
$existingJobId = $_ENV['NAME_TO_DOMAIN_JOB_ID'] ?? null;

if (empty($token)) {
    echo "NAME_TO_DOMAIN_API_TOKEN is required. Copy tests/TestSupport/.env.example to .env and set your token.\n";
    exit(1);
}

$nametodomain = new NameToDomain($token, $baseUrl ?: 'https://nametodomain.dev/api/v1');

$resolution = $nametodomain->resolve('Stitch Digital', 'GB');
dump($resolution);

if ($existingJobId) {
    $jobId = $existingJobId;
} else {
    $job = $nametodomain->createJob([['company' => 'Stitch Digital', 'country' => 'GB']]);
    dump($job);
    $jobId = $job->id;
}

$job = $nametodomain->job($jobId);
dump($job);

foreach ($nametodomain->jobItems($jobId) as $item) {
    dump($item);
}
