<?php

namespace Jw\Pay\AliPay\Request;

use Jw\Pay\AliPay\Config;
use Jw\Pay\AliPay\Contracts\AliPayType;
use Jw\Pay\AliPay\Library\AliRequestAPI;
use Jw\Pay\AliPay\Library\Support;

/**
 * web 支付方式(电脑网站支付产品)
 * Class AliPayOfWeb
 * @package Jw\Pay\AliPay\Library
 */
class AliPayForScan implements AliPayType
{
    /**
     * @var
     */
    protected $config;

    /**
     * 支付类型实际名称
     * @return mixed
     * @Author jiaWen.chen
     */
    public function getTypeName()
    {
        return get_class($this);
    }

    /**
     * 获取配置类
     * @param Config $config
     * @return mixed
     * @Author jiaWen.chen
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 处理支付行为
     * @param array $param
     * @param string $appAuthToken
     * @return mixed
     * @Author jiaWen.chen
     */
    public function sendToPay(array $param,string $appAuthToken = '')
    {
        /**
         *  1. 支付之前对数据，进行加工处理
         */
        // 1. 设置 biz_content 的必填值
        $param['product_code'] = $this->config->getProductCode($this);

        // 2. 设置 biz_content 选填值
        if (!empty($callback)) {
            $param = call_user_func($callback, $param);
        }

        // 3. 对 biz_content 必填值 验证
        $this->config->checkOfBizContent($param, $this);
        $param['out_trade_no'] = (string)$param['out_trade_no'];
        $param['total_amount'] = round($param['total_amount'], 2);

        // 4.  为空就去除
        if (empty($param['product_code'])) {
            unset($param['product_code']);
        }
        /**
         * 2. 对整个 payLoad 进行处理
         */
        $this->sendBefore($param, $appAuthToken);

        \Log::debug('Paying A Web/Wap Order:', ['gateway' => $this->config->gateway, 'payLoad' => $this->config->payLoad]);

        return AliRequestAPI::getInstance($this->config)->aliPayRequest(
            $this->config->gateway, $this->config->payLoad
        );
    }

    /**
     * @Author jiaWen.chen
     * @param $bizContent
     * @param $appAuthToken
     */
    protected function sendBefore($bizContent, $appAuthToken)
    {
        $this->config->payLoad['method'] = $this->config->getMethod($this);
        $this->config->payLoad['app_auth_token'] = $appAuthToken;
        $this->config->payLoad['biz_content'] = json_encode($bizContent);
        $this->config->payLoad['sign'] = Support::generateSign(
            $this->config->payLoad,
            $this->config->config['seller_private_key']
        );

        $this->config->gateway = $this->config->getGateWay($this);
    }
}