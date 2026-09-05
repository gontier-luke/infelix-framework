<?php

namespace Exceptions;

use Throwable;
class AssetsServiceException extends \Exception
{
    private string $prefix = "Assets Service Error : ";

    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($this->prefix . $message, $code, $previous);
    }
}