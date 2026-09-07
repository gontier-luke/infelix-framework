<?php

namespace Exceptions;

use Throwable;
class ModuleException extends \Exception
{
    private string $prefix = "Module Error : ";

    public function __construct($message, $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($this->prefix . $message, $code, $previous);
    }
}