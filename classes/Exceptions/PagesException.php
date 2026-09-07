<?php

namespace Exceptions;

use Throwable;
class PagesException extends \Exception
{
    private string $prefix = "Pages Error : ";

    public function __construct($message, $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($this->prefix . $message, $code, $previous);
    }
}