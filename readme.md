## 功能

在不改动应用源码的前提下，使用一个认证服务号为多个微信应用提供微信接口转发。

## 方法

~~修改hosts，使以下域名指向本程序~~
```text
open.weixin.qq.com
api.weixin.qq.com
```

太天真了，以上方法需要禁用ssl证书验证。

无奈之举：

/vendor/overtrue/socialite/src/Providers/WeChatProvider.php

```php
protected $baseUrl = 'https://your-domain.com/sns';

protected function getAuthUrl($state)
{
    $path = 'oauth2/authorize';

    if (in_array('snsapi_login', $this->scopes)) {
        $path = 'qrconnect';
    }

    return $this->buildAuthUrlFromBase("https://your-domain/connect/{$path}", $state);
}

```

## 路线

- [x] OAuth

- [ ] JSSDK

## 其它

- [Bean 大佬开发的另一种实现方法](https://github.com/HADB/GetWeixinCode)

## License

[MIT license](https://opensource.org/licenses/MIT)
