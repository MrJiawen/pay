<?php

namespace Jw\Pay\AliPay\Request;

use Jw\Pay\AliPay\Contracts\TraitAliRequestApi;
use Jw\Pay\AliPay\Library\AliRequestAPI;
use Jw\Pay\AliPay\Library\Support;

/**
 * 单例认证
 * Class AliPayForGetAccessToken
 * @package Jw\Pay\AliPay\Request
 */
class AliPayForGetAccessToken
{
    use TraitAliRequestApi;

    /**
     * handle method
     * @param $appAuthCode
     * @return \Illuminate\Support\Collection
     * @Author jiaWen.chen
     */
    public function handle($appAuthCode)
    {
        $this->config->payLoad = array_only($this->config->payLoad,
            ['app_id', 'method', 'format', 'charset', 'sign_type', 'sign', 'timestamp', 'version', 'app_auth_token','biz_content']
        );

        $this->config->payLoad['method'] = $this->config->getMethod($this);
        $this->config->payLoad['grant_type'] = $this->config->getMethod($this);

        $bizContent = [
            'grant_type' => 'authorization_code',
            'code' => (string)$appAuthCode
        ];
        $this->config->payLoad['biz_content'] = json_encode($bizContent);

        $this->config->payLoad['sign'] = Support::generateSign(
            $this->config->payLoad,
            $this->config->config['seller_private_key']
        );

        $this->config->gateway = $this->config->getGateway($this);

        return AliRequestAPI::getInstance($this->config)->aliPayRequest(
            $this->config->gateway, $this->config->payLoad
        );

    }
}
