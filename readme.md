# mr-jiawen/pay
> 一个支付包，主要包括的有  阿里支付 、微信支付 等等
> 此包借鉴 https://github.com/yansongda/pay 进行开发。

此包主要完成的是 saas 平台 进行授权支付， 使用一个开发 账号，让所有需要支付的账号全部授权于这个账号（第三方应用开发）。

并且还需要支持线上正常生产， 线下正常开发， 让两者进行解耦合，互不影响

# 第一部分 ： 支付宝支付
### 1.1. 初始化支付宝实例
首选需要创建一个 Config 对象，他具备 verification 功能， 自动检测配置参数，如果出现配置异常，给出报错信息并且提示：
```
$config = new Config([
        'app_id' => '2016082000290192',
        // 支付宝异步通知地址
        'return_url' => 'http://pay.cjwme.com/return_to',

        // 支付成功后同步通知地址
        'notify_url' => 'http://pay.cjwme.com/notify',
        "ali_public_key" => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAt1iB6EnCHaiq34wXcMZJxxA32j4pXrcpPnsWdDcr4taLKUOhs+xZu+9mUJLTxNKUwcGL8adC0ZsAJSh55lhVtRCROXQFPFBLi/EWkW6QNzeSVsj7P/utivE8tPWH9m/XRsRwRCnJA5Be0Rk6woQ/oIqlKSYwAK+rOKOoNGCEzj0qmyI4zjs5iqDFOhS2pLbYC4+yntQJHLR/tOGGh8ONzonZgDHa8KtI0OckXErXnfKzQB2/Lc5+aTlZNejHqJaqHwbZRZdpN4MhEwC4pkhM1fAl69/gWnteKsZKhP7JXLkpkyr/5zkbZVYpvRq3xCBZLozLQsagYdRZIdPOXxMcHwIDAQAB",

        "seller_private_key" => 'MIIEpAIBAAKCAQEAsA9Hfem1z0aNT9KCpn71+bMqY1MoHc3diQAlmdA31QB9L+6oGyOyRmglN8A0UJKjhV5GCnA9IZQCUtRbO9bmroFM47SpFZEt3+3WLK7MhuDfYSdOVn+LpPZE1hRPclKP5Bhaf0UdSyz9ZNZNhU4fen5WH90ORPMMJfpDT6uUmkYIcTcy4Dr+9jJZ/l7OUlSnoL4kHqMhu84ZTZ+w2zQmIyqNh9JtcBY8TV5B5QmjxXMdv19A81jLgu9Z50RlrhATA6JM/ZWu73LmHTR1HitBfg2WnDUX7X53J8a9FETBrxrJPRERsoKJbtlhMq1ExF81YoqKJl+0IIPW+kPILBm1nQIDAQABAoIBAFQZ2GLTY1/yKcq5mmOCPmnbJiJwNLeYAX1SLqP9DM6Y/zQIYxEjyiNL2It7KwJaKfapg/e0id8iXsHGYxaAn404gsw7HpII2csgR7DshxLfFJXKOuei4cgZQ+SnqxPHUKj64S/uigHBKgIjRMCrAup2sxlhdVGGcIUruT+zjLbsdgxpQ8MPeRrN82fJaAw+UUs0KSdacnHReV25gUKzQ/4U5tYEibrk8qdnQGelynTzP5OFkINrYOwvOLzb1Kd/gCTSMh6p9VNSLlRKTgd68ajKvAneKbG3LkXPeER6AQtlh3ikq611JOfWl2GH6ZoeffcUWS59D1x8U+BVn6UAGRkCgYEA4zTLN2O8myBS0WA4TKWzrCnXQzyHJwl6QYGg/WJ/3gk9+xQHF+pdmtZq4zZWUqqKlizSkfNhgm6UEmvdcKUSpT2Lhys8ULUZBhaSrsun0/jZcWRQxGTVWfEPy3gNbrqzTym2tNwpCn1p+/LvzlJIr5QFbE/7zKGeD84Qg82dVzcCgYEAxl8l/80g6+uqVIONEXOIGB8uBLhoeMoCgFrYlimG4Bc8EJJ7FWqmDjRBysvC2lj+Porer4xB5I0BAphtVug2wUMJOS4+10IwYtfeYfBglT9/LIYhj9WmvibzaA0knkGxGTzxGemwwvRI87a1u+dd4KTpPONft/0w6JRy/DDTW8sCgYEAl5Lb03rlEzQwK+AeDvwobj2HjyJtI5zOA6+AACpbFXxi7gKBbPH8OlS7ABN66TE+vUrvu1B7h7FPdS6ijwiA9N2nHKi6VzlJlWcvug7BSsdkAKKkwzBlqNgJ6nqPs4Q4JSK18dhCRO4/60Y5txHrzb5ZE9YULILRWMfm+pcMVWcCgYEAvrhCD3MHoOs+Iu0rlsit4wsvMkid4OvPhdpR++VHEHImRISgo9Gaf3OMgc5vs3/2SOk4+ixuOa+7deRY00KFSkAKQRiOQGUZoh6LZzw1j6ixu3vmDqTKIG3QfYyNVT4xZ+NoppJAaN92uCJFDbh/UHkc6XZt07Yw7Ju1BXRn8QsCgYAcj+b+b15k6OniBO/wHptnz4eoJP+XXLEeoTPEntYMOCrOJ7IHSROO1RLdTg7d7Pebg5CDrBtwayAduB/6m0HDPHZqmw1vzBPI9MxI6+1/FU006uPXR/M+7598+BMHKjAgq9+JCEE3bgPZeTPCDv3Rllugjt7Ai2/SO+tkFqhgOQ==',
        "mode" => "dev",
    ]);
```
然后创建实例：
```
$alipay = new Alipay($config,new AliPayOfWeb());
```
1. 第一个参数是必填参数，用来设置最基本的信息
2. 第二个参数是一个选填参数，他表示使用哪种支付：
    * AliPayOfWeb；
    * AliPayOfWap;
    * 等等，只要是继承了 AliPayType 接口都可以 

