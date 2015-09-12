---
layout: post
title:  "关于 HTTPS 的一些事"
date:   2015-08-24 22:37:00+08:00
image:
  title: ze3kr/2015/ZE3_1224.jpg

tag: 
- HTTPS
- 部署 HTTPS
- Apache

category: article
---
[我的主页](/)就全部强制 HTTPS。使用 HTTPS 有好处也有坏处，本文将说一些关于此的事。

{% include list.html %}

## 优点

### 加密传输

如果一个网页没有使用 HTTPS，那么就意味着页面上的内容，你正在搜索的关键词，甚至是用户名和密码都没有加密，“中间人”可以进行读取、篡改这些没有加密的内容。比如说你连接的免费 Wi-Fi、运营商等都可能会对网页内容进行修改。这些中间人会在你未经允许的情况下在网页上添加他们的广告、修改 404 页面样式、读取你微信朋友圈的图片。

但当这个网页启用了 HTTPS 之后，页面的数据就会被加密，正常情况下中间人就不能获取到你的数据，但是如果中间人替换掉了原有的 HTTPS 后，攻击依然是可能的，所以 HTTPS 站点必须包含一个证书用于验证。

### 验证

由于 HTTPS 站点必须包含一个证书用于验证，那么就可以验证这个服务器是否被域名所有者许可（以防止连接到中间人的服务器）。通常浏览器都会检验这个证书，如果证书错误会警告用户正在访问的网站有问题。假如用户的 DNS 服务被污染，那么就可能伪造身份，到另一个主机（IP 地址），这个甚至比中间人攻击更可怕，能做到更多的事。当用户使用 HTTPS 访问时，浏览器会验证这个网站的证书，如果证书报错则会警告，甚至无法访问。

但是假如用户访问的时候使用的是 HTTP，那么就不会对服务器进行验证。只有网站进行强制 HTTPS （也就是禁用 HTTP 链接）并启用了 HSTS，那么才能够进行全面的认证。一旦启用 HSTS，那么这个域名的所有页面在设定的有效期内就只能通过 HTTPS 访问。

### 加密提示

对于站点下所有资源都使用 HTTPS 协议的页面，很多浏览器都会有加密提示，以告知用户这个站点是加密的，让整个网站更高大上。不过我想在此提醒用户，并不是所有使用 HTTPS 的页面就是安全的，任何网站都能轻易申请到 SSL 证书，所以仍然需要辨别域名本身。但是对于直接显示其公司名称的 HTTPS 站点就更值得被信任，因为这种证书是需要纸质证明材料的验证的。下方为使用 Mac 版 Chrome 访问一些 HTTPS 站点的加密提示，Chrome 的加密提示菜单中分为两部分，前一部分验证，后一部分是加密，通常可以分为以下 4 种：

{% include img-small.html title="1. 显示公司名称的 HTTPS 站点" img="https0.png" %}

{% include img-small.html title="2. 普通 HTTPS 站点" img="https1.png" %}

其中第一种和第二种情况代表使用了足够安全的加密方式，只是证书的签名等级不同，与加密方式以及验证的安全性无关。

{% include img-small.html title="3. 使用过时加密方式的 HTTPS 站点" img="https2.png" %}

第三种情况是使用了 SHA-1 签名的证书，由于 SHA-1 不是足够的安全，也就是说验证的安全性不够，由于这种证书伪造的成本越来越低，所以可能不安全。这种站点的加密仍然是足够的。

{% include img-small.html title="4. 加密协议有问题的 HTTPS 站点" img="https3.png" %}

第四种情况代表当前可能正在被中间人攻击。

关于 Chrome 的加密提示，{% include more.html url="https://support.google.com/chrome/answer/95617" external="true" %}

无论如何，我都不推荐你使用 SHA-1 签名的证书，值得注意的是最新版 Safari 也可以选择不信任 SHA-1 签名的证书了，SHA-1 即将淘汰。

{% include img-small.html title= "Safari 的新选项" img="2015-08-2523.15.42.png" %}

### 搜索引擎

Google 对 HTTPS 站点的抓取很友好，官方说使用 HTTPS 还会提高排名。Google 也支持使用 SNI 协议的 HTTPS 站点。

百度近期支持了对 HTTPS 站点的抓取，经测试并不是所有证书都支持，但是也支持 SNI 协议。

## 缺点

### 更慢的加载速度

不得不说使用 HTTPS  的确会增大延迟，这种延迟主要体现在加载首个页面，进入下一个页面就会快很多了。在我这里测试启用 HTTPS 后会多出 25 倍的延迟。如果还有资源在别的域，那么这些资源也会多出这么多的延迟。

{% include img-small.html title= "使用 HTTPS 后的时间线" img="2015-08-24-17.12.34.png" %}

