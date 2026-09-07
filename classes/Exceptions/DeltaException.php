<?php

namespace Exceptions;

use Throwable;

class DeltaException extends \Exception
{
    private string $prefix = "Delta Error : ";

    public function __construct($message, $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($this->prefix . $message, $code, $previous);
    }
}