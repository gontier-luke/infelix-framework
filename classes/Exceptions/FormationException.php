<?php

namespace Exceptions;

use Throwable;

class FormationException extends \Exception
{
    private string $prefix = "Formation Error : ";

    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($this->prefix . $message, $code, $previous);
    }
}