### 1.2. web 支付
```php
$alipay = new Alipay($this->config, new AliPayOfWap());

return $alipay->pay([
    "out_trade_no" => time(),
    'total_amount' => 1,
    'subject' => 'this is test '
]);
``` 
### 1.3. 异步验证
```php
public function notify()
{
    $alipay = new Alipay($this->config);
    
    try {
        $data = $alipay->verify(); // 是的，验签就这么简单！

        // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
        // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
        // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
        // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
        // 4、验证app_id是否为该商户本身。
        // 5、其它业务逻辑情况

        \Log::debug('Alipay notify', $data->all());
    } catch (Exception $e) {
        // $e->getMessage();
    }

    return $alipay->success();// laravel 框架中请直接 `return $alipay->success()`
}
```
### 1.4. 同步验证
```php
public function returnTo()
{
    $alipay = new Alipay($this->config); // 是的，验签就这么简单！
    $data = $alipay->verify();

    // 订单号：$data->out_trade_no
    // 支付宝交易号：$data->trade_no
    // 订单总金额：$data->total_amount
}
```
### 1.5. 订单的查询
```php
public function query()
{
    $alipay = new Alipay($this->config);
    $result = $alipay->query("1534132785");
 
    // ...
}
```
## 第二部分 支付宝授权
### 2.1 获取app_auth_code
```php
public function get_auth_code()
{
    $alipay = new Alipay($this->config); // 是的，验签就这么简单！
    // 设置好配置参数 ，当用户完成授权交互操作后，自动跳转到回调函数里，并且携带 app_auth_code
    return $alipay->getAuthCode();  # 这里是重定向 去授权
}
```
### 2.2 获取 app_access_token
```php
public function notify_auth(Request $request)
{
    $alipay = new Alipay($this->config); // 是的，验签就这么简单！
    $response = $alipay->getAccessToken($request['app_auth_code']);

    //...里面包含 app_access_token
}
```