### 兼容性问题

SNI 协议已经被大量的使用，但是仍然有一些设备是不兼容的，比如 Windows XP 的 IE8 及以前的浏览器都不兼容。截止到目前，中国支持 SNI 的浏览器使用率已经达到了 90%，{% include more.html url="http://caniuse.com/#feat=sni" external="true" %}

### 搜索引擎

有些搜索引擎对 HTTPS 很好，但是也有一些搜索引擎不支持它。不过目前越来越多的搜索引擎都在逐步支持 HTTPS，我觉得不必担心。

### 成本

使用 HTTPS 必须需要得到一个证书。现在已经有越来越多的免费 SSL 证书了，总体来说实现 HTTPS 的成本越来越低了。但是随着证书颁发的成本越来越低，HTTPS 作为验证的功能将会变的更小，但是作为加密的功能还是存在的。

## 其它

HTTPS 只能在一定程度上防止篡改，但是并不能抵制一些强大的[网络封锁](https://zh.wikipedia.org/wiki/%E9%98%B2%E7%81%AB%E9%95%BF%E5%9F%8E)。

目前一些“流氓”浏览器竟然不验证证书，大大减弱安全性。如果不验证证书，虽然还有加密的效果，但是如果 DNS 被污染，中间人攻击依然可行。

如果 DNS 被污染，但仍需要使用 HTTPS，那么就将无法访问站点。所以 DNS 也是十分需要加密的。如果能早日支持 DNSSEC 或者是 “HTTPS DNS”（即使用 HTTPS 协议解析 DNS），那么 HTTPS 作为验证则显得不那么重要，完全没有中间人攻击的日子指日可待（但是没有网络封锁还是不可能的）。

或许以后的浏览器只能访问 HTTPS 站点，就像 iOS 9 大力推动 HTTPS 一样，这样就能大大提高安全性，抵制中间人攻击。

### 使用 _**.htaccess**_ 强制 HTTPS

下方为在 Apache 里的 `.htaccess` 中配置

{% highlight apache %}
RewriteEngine on

RewriteCond %{HTTP:X-Forwarded-Proto} =http
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301] # 禁用 HTTP 协议
{% endhighlight %}

### HSTS 以及 HSTS Preload List

HSTS（HTTP Strict Transport Security, HTTP 严格传输安全）是一种让浏览器强制 HTTPS 的方法，当用户访问 HTTPS 站点时，由服务器返回一个 Header，告知浏览器在这个域名下必须强制 HTTPS，在有效期内，浏览器访问此域名将只使用 HTTPS，下方为在 Apache 里的 `.htaccess` 中配置。

{% highlight apache %}
Header set Strict-Transport-Security "max-age=315360000; preload; includeSubDomains" env=HTTPS
{% endhighlight %}

比如首次访问 `http://tlo.xyz` 时，浏览器会被 301 跳转到 `https://tlo.xyz` 下，然后就会收到这个 Header，在 10 年内，tlo.xyz 下所有的域名都只会使用 HTTPS，包括二级域名 `ze3kr.tlo.xyz`。

但这一切还没有结束，假如浏览器第一次访问时，网站就已经被 HTTPS 劫持攻击了，那么这样做是毫无意义的，所以需要在启动 HSTS 后，包含 `preload` 参数，然后去提交，注意好要求 {% include more.html url="https://hstspreload.appspot.com" external="true" %}

当你提交了之后，一段时间后就能在各大浏览器的源码里看到你的域名了。

+ Chromium 源码 {% include more.html url="https://code.google.com/p/chromium/codesearch#chromium/src/net/http/transport_security_state_static.json" external="true" %}
+ FireFox 源码 {% include more.html url="https://mxr.mozilla.org/mozilla-aurora/source/security/manager/ssl/nsSTSPreloadList.inc" external="true" %}

现在，我的 `tlo.xyz` 和 `tlo.link` 两个域名已经可以在 Chromium 的源码里可以看到了哦，{% include more.html url="https://code.google.com/p/chromium/codesearch#search/&q=tlo.xyz&sq=package:chromium&type=cs" external="true" %}，同时也在 FireFox 的源码中 {% include more.html url="http://mxr.mozilla.org/mozilla-aurora/search?string=tlo.xyz" external="true" %}。

{% include img-small.html title="截个图" img="2015-09-05-10.47.42.png" %}

## 小提示

+ 一旦设置了强制 HTTPS 协议或者启用了 HSTS 后很难再退回 HTTP 协议（尤其是后者），如果突然关闭了 HTTPS 协议，用户可能在很长一段时间内无法访问网站，或者证书报错，大量流失用户。所以决定强制 HTTPS 前请仔细做好考虑。
