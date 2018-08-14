<?php

namespace Jw\Pay\AliPay\Request;

use Jw\Pay\AliPay\Config;
use Jw\Pay\AliPay\Contracts\TraitAliRequestApi;

/**
 * 单例认证
 * Class AliPayForQuery
 * @package Jw\Pay\AliPay\Request
 */
class AliPayForGetAuthCode
{
    use TraitAliRequestApi;

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @Author jiaWen.chen
     */
    public function handle()
    {
        $this->config->payLoad = [
            'app_id' => $this->config->config['app_id'],
            'redirect_uri' => $this->config->config['notify_url']
        ];
        $this->config->gateway = $this->config->getGateway($this);

        return redirect($this->config->gateway . '?' . http_build_query($this->config->payLoad));
    }
}
