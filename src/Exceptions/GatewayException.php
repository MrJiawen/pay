<?php

namespace Jw\Pay\Exceptions;

use Throwable;

/**
 * 网关异常类
 * Class GatewayException
 * @package Jw\Pay\Exceptions
 */
class GatewayException extends Exception
{
    /**
     * GatewayException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}