<?php

namespace DigitalThreads\LiqPay\Exceptions;

use Exception;

final class InvalidCallbackRequestException extends Exception
{
    /**
     * @return static
     */
    public static function signature()
    {
        return new static('Signature missmatch!');
    }

    /**
     * @return static
     */
    public static function request()
    {
        return new static("Invalid request!");
    }
}
