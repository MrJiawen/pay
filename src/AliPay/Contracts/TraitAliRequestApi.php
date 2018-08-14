<?php

namespace Jw\Pay\AliPay\Contracts;

use Jw\Pay\AliPay\Config;

trait TraitAliRequestApi
{
    /**
     * @var Object;
     */
    protected static $instance;

    /**
     * @var
     */
    protected $config;

    /**
     * AbstractAliRequestApi constructor.
     * @param Config $config
     */
    protected function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * get Object
     * @param Config $config
     * @return TraitAliRequestApi
     * @Author jiaWen.chen
     */
    public static function getInstance(Config $config): self
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * 获取类名
     * @return string
     * @Author jiaWen.chen
     */
    public function getTypeName()
    {
        return get_class($this);
    }
}