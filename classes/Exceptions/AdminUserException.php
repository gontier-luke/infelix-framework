<?php

namespace Exceptions;

use Throwable;
class AdminUserException extends \Exception
{
    private string $prefix = "Admin User Error : ";

    public function __construct($message, $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($this->prefix . $message, $code, $previous);
    }
}