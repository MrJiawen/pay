<?php

namespace Jw\Pay\AliPay\Contracts;

use Jw\Pay\AliPay\Config;

/**
 * Interface AliPayType (阿里的支付方式)
 * @package Jw\Pay\AliPay\Contracts
 */
interface AliPayType
{
    /**
     * 支付类型实际名称
     * @return mixed
     * @Author jiaWen.chen
     */
    public function getTypeName();

    /**
     * 获取配置类
     * @param Config $config
     * @return mixed
     * @Author jiaWen.chen
     */
    public function setConfig(Config $config);

    /**
     * 处理支付行为
     * @param array $param
     * @return mixed
     * @Author jiaWen.chen
     */
    public function sendToPay(array $param);
}