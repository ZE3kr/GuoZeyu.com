---
title: DNS——安全性相关记录，DANE 以及更多
tags:
  - DNS
  - 安全
id: '2839'
categories:
  - - 开发
---

在 DNS 中，有一些是安全性相关的记录，比如 DS、TLSA、CAA、SSHFP、IPSEC、以及一些通过 TXT 记录来实现的记录等。安全性相关的记录类型**十分建议**包含签名，也就是说安全性相关的记录应该使用 DNSSEC。此外，当一个域名下不包含这种记录类型时，也必须返回 NSEC 记录并签名。之前一篇文章中所介绍的 DS 就是一个例子。除了 DS 外，还有这些记录类型：

## TLSA - DANE 相关

DANE 可以用于绑定某个域名下的某个服务的证书，也就是说**可以让一些原本被客户端信任的证书不被信任**，证书颁发商未经网站管理人授权签发的证书可以不被信任，可以实现和 Certificate Transparency 类似的效果。这容易与 HPKP Pin 混淆。HPKP Pin 后者只能使用于 HTTPS 服务，且只有成功且没有劫持的访问过才有效果（所以为了使 HPKP Pin 达到真正安全，必须需要建立一个受信任的中央服务器去 Preload 这些记录，类似 HSTS）；DANE 即使是在第一次访问也无法被劫持，而且可以用于 Mail 等域名相关的 SSL 服务，不只限于 HTTPS。 我认为 DANE 的真正有意思的地方是在于它可以让客户端去有选择的信任自签名的证书，也就是说**可以让一些原本不被客户端信任的证书被信任**：通过 DNS 的方式向浏览器告知这个网站自签名证书的公钥，由于包含了签名，浏览器就能够知道这是域名所有者的公钥，就能够在这个域名下信任这个自签名的证书。**这打破了目前常用的 CA 机制**，网站管理者也再也不用去向 CA 花钱或者是不花钱的申请证书，而是直接使用自签名证书甚至是自己管理的 CA 签发的证书，操作系统也不再需要选择去信任哪些根证书，也能避免传统证书签发商系统存在结构性缺陷（比如证书签发商通过自己签发证书来进行 HTTPS 中间人等）。然而实现这一步首先需要客户端的支持，已经开始有程序开始支持，然而却还没有看到浏览器去支持的迹象。 使用了自签名证书的 HTTPS 且配合了 DANE 的站点与常规 HTTPS 站点的信任链对比：

*   **DANE 自签名** （dane-self-ca.landcement.com）：内置的 DS 记录 -> 根域名（.）-> 一级域名（.com）-> 二级域名（landcement.com）-> 自签名证书（dane-self-ca.landcement.com）
*   **证书颁发商签名**（dane-trusted-ca.landcement.com）：内置的根证书（DST Root CA X3）-> 中间证书（Let's Encrypt Authority X3）-> 域名证书（dane-trusted-ca.landcement.com）

注：域名 landcement.com 只是在本地环境中进行的测试，公网无法访问

