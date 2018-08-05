<?php

namespace Jw\Pay\AliPay;

use Jw\Pay\AliPay\Contracts\AliPayType;
use Jw\Pay\AliPay\Library\Support;
use Symfony\Component\HttpFoundation\Response;

/**
 * 阿里的支付
 * Class Alipay
 * @package Jw\Pay\AliPay
 */
class Alipay
{
    /**
     * 配置文件类
     * @var Config
     */
    protected $config;

    /**
     * 支付类型
     * @var string
     */
    public $payType;

    /**
     * Alipay constructor.
     * @param Config $config
     * @param AliPayType $aliPayType
     */
    public function __construct(Config $config, AliPayType $aliPayType)
    {
        $this->config = $config;

        $this->payType = $aliPayType;
        $this->payType->setConfig($this->config);
    }

    /**
     * 进行支付
     * @param $param
     * @return mixed
     * @Author jiaWen.chen
     */
    public function pay($param)
    {
        return $this->payType->sendToPay($param);
    }
}