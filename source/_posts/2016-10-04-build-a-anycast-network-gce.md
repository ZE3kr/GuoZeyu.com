---
title: 利用 GCE 建立一个 Anycast 网络，超快的香港节点，Google Cloud CDN
tags:
  - Google Cloud Platform
  - VPS
  - 网络
id: '1998'
categories:
  - - 开发
date: 2016-10-04 09:27:00
languages:
  en-US: https://www.ze3kr.com/2016/10/cloudflare-2016-new-feature/
cover: <img src="https://cdn.yangxi.tech/images/04e0bbc3-4b73-4cde-7c0f-2bb2b35f5e00/extra" alt="缓存服务器的位置" width="1124" height="482"/>
---

[在上一篇文章](https://www.guozeyu.com/2016/10/asia-google-compute-engine/)中，我简单的介绍了 Google Compute Engine（简称 GCE）的基础使用。这篇文章我将介绍如何利用 GCE 建立一个 Anycast 网络，并测试了其速度。 想要实现这个功能，就需要使用 [Cross-Region Load Balancing](https://cloud.google.com/compute/docs/load-balancing/http/cross-region-example)（跨地区的负载均衡），此功能就相当于一个 HTTP(S) 的反向代理，所以只能针对 HTTP/HTTPS 请求进行负载均衡。
<!-- more -->

## 简要概述

GCE 上所实现的这个功能是基于第七层的网络代理，所以其拓扑图是这样的： 用户 —> 边缘服务器 —> 实例

*   **用户到边缘服务器之间的连接**：使用 HTTP 或 HTTPS；如果是 HTTPS 连接，那么 TLS 加密过程是在边缘服务器上实现。
*   **边缘服务器到实例的连接**：使用 HTTP 或 HTTPS 连接，之前的网络是走的 Google 的专线。

不论配置了几个位置的实例，边缘服务器都是使用 Google 全部的边缘服务器。 启用这个功能后，就会得到另一个 Anycast 的 IP 地址，这是个独享的 IP 地址。

什么是 Anycast？Anycast 能够让多个主机使用一个 IP 地址，当用户连接这个 IP 地址的时候，连接到的只是这多个主机中的其中之一，通常会选择最快的线路，能有效的降低延迟，所以很多 DNS/CDN 提供商都使用了 Anycast。

此外，目前使用负载均衡是唯一能让其原生支持 IPv6 的方法。具体可以参见其文档：[IPv6 Termination for HTTP(S), SSL Proxy, and TCP Proxy Load Balancing](https://cloud.google.com/compute/docs/load-balancing/ipv6)

<img src="https://cdn.yangxi.tech/images/05526956-c779-4a8f-95d8-80ae5b59b700/extra" alt="预留 IPv6 地址的截图" width="1604" height="1056"/>

## 配置方法

### 建立实例

首先，需要前往到 GCE 后台，建立至少两个不同地区的实例，我专门为测试 Anycast 功能建立了两个新的实例：

<img src="https://cdn.yangxi.tech/images/27dca1a3-238f-4656-8f06-0047b0b74b00/extra" alt="为 Anycast 建立的两个实例" width="1700" height="298"/>

每个地区也可以建立多个实例以提高可用性，而我只给每个地区建立了一个实例，这两个实例分别叫 anycast-asia 和 anycast-us。

### 建立实例组

然后，需要给每个地区的实例[建立一个实例组](https://console.cloud.google.com/compute/instanceGroups/add)：

<img src="https://cdn.yangxi.tech/images/bb687697-6b3c-47d7-ed89-d134d8d95000/extra" alt="实例组配置页面" width="962" height="1588"/>

需要注意的是，实例组配置页面中位置里的 “多地区（Multi-zone）” 是指同一个**地区**（Region）的不同**可用区域**（Zone），而不是多个不同的地区，所以这实际上是翻译的错误，应该叫做 “多可用区域” 才对。

* * *

刚接触云服务的人可能不理解可用区域的概念，可以参考 [AWS 的这篇文章](http://docs.aws.amazon.com/zh_cn/AWSEC2/latest/UserGuide/using-regions-availability-zones.html)来理解。简单点说，地区这个概念就是指离得很远的地区（比如城市之间，如北京和上海），所有在北京的服务器都算北京地区，所有在上海的服务器都算上海地区。但是为了能达到更高的可用性，通常还会在同一个地区设立多个数据中心，也就是可用区域。这些可用区域虽在一个地区中，其之间的距离可能相隔几十甚至几百公里，但这些可用区域之间的距离和不同地区之间的距离相比起来要小得多，所以这些可用区域之间的网络延迟也很低。

设立多个可用区域的意义是：可以能加更高的可用性（主要是为了避免外界因素：比如说火灾等），虽然是异地分布，但是可用区域之间的距离并不远，所以网络延迟可以忽略。

我只给每个地区建立了一个实例，所以我只需要选择 “单地区（Single-zone）”。每个地区都需要建立一个实例组，所以一共需要建立两个（或更多的）实例组。我配置了两个实例组，分别叫 asia-group 和 us-group。

### 建立负载均衡

完成前两步之后，就需要建立负载均衡的规则了，需要选择 “HTTP(S) 负载平衡” 来实现 Anycast 的功能。

<img src="https://cdn.yangxi.tech/images/82658e9b-c6cb-4d62-d457-07b768625800/extra" alt="三种负载均衡模式" width="1998" height="686"/>

在负载均衡的配置界面，把这两个实例组都添加到 “后端” 中。 该功能还需要创建一个运行状态检查（相当于监控功能），当主机宕机后能实现切换。

<img src="https://cdn.yangxi.tech/images/e2f53c4f-53b7-44a4-bc27-84bd5114f400/extra" alt="暂时先不开启 CDN 功能" width="2488" height="1918"/>

<img src="https://cdn.yangxi.tech/images/2a0a01bf-5f9a-4fca-d774-a752d3b7cb00/extra" alt="保留默认的 “主机路径和规则”" width="1398" height="500"/>

<img src="https://cdn.yangxi.tech/images/71d21649-5c89-4c70-9c69-efe892560f00/extra" alt="目前是需要 HTTP 的例子，如果需要 HTTPS，还需要指定一个证书。" width="1400" height="448"/>

创建成功后，可以看到如下界面，其中的 IP 地址就是 Anycast IP 了。

<img src="https://cdn.yangxi.tech/images/3c917e4d-66c7-462a-ed17-f81f9d74b100/extra" alt="成功创建了一个 Anycast IP" width="1612" height="1064"/>

### Nginx 的配置

这里的配置只是为了方便调试，实际使用不用额外修改 Nginx 的配置。 在这两个主机上都安装 Nginx，然后稍微改动默认的配置文件：增加两个 Header，然后 Reload。

```
add_header X-Tlo-Hostname $hostname always;
add_header Cache-Control "max-age=36000, public";
```

### 检测是否可用

在测试 Anycast 之前，先测试这两个主机是否存在问题。为了方便阅读，我将 curl 的 IP 地址换为主机名，并省略两个主机都相同的 Header 字段

```
$ curl anycast-us -I
HTTP/1.1 200 OK
…
ETag: "57ef2cb9-264"
X-Tlo-Hostname: **anycast-us**
$ curl anycast-asia -I
HTTP/1.1 200 OK
…
ETag: "57ef2b3b-264"
X-Tlo-Hostname: anycast-asia
```

可以看到这两个主机都没有什么问题。然后，我用我的电脑去测试那个 Anycast IP：

```
$ curl anycast-ip -I
HTTP/1.1 200 OK
…
ETag: "57ef2b3b-264"
X-Tlo-Hostname: anycast-asia
Accept-Ranges: bytes
Via: 1.1 google
```

可以看到，这是 anycast-asia 主机响应的。现在我使用另一个在美国的主机继续测试这个 Anycast IP：

```
$ curl anycast-ip -I
HTTP/1.1 200 OK
…
ETag: "57ef2cb9-264"
X-Tlo-Hostname: anycast-us
Accept-Ranges: bytes
Via: 1.1 google
```

此时就是 anycast-us 主机响应的，是因为客户端离这个服务器更近。 当通过 Anycast IP 访问时，就可以看到 HTTP Header 中的 `Via: 1.1 google` 字段。

## 速度测试

### Ping 测试

Ping 测试发现速度很快，看来反代的操作是放在 Google 的边缘服务器上了。**速度堪比 Google 啊！**

<img src="https://cdn.yangxi.tech/images/2536da17-6a82-4483-6102-f3fc24958c00/extra" alt="对 Anycast IP 的国外速度测试" width="1868" height="944"/>

中国的速度那更是一流的快，Google 有香港的边缘节点，所以基本上是直接走的香港节点，比原本的连接台湾可用区快不少。（只有部分 IP 段是完全直连的）

<img src="https://cdn.yangxi.tech/images/4bd11bf9-cabc-4d5b-b416-7d9012261900/extra" alt="对 Anycast IP 的国内速度测试" width="2410" height="1208"/>

### HTTP GET 测试

在开启 CDN 功能之前，负载均衡器是不会对任何内容缓存的，所以会发现 Connect 的速度很快，但是 TTFB 延迟还是有不少。

<img src="https://cdn.yangxi.tech/images/8ac73308-2f5f-4374-5877-77a2481c5f00/extra" alt="对 Anycast IP 进行 HTTP GET 测试" width="1918" height="1048"/>

可以预测，如果启用了 HTTPS 功能，其 TLS 所需要的等待时间也会很短，TTFB 时间不变，总时长不会延长太多。

#### 开启 CDN 后进行 HTTP GET 测试

当将 CDN 开启后，负载均衡器就会自动地对静态资源做缓存了，当缓存命中后会显示 Age 字段，这个字段是表示自缓存命中了，后面的数字代表这是多少秒之前缓存的内容。

curl anycast-ip -I
HTTP/1.1 200 OK
…
Via: 1.1 google
Age: 10

经过多次执行这个指令，会发现有一定几率 Age 字段消失，这可能是流量指到了同一个地区的不同可用区上。但总之，是缓存命中率不高，即使之前曾访问过了。

<img src="https://cdn.yangxi.tech/images/433f5e12-3475-4a92-4b0e-fd4bbb43e200/extra" alt="开启 CDN 后进行 HTTP GET 测试" width="1946" height="1046"/>

多次运行测试确保有缓存之后，发现速度似乎并没有太多明显的提升。能够明显的看出改善的是：巴黎和阿姆斯特丹的 TTFB 延迟从 200ms 减少到了 100ms，然而还是不尽人意。可能的原因是：Google 并没有将内容缓存到离访客最近的边缘节点上，而是别的节点上。 [CDN 缓存服务器的位置列表](https://cloud.google.com/cdn/docs/locations) 

<img src="https://cdn.yangxi.tech/images/04e0bbc3-4b73-4cde-7c0f-2bb2b35f5e00/extra" alt="缓存服务器的位置" width="1124" height="482"/>

## 统计与日志

开启了 Load Balancing 后，就会自动在 Google Cloud Platform 下记录一些信息了。

### 实时流量查看

在网页后台的 Network，Load balancing，advanced menu 的 Backend service 下，可以查看实时的流量情况： 

<img src="https://cdn.yangxi.tech/images/c2cabc9e-0789-4d47-81eb-b59b92462400/extra" alt="图形还是很漂亮的" width="2274" height="1238"/>

### 延迟日志

在网页后台的 Stackdriver，Trace 下，可以看到延迟日志： 

<img src="https://cdn.yangxi.tech/images/49721dc7-25c6-4aa6-a291-9995f254ef00/extra" alt="延迟日志截图 1" width="2354" height="1470"/>

<img src="https://cdn.yangxi.tech/images/f90d5a7a-df1e-4bc2-2b2f-3a9eaf724400/extra" alt="延迟日志截图 2" width="2082" height="1474"/>

这里的延迟包含了网络延迟和服务器响应延迟

## 总结

GCE 所能实现的 Anycast 功能，只能通过 HTTP 代理（第七层）的方式实现，所以只能代理 HTTP 请求，其他功能（如 DNS）无法实现。所以很多功能受限于负载均衡器的功能（比如证书和 HTTP2 都需要在负载均衡器上配置），然而由于 TLS 加解密过程是在边缘服务器上实现，而且其本身也带有 CDN 功能，所以会比单纯的 Anycast（比如基于 IP 层，或是 TCP/UDP 层）的更快一些。

### 价格

前五个 Rules **$18/月**，流量费用相比 GCE 不变，已经被缓存的内容的流量有一点优惠。

## 对比

### Cloudflare

通过使用 Cloudflare 所提供的服务也能实现 Anycast，也是基于第七层的，即将也能实现 Cross-Region Load Balancing 的功能。虽然它还不能根据主机的 CPU 占用率去调整权重（毕竟它拿不到这些数据），却有强大的 Page Rules 功能以及 WAF 功能。 CloudFlare 并不提供独立 IP 地址，不过这不是什么大问题。 由于它属于第三方服务，不受服务提供商的限制，于是就可以给多种不同的服务提供商去做 Anycast 功能；而且无论服务商是否支持，都能够使用。 连接速度上，GCE 的在中国连接速度有明显的优势。

### BuyVM

BuyVM 是一家 VPS 提供商，却提供免费的 Anycast 功能，其 Anycast 功能是直接基于 IP 层的 Anycast，所以可以配置 HTTP 之外的各种服务。BuyVM 没有所谓的边缘服务器一说，只能有三个节点，Ping 的结果不像前两家那么快，而且 TLS 过程也是在原本的主机（这三个主机中里用户最近的一个）上进行，也会有一定延迟。 BuyVM 并不提供任何亚洲的主机，所以中国的连接速度也没有比 Cloudflare 快多少，整个亚洲的速度也不是很快。
