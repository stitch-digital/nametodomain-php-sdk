<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Concerns;

use NameToDomain\PhpSdk\Dto\Resolution;
use NameToDomain\PhpSdk\Requests\Resolve\ResolveRequest;

/** @mixin \NameToDomain\PhpSdk\NameToDomain */
trait SupportsResolveEndpoints
{
    /**
     * @param  array<int, string>|null  $emails
     */
    public function resolve(string $company, string $country, ?array $emails = null): Resolution
    {
        $request = new ResolveRequest($company, $country, $emails);

        return $this->send($request)->dto();
    }
}
