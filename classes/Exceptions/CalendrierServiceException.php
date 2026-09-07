<?php

namespace Exceptions;

use Throwable;
class CalendrierServiceException extends \Exception
{
    private string $prefix = "Calendrier Service Error : ";

    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($this->prefix . $message, $code, $previous);
    }
}