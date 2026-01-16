<?php

declare(strict_types=1);

namespace NameToDomain\PhpSdk\Exceptions;

use Exception;
use Saloon\Http\Response;

final class NameToDomainException extends Exception
{
    public ?Response $response = null;

    public function __construct(Response $response, string $message, int $code)
    {
        parent::__construct($message, $code);

        $this->response = $response;
    }
}
