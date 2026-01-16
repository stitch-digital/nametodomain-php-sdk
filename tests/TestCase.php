<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Tests;

use NameToDomain\PhpSdk\NameToDomain;
use Saloon\Http\Faking\MockConfig;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected NameToDomain $sdk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sdk = new NameToDomain('test-api-token');

        MockConfig::setFixturePath(__DIR__.'/Fixtures/Saloon');
    }
}
