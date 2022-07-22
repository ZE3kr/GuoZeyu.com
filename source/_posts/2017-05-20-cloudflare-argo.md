---
title: Cloudflare Argo 与 Railgun 对比测试，CDN 加速的黑科技
tags:
  - CDN
  - 网络
id: '3108'
categories:
  - 科技
date: 2017-05-20 19:52:23
languages:
  en-US: https://ze3kr.com/2017/05/cloudflare-argo/
---

本网站曾经一直将国外解析到 CloudFront 实现为国外加速，最近看到 Cloudflare 支持了 [Argo](https://blog.cloudflare.com/argo/) 这一新功能，于是就把国外的 CDN 从 CloudFront 换到了 Cloudflare 并开启了 Argo 来试一下效果，官方宣称无缓存时能明显降低 TTFB（首字节延迟），有缓存时也能提高缓存命中率。本文还会将其与 Cloudflare 的另一个企业级的 CDN 加速黑科技——Railgun 进行对比。
<!-- more -->

# Cloudflare Argo

## 提升缓存命中率，Argo Tiered Cache

Cloudflare 的节点很多，但是节点太多有时不是一件好事——**大多数 CDN 之间的节点是相对独立的**。首先要先明白 CDN 的工作原理，CDN 通常不会预先缓存内容，而是在访客访问时充当代理的同时对可缓存的内容缓存。就拿本站来说，本站用的是[香港虚拟主机](https://domain.tloxygen.com/web-hosting/index.php)，如果有英国伦敦的访客访问了我的网站，那么由于我的网站是可被缓存的，他就会连接到伦敦的节点并被缓存在这个节点。那么如果是英国曼彻斯特的访客访问了呢？由于 CDN 在曼彻斯特另有节点，访客会直接连接到曼彻斯特节点，然而曼彻斯特上并没有缓存，所以该节点会回源到香港。而显然的是，如果曼彻斯特回源到伦敦，使用伦敦的缓存会更快。 综上，如果能选择性的从其他节点上获取资源，TTFB 会更低，缓存命中率也会相应提高。但是一般的 CDN 不会去这样做，因为节点相互独立，节点之间并不知道对方是否已经缓存。一般的解决方法是节点与源站之间先经过为数不多的几个节点，这几个节点可能只是分布在几个州，比如整个欧洲就只有一个这种节点。这样的话，伦敦的访客访问后，同时也被欧洲的那个节点缓存。这样，当再有欧洲其他地区的访客连接到一个没有缓存的节点时，这些节点会直接提供欧洲的那个节点的缓存。CloudFront 和 KeyCDN 就利用了这样的技术。 Cloudflare 是如何实现的他们官方没有详细说明。然而在实际测试时，并没有观察到缓存率上有明显提升，远比不过 CloudFront 的效果。下图是通过这些节点测试的 TTFB，请求是逐个发起的。

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/25a9957c-de0d-4f7a-d3d9-d3366332ba01/extra" alt="Cloudflare 上可被缓存的内容的首次访问测试，启用了 Argo" width="1940" height="1038"/>

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/86d28375-4541-48f8-a2cd-b2989cba5a01/extra" alt="CloudFront 对比，比 Cloudflare 要强" width="1934" height="1046"/>

## 降低 TTFB，Argo Smart Routing

通常情况下，节点与源站的连接是直接的，这之间的网络很大程度上取决于主机的网络接入。然而，有了 Argo Smart Routing，Cloudflare 会使用自己的线路。图片来自 Cloudflare.com。

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/256fae72-3104-4b59-4c6a-fe18314b5801/extra" alt="Argo Smart Routing 动态图" width="960" height="375"/>

国外请求测试地址，其中的 via 字段就是 Cloudflare 与本站建立的连接的 IP 地址。通过 GeoIP 服务查询，发现是香港的 IP。Cloudflare 将自己的节点之间都建立了长连接，并在离源站最近的服务器上与源站也提前建立了连接。这样，就能大大降低首次连接所需要的时间。如果回源是 HTTPS 的，那么效果更明显。我的另一个测试地址是没有开启这个功能的，用来对比，它的回源与本站建立的 IP 就不是香港的。

### 使用 Flexible SSL 的 TTFB 对比

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/46c49e08-8914-4b54-f8e7-84137dab0c01/extra" alt="没有启用 Argo" width="1946" height="1050"/>

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/cf1edeb1-09e8-4e1e-438c-e8edb6ee0401/extra" alt="启用了 Argo" width="1978" height="1060"/>

### 使用 Full SSL 的 TTFB 对比


<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/670536bb-6c79-403a-246e-7f479deba501/extra" alt="没有启用 Argo 并且是 Full SSL" width="1928" height="1040"/>

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/09b3a372-cf2f-434a-59fc-f86ec28a5201/extra" alt="启用了 Argo 并且是 Full SSL" width="1940" height="1030"/>

速度的确有一定的提升，但是不是特别明显，而且似乎开启了之后一些节点反而更不稳定——原本都是比较稳定的一个速度，开了这个之后一些节点反而忽快忽慢。看来提速的最佳方法还是半程加密。

# Cloudflare Railgun

Railgun 是 Cloudflare 专门为 Business 和 Enterprise 企业级客户提供的终极加速方案。要使用它，先需要升级网站套餐为 Business 或 Enterprise，然后还需要在服务器上安装必要软件并在 Cloudflare 上完成配置。这相当于是一个双边加速的软件，其实现原理是让服务器与 Cloudflare 建立一个长久的 TCP 加密连接，使用 Railgun 独有协议而不是 HTTP 协议，这样显然能减少连接延迟。此外，它还会对动态页面缓存：考虑到大多动态页面都包含了大量相同的 HTML 信息，在用户请求一个新的页面时，服务器将只发送那些变化了的内容。这相当于一种多次的 Gzip 压缩。

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/c836e291-5e2d-4d55-371a-1599cf34e101/extra" alt="开启 Railgun 截图" width="1125" height="1310"/>

官方宣称，使用 Railgun 能够实现 99.6% 的压缩率，并实现两倍的速度。实际体验也确实如此：

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/75969fec-6ff1-4bdf-204b-a64dfd5d2901/extra" alt="启用了 Railgun 并且是 Full SSL" width="1982" height="1060"/>

Railgun 的加速效果还是非常之明显的，明显强于 Argo。

# 总结

Argo 并没有想象中的那么好用，而且 **$5/mo** 的起步价和 **$0.10/GB** 的流量并不便宜。当然也有可能需要一段时间 Argo 去分析线路延迟才能更好的进行优化。本文预计将在一个月后补充更新。 Railgun 效果还是极其显著的，但是它需要企业版套餐才能够使用，并不亲民。

## 动态内容

**延迟**：Google Cloud CDN 延迟最低，Cloudflare Railgun 仅次。 **流量**：对于普通的动态 CMS，Cloudflare Railgun 大约能节省 10 倍以上流量，Google Cloud CDN 是做不到的。 我在[国内外几家全站 CDN 对比](https://guozeyu.com/2017/01/wordpress-full-site-cdn/)中测试 Google Cloud CDN 时，其极低的 TTFB 令我惊讶，仔细研究后发现节点是与主机之间建立长连接，而且会保持很长一段时间，此外所有网络都走 Google 内网，本质上与 Argo 和 Railgun 类似。所以目前服务动态内容最快的应该还属 Google Cloud CDN 了，Railgun 基本与之相当。

## 静态内容

CloudFront 自带的 Regional Edge Caches 在**缓存静态内容**和**提高缓存命中率**上要比 Argo Tiered Cache 和 Railgun 好，但是 Argo Smart Routing 在服务于动态的不可缓存的内容上更显出优势。Railgun 和 Google Cloud CDN 除了会在边缘节点缓存之外没有其他专门的优化。

## 关于本站的分区解析

本站的解析没有使用 Cloudflare 而是 自建的 DNS，因为我的 Cloudflare 域名是通过 CNAME 接入的。Cloudflare 分配的 IP 在很长时间内都不会变动，所以我直接把其 IP 设置为了海外线路。使用自建的DNS是为了在备案后，为国内分区解析配置 CDN 线路。 PS: 大家应该都知道启用这个功能后并不会提升国内连接 Cloudflare 的速度，如果想要用 Cloudflare 并且希望国内快一点，源站最好就用美国西岸的。
