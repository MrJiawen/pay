<?php

namespace Jw\Pay\AliPay;

use Jw\Pay\AliPay\Contracts\AliPayType;
use Jw\Pay\AliPay\Request\AliPayForQuery;
use Jw\Pay\AliPay\Request\AliPayOfWap;
use Jw\Pay\AliPay\Request\AliPayOfWeb;
use Jw\Pay\Exceptions\Exception;
use Jw\Pay\Exceptions\InvalidConfigException;

/**
 * Class Config 配置项
 * @package Jw\Pay\Library
 */
class Config
{
    /**
     * @var array 配置
     */
    public $config;
    /**
     * @var array 支付加载的数据
     */
    public $payLoad;
    /**
     * @var string 支付的网关
     */
    public $gateway;

    /**
     * Config constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->checkOfInitConfig();

        $this->payLoad = [
            'app_id' => $this->config['app_id'],
            'method' => '',
            'format' => 'JSON',
            'charset' => 'utf-8',
            'sign_type' => 'RSA2',
            'version' => '1.0',
            'return_url' => $this->config['return_url'],
            'notify_url' => $this->config['notify_url'],
            'timestamp' => date('Y-m-d H:i:s'),
            'sign' => '',
            'biz_content' => '',
        ];
    }

    /**
     * 检查初始化配置的每一项是否都已配置正确
     * @Author jiaWen.chen
     */
    private function checkOfInitConfig()
    {
        $keys = [
            'app_id',
            'notify_url',
            'return_url',
            'ali_public_key',
            'seller_private_key',
            'mode'
        ];

        foreach ($keys as $item) {
            if (!in_array($item, array_keys($this->config))) {
                throw new InvalidConfigException('invalid config setting, must include [' . implode(',', $keys) . ']');
            }
        }
        if (!in_array($this->config['mode'], ['dev', 'pro'])) {
            throw new InvalidConfigException('invalid config setting, mode value must is `dev` or `pro`');
        }
    }

    /**
     *  获取方法
     * @param  $aliPay
     * @return string
     * @Author jiaWen.chen
     */
    public function getMethod($aliPay): string
    {
        switch ($aliPay->getTypeName()) {
            case AliPayOfWeb::class:
                return 'alipay.trade.page.pay';
            case AliPayOfWap::class:
                return 'alipay.trade.wap.pay';

            case AliPayForQuery::class:
                return 'alipay.trade.query';
            default:
                return new Exception('system error');
        }
    }

    /**
     * 获取 product code 值
     * @param AliPayType $aliPayType
     * @return string
     * @Author jiaWen.chen
     */
    public function getProductCode(AliPayType $aliPayType): string
    {
        switch ($aliPayType->getTypeName()) {
            case AliPayOfWeb::class:
                return 'FAST_INSTANT_TRADE_PAY';
            case AliPayOfWap::class:
                return 'QUICK_WAP_WAY';
            default:
                return new Exception('system error');
        }
    }

    /**
     * 检查 biz_content 值
     * @param $bizContent
     * @param AliPayType $aliPayType
     * @return bool
     * @throws InvalidConfigException
     * @Author jiaWen.chen
     */
    public function checkOfBizContent($bizContent, AliPayType $aliPayType): bool
    {
        switch ($aliPayType->getTypeName()) {
            case AliPayOfWeb::class:
            case AliPayOfWap::class:
                $keys = [
                    'out_trade_no',     //商户订单号，64个字符以内、可包含字母、数字、下划线；需保证在商户端不重复
                    'product_code',     // 销售产品码，与支付宝签约的产品码名称。 注：目前仅支持FAST_INSTANT_TRADE_PAY
                    'total_amount',     // 订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]
                    'subject'           // 订单标题
                ];
                break;
            default:
                return new Exception('system error');
        }

        foreach ($keys as $item) {
            if (!in_array($item, array_keys($bizContent))) {
                throw new InvalidConfigException('invalid biz_content setting, must include [' . implode(',', $keys) . ']');
            }
        }

        return true;
    }

    /**
     * 获取网关
     * @param  $aliPay
     * @return Exception|string
     * @Author jiaWen.chen
     */
    public function getGateway($aliPay): string
    {
        switch ($aliPay->getTypeName()) {
            case AliPayOfWeb::class:
            case AliPayOfWap::class:
            case AliPayForQuery::class:
                return [
                    'dev' => 'https://openapi.alipaydev.com/gateway.do',
                    'pro' => 'https://openapi.alipay.com/gateway.do'
                ][$this->config['mode']];
            default:
                return new Exception('system error');
        }
    }
}