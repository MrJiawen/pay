<?php

namespace Jw\Pay\AliPay\Library;

use Jw\Pay\Exceptions\InvalidConfigException;
use Jw\Pay\Library\ArrayTool;
use Jw\Pay\Library\StringTool;

/**
 * 公共类方法
 * Class Support
 * @package Jw\Pay\AliPay\Library
 */
class Support
{
    /**
     * 密钥生成器
     * @param array $parmas
     * @param null $privateKey
     * @return string
     * @throws InvalidConfigException
     * @Author jiaWen.chen
     */
    public static function generateSign(array $parmas, $privateKey = null): string
    {
        if (is_null($privateKey)) {
            throw new InvalidConfigException('Missing Alipay Config -- [private_key]');
        }

        if (StringTool::endsWith($privateKey, '.pem')) {
            $privateKey = openssl_pkey_get_private($privateKey);
        } else {
            $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" .
                wordwrap($privateKey, 64, "\n", true) .
                "\n-----END RSA PRIVATE KEY-----";
        }

        openssl_sign(self::getSignContent($parmas), $sign, $privateKey, OPENSSL_ALGO_SHA256);

        return base64_encode($sign);
    }

    /**
     *  Get signContent that is to be signed.
     * @param array $data
     * @param bool $verify
     * @return string
     * @Author jiaWen.chen
     */
    protected static function getSignContent(array $data, $verify = false): string
    {
        $data = self::encoding($data, $data['charset'] ?? 'gb2312', 'utf-8');

        ksort($data);

        $stringToBeSigned = '';
        foreach ($data as $k => $v) {
            if ($verify && $k != 'sign' && $k != 'sign_type') {
                $stringToBeSigned .= $k . '=' . $v . '&';
            }
            if (!$verify && $v !== '' && !is_null($v) && $k != 'sign' && '@' != substr($v, 0, 1)) {
                $stringToBeSigned .= $k . '=' . $v . '&';
            }
        }

        return trim($stringToBeSigned, '&');
    }

    /**
     * Convert encoding.
     * @param $data
     * @param string $to
     * @param string $from
     * @return array
     * @Author jiaWen.chen
     */
    public static function encoding($data, $to = 'utf-8', $from = 'gb2312'): array
    {
        return ArrayTool::encoding((array)$data, $to, $from);
    }

    /**
     * Verfiy sign.
     * @param array $data
     * @param null $publicKey
     * @param bool $sync
     * @param null $sign
     * @return bool
     * @throws InvalidConfigException
     * @Author jiaWen.chen
     */
    public static function verifySign(array $data, $publicKey = null, $sync = false, $sign = null): bool
    {
        if (is_null($publicKey)) {
            throw new InvalidConfigException('Missing Alipay Config -- [ali_public_key]');
        }

        if (StringTool::endsWith($publicKey, '.pem')) {
            $publicKey = openssl_pkey_get_public($publicKey);
        } else {
            $publicKey = "-----BEGIN PUBLIC KEY-----\n".
                wordwrap($publicKey, 64, "\n", true).
                "\n-----END PUBLIC KEY-----";
        }

        $sign = $sign ?? $data['sign'];

        $toVerify = $sync ? mb_convert_encoding(json_encode($data, JSON_UNESCAPED_UNICODE), 'gb2312', 'utf-8') :
            self::getSignContent($data, true);

        return openssl_verify($toVerify, base64_decode($sign), $publicKey, OPENSSL_ALGO_SHA256) === 1;
    }
}