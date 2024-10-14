<?php

class SliderException extends Exception
{
    private string $prefix = "Slider Error : ";

    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($this->prefix . $message, $code, $previous);
    }
}