<?php

namespace App\Exceptions;

use Exception;

class AttendanceException extends Exception
{
    public function __construct(
        string $message,
        private readonly string $errorCode,
        private readonly int $status = 422
    ) {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }

    public function status(): int
    {
        return $this->status;
    }
}
