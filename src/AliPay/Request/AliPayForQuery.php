<?php

namespace Jw\Pay\AliPay\Request;

use Jw\Pay\AliPay\Contracts\TraitAliRequestApi;
use Jw\Pay\AliPay\Library\AliRequestAPI;
use Jw\Pay\AliPay\Library\Support;

/**
 * 单例查询
 * Class AliPayForQuery
 * @package Jw\Pay\AliPay\Request
 */
class AliPayForQuery
{
    use TraitAliRequestApi;

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
