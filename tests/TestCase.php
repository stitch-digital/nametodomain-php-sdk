<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Tests;

use NameToDomain\PhpSdk\NameToDomain;
use Saloon\MockConfig;
use Saloon\Http\Faking\MockClient;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected NameToDomain $sdk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sdk = new NameToDomain('test-api-token');

        MockConfig::setFixturePath(__DIR__.'/Fixtures/Saloon');
        
        // Reset mock client before each test
        MockClient::destroyGlobal();
    }

    protected function tearDown(): void
    {
        // Clean up mock client after each test
        MockClient::destroyGlobal();
        
        parent::tearDown();
    }
}
