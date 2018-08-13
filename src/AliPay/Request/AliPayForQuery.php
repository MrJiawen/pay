<?php

namespace Jw\Pay\AliPay\Request;

use Jw\Pay\AliPay\Config;
use Jw\Pay\AliPay\Library\AliRequestAPI;
use Jw\Pay\AliPay\Library\Support;

/**
 * 单例查询
 * Class AliPayForQuery
 * @package Jw\Pay\AliPay\Request
 */
class AliPayForQuery
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
     * AliPayForQuery constructor.
     * @param Config $config
     */
    protected function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * get Object
     * @param Config $config
     * @return AliPayForQuery
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

    /**
     * handle query
     * @Author jiaWen.chen
     * @param String $outTradeNo
     * @return \Illuminate\Support\Collection
     */
    public function query(String $outTradeNo)
    {
        $this->config->payLoad['method'] = $this->config->getMethod($this);

        $bizContent = [
            'out_trade_no' => (string)$outTradeNo
        ];
        $this->config->payLoad['biz_content'] = json_encode($bizContent);

        $this->config->payLoad['sign'] = Support::generateSign(
            $this->config->payLoad,
            $this->config->config['seller_private_key']
        );
        $this->config->gateway = $this->config->getGateWay($this);

        return AliRequestAPI::getInstance($this->config)->aliPayRequest(
            $this->config->gateway, $this->config->payLoad
        );
    }
}
