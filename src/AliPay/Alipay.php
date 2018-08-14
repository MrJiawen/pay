<?php

namespace Jw\Pay\AliPay;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Jw\Pay\AliPay\Contracts\AliPayType;
use Jw\Pay\AliPay\Library\Support;
use Jw\Pay\AliPay\Request\AliPayForGetAccessToken;
use Jw\Pay\AliPay\Request\AliPayForGetAuthCode;
use Jw\Pay\AliPay\Request\AliPayForQuery;
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
    public function __construct(Config $config, AliPayType $aliPayType = null)
    {
        $this->config = $config;

        $this->payType = $aliPayType;
        if (!empty($aliPayType)) {
            $this->payType->setConfig($this->config);
        }
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

        if (Support::verifySign($data, $this->config->config['ali_public_key'])) {
            return new Collection($data);
        }

        \Log::warning('Alipay Sign Verify FAILED', $data);

        throw new InvalidSignException('Alipay Sign Verify FAILED', $data);
    }

    /**
     * Reply success to alipay.
     * @return Response
     * @Author jiaWen.chen
     */
    public function success(): Response
    {
        return Response::create('success');
    }

    /**
     * 查询订单
     * @param string $outTradeNo
     * @return Collection
     * @Author jiaWen.chen
     */
    public function query(string $outTradeNo)
    {
        return AliPayForQuery::getInstance($this->config)->query($outTradeNo);
    }

    /**
     * 用户授权第一步 ： 获取 auth_code
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @Author jiaWen.chen
     */
    public function getAuthCode()
    {
        return AliPayForGetAuthCode::getInstance($this->config)->handle();
    }

    /**
     * 用户授权的第二部 ： 使用返回的 auth_code 换取 access_token
     * @Author jiaWen.chen
     * @param string $appAuthCode
     * @return void
     */
    public function getAccessToken(string $appAuthCode)
    {
        return AliPayForGetAccessToken::getInstance($this->config)->handle($appAuthCode);
    }
}