<?php

namespace Exceptions;

use Throwable;
class MailException extends \Exception
{
    private string $prefix = "Mail Error : ";

    public function __construct($message, $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($this->prefix . $message, $code, $previous);
    }
}