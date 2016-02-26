---
ID: 13
post_title: 全面 HTTPS 时代即将到来
author: ZE3kr
post_date: 2015-12-13 09:19:00
post_excerpt: ""
layout: post
permalink: >
  https://ze3kr.com/2015/12/https-everywhere-is-coming/
published: true
dsq_thread_id:
  - "4543289199"
---
HTTPS 是一种网络安全传输协议，网址以 `https://` 开头，就代表是使用了这个协议。

苹果最新发布的移动端操作系统 iOS 9，除了带来了许多新的功能之外，还提升了整个系统安全性，正如[iOS 开发者资源](https://developer.apple.com/library/prerelease/ios/releasenotes/General/WhatsNewIniOS/Articles/iOS9.html)所说

> 如果你正在开发一个新的程序，你仅应该使用 HTTPS。如果你已经有一个程序，你现在就应该尽可能多的使用 HTTPS，并准备好对剩下部分迁移的计划。另外，如果你的程序使用更高层级的 API 进行通信，则需要使用 TLS 1.2 或以上的版本。如果你试图建立一个不遵守这些需求的通信，就会引发错误。（If you’re developing a new app, you should use HTTPS exclusively. If you have an existing app, you should use HTTPS as much as you can right now, and create a plan for migrating the rest of your app as soon as possible. In addition, your communication through higher-level APIs needs to be encrypted using TLS version 1.2 with forward secrecy. If you try to make a connection that doesn't follow this requirement, an error is thrown.）

没错，从 iOS 9 开始，将逐步禁用非 HTTPS 请求！

即使现<!--more-->在已有的程序在 iOS 9 中仍可以在非 HTTPS 情况下工作。但是相信在不久的将来，所有程序都会使用 HTTPS，而且 HTTP 将会完全淘汰。

那么为什么要使用 HTTPS？那些情况下要使用HTTPS呢？

## 使用 HTTPS 原因

HTTPS 能够加密数据传输，防止中间人截取或是修改。能够实现加密用户信息和网站内容。

比如使用大众所说的 “不安全的免费 Wi-Fi”，如果用户访问的网页全部是 HTTPS 的，那么这个 Wi-Fi 对用户没有任何影响。也就是说，媒体报道的 “免费 Wi-Fi 不安全” 纯属造谣，没有任何道理。当启用了 HTTPS 和 HSTS 后，免费 Wi-Fi 完全不能截获到用户密码等任何信息，用户可以安心的进行付款等操作。显然央视 315 没有任何专业知识及解释就在骗大家 “免费 Wi-Fi 不安全”，完全就是恐吓观众。之所以微信朋友圈所有照片都能被获取，是因为<strong>微信朋友圈的上传是明文的</strong>，这分明是微信自己的问题，显然并不是所有的软件都存在这样的问题。随着 iOS 9 的发布以及强制 HTTPS 措施，这一类问题将不复存在了。

其次，使用 HTTPS 不仅仅是为了防止信息被盗窃，还可以防止信息被中途修改。比如中国联通和中国移动会修改网站内容，投放自己的广告让用户升级产品，而这些广告并不是网站主准备的，网站主事先也不知道。虽然它们这样做就是没有行业道德底线，但是我们仅需要使用 HTTPS，这些运营商就统统无能为力了。

包括小米路由器的 “404错误页面优化” 也是利用了同样的原理，对非 HTTPS 页面进行篡改，给用户提供自己的广告从而谋取利益。其本身就是<strong>劫持</strong>，<strong>绝没有夸大之言</strong>。除此之外，有的用户还发现就算是正常页面，也有小米通过<strong>劫持</strong>网页<strong>代码注入</strong>而加入的广告信息。但当 HTTPS 普及之后，这一切都会无影无踪。然而在 HTTPS 普及之前，一些不支持 HTTPS 的网站主只能忍受被运营商、路由器的劫持了。

## 使用 HTTPS 的地方

我认为，<strong>所有的网页以及程序</strong>都有必要<strong>全部且强制的使用 HTTPS</strong>，可以避免上述情况的发生。包括个人网站在内，也应该全面启用 HTTPS，防止因为被篡改植入的广告而流失读者。

使用 HTTPS 并不会增加太多的成本，还可以让页面速度变得更快。SPDY 协议可以最小化网络延迟，提升网络速度，优化用户的网络使用体验，然而 SPDY 协议只支持 HTTPS。

随着现在的趋势，越来越多的站长会主动或被迫的使用 HTTPS，HTTPS 即将成为主流。中国是 HTTPS 普及程度最小的国家，但是随着百度全站 HTTPS 以及 UPYUN 支持自定义域名的 HTTPS，将推动整个行业 HTTPS 的发展。

## 使用 HTTPS 的优点

### 加密传输

如果一个网页没有使用 HTTPS，那么就意味着页面上的内容，你正在搜索的关键词，甚至是用户名和密码都没有加密，“中间人”可以进行读取、篡改这些没有加密的内容。比如说你连接的免费 Wi-Fi、运营商等都可能会对网页内容进行修改。这些中间人会在你未经允许的情况下在网页上添加他们的广告、修改 404 页面样式、读取你微信朋友圈的图片。

但当这个网页启用了 HTTPS 之后，页面的数据就会被加密，正常情况下中间人就不能获取到你的数据，但是如果中间人替换掉了原有的 HTTPS 后，攻击依然是可能的，所以 HTTPS 站点必须包含一个证书用于验证。

### 验证

由于 HTTPS 站点必须包含一个证书用于验证，那么就可以验证这个服务器是否被域名所有者许可（以防止连接到中间人的服务器）。通常浏览器都会检验这个证书，如果证书错误会警告用户正在访问的网站有问题。假如用户的 DNS 服务被污染，那么就可能伪造身份，到另一个主机（IP 地址），这个甚至比中间人攻击更可怕，能做到更多的事。当用户使用 HTTPS 访问时，浏览器会验证这个网站的证书，如果证书报错则会警告，甚至无法访问。

但是假如用户访问的时候使用的是 HTTP，那么就不会对服务器进行验证。只有网站进行强制 HTTPS （也就是禁用 HTTP 链接）并启用了 HSTS，那么才能够进行全面的认证。一旦启用 HSTS，那么这个域名的所有页面在设定的有效期内就只能通过 HTTPS 访问。

### 加密提示

对于站点下所有资源都使用 HTTPS 协议的页面，很多浏览器都会有加密提示，以告知用户这个站点是加密的，让整个网站更高大上。不过我想在此提醒用户，并不是所有使用 HTTPS 的页面就是安全的，任何网站都能轻易申请到 SSL 证书，所以仍然需要辨别域名本身。但是对于直接显示其公司名称的 HTTPS 站点就更值得被信任，因为这种证书是需要纸质证明材料的验证的。下方为使用 Mac 版 Chrome 访问一些 HTTPS 站点的加密提示，Chrome 的加密提示菜单中分为两部分，前一部分验证，后一部分是加密，通常可以分为以下 4 种：

[img id="1011" size="medium"]1. 显示公司名称的 HTTPS 站点[/img]

[img id="1013" size="medium"]2. 普通 HTTPS 站点[/img]

其中第一种和第二种情况代表使用了足够安全的加密方式（但是第二种没有提供任何 Certificate Transparency 信息），只是证书的签名等级不同，与加密方式以及验证的安全性无关，这两种情况下都能保证证书不是伪造的。

[img id="1015" size="medium"]3. 包含不安全资源的 HTTPS 站点[/img]

第三种情况是包含不安全资源，网站的外观可能会被改变，但 HTML 文本本身是可靠的。

[img id="1017" size="medium"]4. 使用过时验证方式的 HTTPS 站点[/img]

第四种情况是使用了 SHA-1 签名的证书，由于 SHA-1 不是足够的安全，也就是说验证的安全性不够，由于这种证书伪造的成本越来越低，所以可能不安全。这种站点的加密仍然是足够的。

[img id="1019" size="medium"]5. 加密协议有问题的 HTTPS 站点[/img]

第五种情况代表当前可能正在被中间人攻击（因为没有提供任何 Certificate Transparency 信息，而且还使用了 SHA-1）。

[img id="1020" size="medium"]6. 使用不被信任的根证书签发的证书的 HTTPS 站点[/img]

第六种情况表示这个网站使用不被信任的根证书签发的证书（或者证书中不包含当前域名）。

[关于 Chrome 的加密提示](https://support.google.com/chrome/answer/95617)

无论如何，我都不推荐你使用 SHA-1 签名的证书，值得注意的是最新版 Safari 也可以选择不信任 SHA-1 签名的证书了，SHA-1 即将淘汰。

[img id="1023" size="medium"]Safari 的新选项[/img]

### 搜索引擎

Google 对 HTTPS 站点的抓取很友好，官方说使用 HTTPS 还会提高排名。Google 也支持使用 SNI 协议的 HTTPS 站点。

百度近期支持了对 HTTPS 站点的抓取，并会相应提高排名，经测试并不是所有证书都支持，但是也支持 SNI 协议。

## 使用 HTTPS 的缺点

### 更慢的加载速度

不得不说使用 HTTPS 的确会增大延迟，这种延迟主要体现在加载首个页面，进入下一个页面就会快很多了。在我这里测试启用 HTTPS 后会多出 2~5 倍的延迟。如果还有资源在别的域，那么这些资源也会多出这么多的延迟。

### 兼容性问题

SNI 协议已经被大量的使用，但是仍然有一些设备是不兼容的，比如 Windows XP 的 IE8 及以前的浏览器都不兼容。截止到目前，[a href="http://caniuse.com/#feat=sni"]中国支持 SNI 的浏览器使用率已经达到了 90%[/a]。

### 搜索引擎

有些搜索引擎对 HTTPS 很好，但是也有一些搜索引擎不支持它。不过目前越来越多的搜索引擎都在逐步支持 HTTPS，我觉得不必担心。

### 成本

使用 HTTPS 必须需要得到一个证书。现在已经有越来越多的免费 SSL 证书了，总体来说实现 HTTPS 的成本越来越低了。但是随着证书颁发的成本越来越低，HTTPS 作为验证的功能将会变的更小，但是作为加密的功能还是存在的。

## 其它

HTTPS 只能在一定程度上防止篡改，但是并不能抵制一些强大的[a href="https://zh.wikipedia.org/wiki/%E9%98%B2%E7%81%AB%E9%95%BF%E5%9F%8E"]网络封锁[/a]。

目前一些“流氓”浏览器竟然不验证证书，大大减弱安全性。如果不验证证书，虽然还有加密的效果，但是如果 DNS 被污染，中间人攻击依然可行。

如果 DNS 被污染，但仍需要使用 HTTPS，那么就将无法访问站点。所以 DNS 也是十分需要加密的。如果能早日支持 DNSSEC 或者是 “HTTPS DNS”（即使用 HTTPS 协议解析 DNS），那么 HTTPS 作为验证则显得不那么重要，完全没有中间人攻击的日子指日可待（但是没有网络封锁还是不可能的）。

或许以后的浏览器只能访问 HTTPS 站点，就像 iOS 9 大力推动 HTTPS 一样，这样就能大大提高安全性，抵制中间人攻击。

### 使用 .htaccess 强制 HTTPS

下方为在 Apache 里的 `.htaccess` 中配置

<pre class="lang:apache decode:true">RewriteEngine on

RewriteCond %{HTTP:X-Forwarded-Proto} =http
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301] # 禁用 HTTP 协议</pre>

### HSTS 以及 HSTS Preload List

HSTS（HTTP Strict Transport Security, HTTP 严格传输安全）是一种让浏览器强制 HTTPS 的方法，当用户访问 HTTPS 站点时，由服务器返回一个 Header，告知浏览器在这个域名下必须强制 HTTPS，在有效期内，浏览器访问此域名将只使用 HTTPS，下方为在 Apache 里的 `.htaccess` 中配置。

<pre class="lang:apache decode:true">Header set Strict-Transport-Security "max-age=315360000; preload; includeSubDomains" env=HTTPS</pre>

比如首次访问 `http://tlo.xyz` 时，浏览器会被 301 跳转到 `https://tlo.xyz` 下，然后就会收到这个 Header，在 10 年内，tlo.xyz 下所有的域名都只会使用 HTTPS，包括二级域名 `ze3kr.tlo.xyz`。

但这一切还没有结束，假如浏览器第一次访问时，网站就已经被 HTTPS 劫持攻击了，那么这样做是毫无意义的，所以需要在启动 HSTS 后，包含 `preload` 参数，然后去[a href="https://hstspreload.appspot.com"]提交[/a]，注意好要求。

当你提交了之后，一段时间后就能在各大浏览器的源码里看到你的域名了。

+ [a href="https://code.google.com/p/chromium/codesearch#chromium/src/net/http/transport_security_state_static.json"]Chromium 源码[/a]
+ [a href="https://mxr.mozilla.org/mozilla-aurora/source/security/manager/ssl/nsSTSPreloadList.inc"]FireFox 源码[/a]

### 检查你的 SSL 配置

前往 [a href="https://www.ssllabs.com/ssltest/index.html"]SSL Server Test[/a]，就能给你的服务器的 SSL 配置给出一个评分。

哎，这差距

[img id="1090" size="medium"][/a]

[img id="1091" size="medium"][/a]

## 小提示

+ 一旦设置了强制 HTTPS 协议或者启用了 HSTS 后很难再退回 HTTP 协议（尤其是后者），如果突然关闭了 HTTPS 协议，用户可能在很长一段时间内无法访问网站，或者证书报错，大量流失用户。所以决定强制 HTTPS 前请仔细做好考虑。
