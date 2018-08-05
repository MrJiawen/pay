<?php

namespace Jw\Pay\Exceptions;

use Throwable;

/**
 * 配置错误的异常类
 * Class InvalidSignException
 * @package Jw\Pay\Exceptions
 */
class InvalidConfigException extends Exception
{
    /**
     * InvalidConfigException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}