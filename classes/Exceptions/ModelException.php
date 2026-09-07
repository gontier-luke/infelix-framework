<?php

namespace Exceptions;

use Throwable;
class ModelException extends \Exception
{
    private string $prefix = "Model Error : ";

    public function __construct($message, $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($this->prefix . $message, $code, $previous);
    }
}