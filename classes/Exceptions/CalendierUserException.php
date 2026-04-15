<?php

namespace Exceptions;

use Throwable;
class CalendrierUserException extends \Exception
{
    private string $prefix = "Calendrier User Error : ";

    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($this->prefix . $message, $code, $previous);
    }
}