实现 DANE 的方式主要是通过 **TLSA** 记录： TLSA 记录包含了证书的哈希值，或者是某一个中间证书的哈希值（或者也可以是完整的证书）。同时，它可以针对不同的协议和端口配置不同的 TLSA 记录。我认为，TLSA 是最安全的一种 DANE 的方式。 你可以在[这个网站](https://ssl-tools.net/tlsa-generator)生成一个 TLSA 记录，我的 dane-trusted-ca.landcement.com 站点绑定了 Let's Encrypt 的中间证书，设置的 TLSA 记录是这样的：

\_443.\_tcp.dane-trusted-ca.landcement.com. 604800 IN TLSA 0 0 1 25847D668EB4F04FDD40B12B6B0740C567DA7D024308EB6C2C96FE41D9DE218D

这里记录中的第一项（这里是 0）代表着 PKIX-TA，TA 意味着这个根证书或是中间证书必须在这个域名所使用的证书链中，也就是说这个域名只能使用某一个证书颁发商颁发的证书。如果第一项填写 1，代表着 PKIX-EE，EE 意味着这个证书必须是域名所使用的证书，也就是说每次更换证书后都得修改这个记录。PKIX 意味着这个证书链是受操作系统信任的，在使用证书颁发商颁发的证书时（如 Let's Encrypt），应该使用 PKIX。 当第一项为 2 和 3 时，一切就变的有意思多了，2 代表着 DANE-TA，代表着绑定一个自签名的根证书，我的 dane-self-ca.landcement.com 站点就绑定了一个自签名的 Root： ![](https://cdn.landcement.com/sites/2/2018/04/Screenshot-2017-04-03-下午10.38.48-450x288.png) 设置的 TLSA 是这样的：

\_443.\_tcp.dane-trusted-ca.landcement.com. 604800 IN TLSA 0 0 1 25847D668EB4F04FDD40B12B6B0740C567DA7D024308EB6C2C96FE41D9DE218D

所以如果客户端支持了 DANE，那么这个自签名的根证书在这个域名下就是被信任的。 当第一项为 3 时，代表着 DANE-EE，这可以直接绑定域名证书，意味着不但可以使用自签名的证书，连证书链都免去了，我的 dane-self-signed.landcement.com 就直接使用了一个自签名的证书： ![](https://cdn.landcement.com/sites/2/2018/04/Screenshot-2017-04-03-下午10.39.28-450x290.png) 设置的 TLSA 是这样的：

\_443.\_tcp.dane-self-signed.landcement.com. 604800 IN TLSA 3 0 1 BF617DDCC4F39BD0C9228FC0C1EAD5E96252F36FB3FB5AB0CBB9B9B09C3CFE21

你可以在[这个网站](http://www.internetsociety.org/deploy360/resources/dane-test-sites/)找到更多的不同种类的支持 TLSA 的网站。 那么问题就来了，**为什么现有的浏览器都不支持 TLSA 呢？**我认为主要原因如下：

1.  **会给浏览器带来一个额外的 DNS 查询时间**，为了最高的安全性，浏览器应该在开始加载 HTTPS 页面（建立握手后）之前就先查询 TLSA 记录，这样才能够匹配证书是否该被信任，这样无疑增加了所有 HTTPS 页面的加载时长。
2.  现有的 DNS 是一个不是足够稳定的东西，何况 DNSSEC 的记录类型不被一些 DNS 递归服务器所支持等，**经常会由于因为种种原因遇到查询失败的问题**，按照规则，TLSA 记录查询失败的话客户端也应该不加载页面。这样无疑增加了 HTTPS 页面加载的出错率，这样无疑会导致很多原本没有被中间人的网站也无法加载。
3.  **DNS 污染**，在一些情况下，客户端所处的是被 DNS 污染的环境，特点就是将服务器解析到了错误的 IP 地址。然而此时中间人为了仍能够让用户访问到 HTTPS 网站，会进行类似 SNI Proxy 的操作，此时的客户端访问网站仍是比较安全的。然而显然，DANE 在 DNS 被污染后，会直接拒绝加载页面，这样**被 DNS 污染的环境会有大量站点无法加载**。
4.  **DNSSEC 本身的安全性**，现在仍有一些 DNSSEC 的 ZSK 或者是 KSK 在使用 1024-bit 的 RSA，在证书系统中，1024-bit 的 RSA 已经基本不被信任，而 RFC 却建议使用 1024-bit 直到 2022 年。而这不是主要问题，越来越多的域名服务器，尤其是根域名和一级域名，都已经使用了 2048-bit 的 RSA 甚至是 256 位的 ECDSA，客户端可以直接拒绝接受 1024-bit RSA。

然而我认为这些都不是什么问题。为了解决 DNS 的问题，可以使用 [Google DNS-over-HTTPS](https://developers.google.com/speed/public-dns/docs/dns-over-https)，这样的话就能避免很多 DNS 污染的问题，而且由于 DNSSEC 本身包含签名，Google 是无法对返回内容篡改的。那么直至现在，TLSA 就只存在最后一个问题：为了获取 TLSA 记录而增加的加载延迟。而这也可以完美解决，OCSP 就是一个例子，现在传统的 CA 为了实现吊销机制，也都有 OCSP：

> OCSP（Online Certificate Status Protocol，在线证书状态协议）是用来检验证书合法性的在线查询服务，一般由证书所属 CA 提供。为了真正的安全，客户端会在 TLS 握手阶段进一步协商时，实时查询 OCSP 接口，并在获得结果前阻塞后续流程。OCSP 查询本质是一次完整的 HTTP 请求，导致最终建立 TLS 连接时间变得更长。
> 
> *   ——[JerryQu](https://imququ.com/post/why-can-not-turn-on-ocsp-stapling.html)

这样，在开始正式开始加载页面，客户端也需要进行一次 HTTP 请求去查询 OCSP。OCSP 也会十分影响页面加载速度，也同样会增加加载页面出错的可能。而 OCSP 有了 OCSP Stapling，这样的话 Web 服务器会预先从 CA 获取好 OCSP 的内容，在与 Web 服务器进行 HTTPS 连接时，这个内容直接返回给客户端。由于 OCSP 内容包含了签名，Web 服务器是无法造假的，所以这一整个过程是安全的。同理，TLSA 记录也可以被预存储在 Web 服务器中，在 TLS 握手阶段直接发送给客户端。这就是 **DNSSEC/DANE Chain Stapling**，这个想法已经被很多人提出，然而直至现在还未被列入规范。也许浏览器未来会支持 TLSA，但至少还需要很长一段时间。

### 传统 CA 机制所特有的优势

传统的证书配合了现在的 Certificate Transparency，即使不需要 TLSA 记录，也能一定程度上保证了证书签发的可靠性。此外，传统证书也可以使用 TLSA，其本身的安全性不比 DANE 自签名差。 此外，传统 CA 签发的证书还有自签名证书做不到的地方：

*   **OV 以及 EV 证书**：DANE 自签名的证书其实就相当于 CA 签发的 DV 证书，只要是域名所有者，就能够拥有这种证书。然而很多 CA 同时还签发 OV 和 EV 证书，OV 证书可以在证书内看到证书颁发给的组织名称，EV 则是更突出的显示在浏览器上：在 Chrome 的地址栏左侧和 HTTPS 绿锁的右侧显示组织名称；Safari 则甚至直接不显示域名而直接显示组织名称。OV 和 EV 的特点就是，你甚至都不用去思考这个域名到底是不是某个组织的，而直接看证书就能保证域名的所有者。而 DV 证书可能需要通过可靠的途径验证一下这个域名到底是不是某个组织的。比如在你找一家企业官网的时候，有可能会碰到冒牌域名的网站，而你不能通过网站是否使用了 HTTPS 而判断网站是否是冒牌的——因为冒牌的网站也可以拿到 DV 证书。而当你发现这个企业使用了 OV 或 EV 证书后，你就不用再去考虑域名是否正确，因为冒牌的网站拿不到 OV 或 EV 证书。在申请 OV 或 EV 证书时，需要向 CA 提交来自组织的申请来验证组织名称，而由于 DANE 证书是自签发的，自然也不能有 OV 或 EV 的效果。
*   **IP 证书**：CA 可以给 IP 颁发证书（尽管这很罕见），这样就可以直接访问 HTTPS 的 IP。而 DANE 基于域名系统，所以不能实现这一点

## CAA - 证书颁发相关

CAA 比较简单，它相当于是公开声明这个域名允许了哪些证书颁发商颁发的证书。CAA 不需要 DNSSEC，写在这里只是和 TLSA 区分一下，但开启 DNSSEC 无疑能够使 CAA 更可信。 比如在域名添加了如下记录，就相当于限制了仅允许 Let's Encrypt 签发证书，**其他证书签发商则不再给这个域名签发证书**。在 CAA 记录刚推出时受到以下因素限制：

*   只有在使用支持 CAA 的证书颁发商，添加 CAA 才有意义
*   证书颁发商并没有必要 CAA，也就是说即是添加了 CAA 记录后仍可能可以被其他你没有允许的证书颁发商颁发。所以这是需要其他证书颁发商**自觉地**不再给这个域名办法证书。

但是从 2017 年底开始，所有证书颁发商被强制要求遵守域名的 CAA 记录。[来源](https://cabforum.org/pipermail/public/2017-March/009988.html) CAA 的副作用最小，你也可以添加多个 CAA 记录来允许多个证书颁发商去签发，以增加灵活性。 google.com 就使用了 CAA（但是没有开启 DNSSEC）：

$ dig google.com caa
;; ANSWER SECTION:
google.com.86399INCAA0 issue "symantec.com"

如果要将一个域名绑定在某个证书颁发商下，建议同时使用 TLSA 和 CAA。如果是长期的绑定，可以考虑一下 HPKP Pin。

### 细节

## SSHFP - 认证主机相关

在使用 SSH 首次连接一个主机时，SSH 通常会询问是否信任服务器的指纹（fingerprint）：

$ ssh guozeyu.com
The authenticity of host 'guozeyu.com (104.199.138.99)' can't be established.
ECDSA key fingerprint is SHA256:TDDVGvTLUIYr6NTZQbXITcr7mjI5eGCpqWFpXjJLUkA.
Are you sure you want to continue connecting (yes/no)?

这个指纹告诉你了你连接到的是这个服务器，而不是别的服务器。你需要确认这个指纹与自己所要连接到的服务器匹配时，才应该进行连接，不然你就会连接到别的服务器（攻击者可以让服务器允许任何来源的 SSH，于是你就成功登陆到了攻击者的服务器。这样你所执行的代码，甚至是通过 SSH 提交的 Git，都能被获取到）。 那些允许公开的 SSH 连接的服务器，如 [GitHub](https://help.github.com/articles/github-s-ssh-key-fingerprints/)，会在网站上公开自己的指纹，用户需到在它们的官方文档中找到指纹。而 SSHFP 则是一种更简单且通用的的公开这个指纹的方式，这个功能甚至都集成到了 SSH 中去，当检测到指纹匹配时就会自动登陆，跳过询问指纹的步骤。 然而，假如攻击者同时控制了网络中的 DNS 和 SSH，那么 SSHFP 反而是更加不安全的。所以客户端仅应该信任使用了 DNSSEC 的 SSHFP 记录。 编辑 `~/.ssh/config` ，添加这一行即可实现验证 SSHFP 记录：

VerifyHostKeyDNS=ask

如果将 `ask` 换为 `yes`，那么会默认信任 SSHFP 记录。

## IPSECKEY - IP 安全性相关

## 参考资料

*   [RFC 6698 - The DNS-Based Authentication of Named Entities (DANE) Transport Layer Security (TLS) Protocol: TLSA](https://tools.ietf.org/html/rfc6698)
*   [RFC 6844 - DNS Certification Authority Authorization (CAA) Resource Record](https://tools.ietf.org/html/rfc6844)