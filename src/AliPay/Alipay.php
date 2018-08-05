<?php

namespace Jw\Pay\AliPay;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Jw\Pay\AliPay\Contracts\AliPayType;
use Jw\Pay\AliPay\Library\Support;
use Jw\Pay\Exceptions\InvalidSignException;
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

    /**
     * Verfiy sign.
     * @param null $content
     * @param bool $refund
     * @return Collection
     * @throws InvalidSignException
     * @Author jiaWen.chen
     */
    public function verify($content = null, $refund = false): Collection
    {
        $request = Request::createFromGlobals();

        $data = $request->request->count() > 0 ? $request->request->all() : $request->query->all();

        $data = Support::encoding($data, 'utf-8', $data['charset'] ?? 'gb2312');

        \Log::debug('Receive Alipay Request:', $data);

        if (Support::verifySign($data, $this->config->get('ali_public_key'))) {
            return new Collection($data);
        }

        \Log::warning('Alipay Sign Verify FAILED', $data);

        throw new InvalidSignException('Alipay Sign Verify FAILED', $data);
    }
}