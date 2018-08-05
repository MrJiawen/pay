<?php

namespace Jw\Pay\AliPay\Request;

use Closure;
use Jw\Pay\AliPay\Config;
use Jw\Pay\AliPay\Contracts\AliPayType;
use Jw\Pay\AliPay\Library\Support;
use Symfony\Component\HttpFoundation\Response;

/**
 * web 支付方式(电脑网站支付产品)
 * Class AliPayOfWeb
 * @package Jw\Pay\AliPay\Library
 */
class AliPayOfWeb implements AliPayType
{
    /**
     * @var
     */
    protected $config;
    protected $questParam;

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
     * 获取支付类型name
     * @return string
     * @Author jiaWen.chen
     */
    public function getTypeName()
    {
        return self::class;
    }

    /**
     * 处理支付行为
     * @param array $param 对应手册中 biz_content 参数
     * @param Closure|null $callback
     * @return mixed
     * @Author jiaWen.chen
     */
    public function sendToPay(array $param, Closure $callback = null)
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

        /**
         * 2. 对整个 payLoad 进行处理
         */
        $this->sendBefore($param);

        return $this->buildPayHtml($this->config->gateway, $this->config->payLoad);
    }

    /**
     * @Author jiaWen.chen
     * @param $bizContent
     */
    protected function sendBefore($bizContent)
    {
        $this->config->payLoad['method'] = $this->config->getMethod($this);
        $this->config->payLoad['biz_content'] = json_encode($bizContent);
        $this->config->payLoad['sign'] = Support::generateSign(
            $this->config->payLoad,
            $this->config->config['seller_private_key']
        );

        $this->config->gateway = $this->config->getGateWay($this);
    }

    /**
     * Build Html response.
     * @param $endpoint
     * @param $payload
     * @return Response
     * @Author jiaWen.chen
     */
    protected function buildPayHtml($endpoint, $payload): Response
    {
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='" . $endpoint . "' method='POST'>";
        foreach ($payload as $key => $val) {
            $val = str_replace("'", '&apos;', $val);
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
        $sHtml .= "<input type='submit' value='ok' style='display:none;''></form>";
        $sHtml .= "<script>document.forms['alipaysubmit'].submit();</script>";

        return Response::create($sHtml);
    }
}