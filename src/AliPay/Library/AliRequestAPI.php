<?php

namespace Jw\Pay\AliPay\Library;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Jw\Pay\AliPay\Config;
use Jw\Pay\Exceptions\GatewayException;
use Jw\Pay\Exceptions\InvalidSignException;
use Psr\Http\Message\ResponseInterface;

class AliRequestAPI
{
    /**
     * @var Object;
     */
    protected static $instance;

    /**
     * @var
     */
    protected $config;

    /**
     * AliPayForQuery constructor.
     * @param Config $config
     */
    protected function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * get Object
     * @param Config $config
     * @return AliRequestAPI
     * @Author jiaWen.chen
     */
    public static function getInstance(Config $config): self
    {
        if (!(self::$instance instanceof self)) {

            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     *  ali request
     * @param string $gateway
     * @param array $queryParam
     * @return Collection
     * @throws GatewayException
     * @throws InvalidSignException
     * @Author jiaWen.chen
     */
    public function aliPayRequest(string $gateway, array $queryParam)
    {
        Log::debug('Request To Alipay Api', [$gateway, $queryParam]);

        $queryParam = array_filter($queryParam, function ($value) {
            return ($value == '' || is_null($value)) ? false : true;
        });

        $result = mb_convert_encoding($this->post($gateway, '', $queryParam), 'utf-8', 'gb2312');

        $result = json_decode($result, true);

        $method = str_replace('.', '_', $queryParam['method']) . '_response';

        if (!isset($result['sign']) || !isset($result[$method]['code']) || $result[$method]['code'] != '10000') {
            throw new GatewayException(
                'Get Alipay API Error:' . $result[$method]['msg'] . ($result[$method]['sub_code'] ?? '') . 'data:' . json_encode($result),
                $result[$method]['code']
            );
        }

        if (Support::verifySign($result[$method], $this->config->config['ali_public_key'], true, $result['sign'])) {
            return new Collection($result[$method]);
        }

        Log::warning('Alipay Sign Verify FAILED', $result);

        throw new InvalidSignException('Alipay Sign Verify FAILED' . json_encode($result));
    }

    /**
     * Send a POST request.
     * @param $gateway
     * @param $endpoint
     * @param $data
     * @param array $options
     * @return mixed|string
     * @Author jiaWen.chen
     */
    protected function post($gateway, $endpoint, $data, $options = [])
    {
        if (!is_array($data)) {
            $options['body'] = $data;
        } else {
            $options['form_params'] = $data;
        }

        return $this->request($gateway, 'post', $endpoint, $options);
    }

    /**
     * Send request.
     * @param $gateway
     * @param $method
     * @param $endpoint
     * @param array $options
     * @return mixed|string
     * @Author jiaWen.chen
     */
    protected function request($gateway, $method, $endpoint, $options = [])
    {
        return $this->unwrapResponse($this->getHttpClient($this->getBaseOptions($gateway))->{$method}($endpoint, $options));
    }

    /**
     * Return http client.
     * @param array $options
     * @return Client
     * @Author jiaWen.chen
     */
    protected function getHttpClient(array $options = [])
    {
        return new Client($options);
    }

    /**
     * Get base options.
     * @param $gateway
     * @return array
     * @Author jiaWen.chen
     */
    protected function getBaseOptions($gateway)
    {
        $options = [
            'base_uri' => $gateway,
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
        ];

        return $options;
    }

    /**
     *  Convert response
     * @param ResponseInterface $response
     * @return mixed|string
     * @Author jiaWen.chen
     */
    protected function unwrapResponse(ResponseInterface $response)
    {
        $contentType = $response->getHeaderLine('Content-Type');
        $contents = $response->getBody()->getContents();

        if (false !== stripos($contentType, 'json') || stripos($contentType, 'javascript')) {
            return json_decode($contents, true);
        } elseif (false !== stripos($contentType, 'xml')) {
            return json_decode(json_encode(simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE), true);
        }

        return $contents;
    }
}