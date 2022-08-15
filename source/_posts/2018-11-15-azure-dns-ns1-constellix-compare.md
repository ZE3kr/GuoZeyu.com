---
title: Azure DNS、NS1、Constellix，三家海外 GeoDNS 服务商对比
tags:
  - CDN
  - DNS
  - 网络
id: '3718'
categories:
  - 科技
date: 2018-11-15 11:45:54
languages:
  en-US: https://www.ze3kr.com/2018/11/azure-dns-ns1-constellix-compare/
---

最近 DNSPod 的解析服务器[宕机了一段时间](https://www.ithome.com/0/394/009.htm)，导致许多 DNSPod 用户的网站无法访问。本文将推荐几个提供 100% SLA 的海外 GeoDNS 服务，可用于替代不稳定的 DNSPod。并介绍一下使用多家 DNS 提供商来提高服务可用性的方法。

本文包括 Azure DNS、NS1、Constellix 的全面对比。
<!-- more -->

## 简介

这次推荐的三家 DNS 均是海外的支持 GeoDNS 的服务商，并都支持 Anycast。其中的 Azure DNS 和 NS1 在国内的速度都非常好，是直接走的香港/亚洲线路，平均延迟能够在 50ms 以内，不输国内 DNS 服务商。而这三家的海外速度都是极快的，可以秒杀 DNSPod、CloudXNS、阿里云解析等国内提供商。

关于 GeoDNS，精细度最好的是 Constellix：支持国家、省、市（包括中国的省、市），甚至是 AS 号（可以实现分运营商解析）、IP 段。NS1 支持国家、美国的州，也支持 AS 号、IP 段。至于 Azure DNS，通过使用 Traffic Manager，可以支持国家、部分国家的州、IP 段。由于都支持 IP 段，所以理论上支持任意精度的 GeoDNS 了。

至于价格，NS1 提供了免费额度（每月 500k 请求），对于小流量网站而言是够用的，可一旦超出这个免费额度，那么收费是很高的：**$8/百万个请求**。相比而言，Azure DNS 和 Constellix 的价格都很便宜了，其中 Constellix 有 **$5/月** 的起价。

## 使用多家 DNS 服务商

同时使用多家 DNS 服务商是可行的，只要使用服务商的 DNS 记录保持相同即可。这要求所使用的服务商能够配置主域名的 NS 记录（建议但不是必须）。这次介绍的 Azure DNS、NS1、Constellix 三家，以及以前介绍过的 Route 53、Google Cloud DNS、Rage4、阿里云解析均可配置主域名的 NS 记录（其中阿里云解析和 Azure DNS 只能额外添加第三方记录；其他几家可以完全自定义 NS 记录）。而 Cloudflare 以及国内的 DNSPod、CloudXNS、阿里云解析由于不能配置主域名下的 NS 记录，意味着你不能很好的进行 DNS 混用。

要使用多家 DNS，有两种实现方法：主服务商和从服务商方式以及两个主服务商方式。完成配置后，将多家的 NS 服务器配置到权威 NS 记录以及域名注册商下的 NS 列表。

使用两家服务商会增加配置难度，但可以提高 DNS 服务的稳定性。如最近的 [DNSPod 宕机](https://www.ithome.com/0/394/009.htm)以及 [2016 年的 Dyn 宕机](https://en.wikipedia.org/wiki/2016_Dyn_cyberattack)所导致的众多网站无法访问，均是因为众多网站仅使用了一家 DNS 提供商。如果使用了多家 DNS 提供商，则网站仅会在所使用的所有服务商均发生宕机事故时才会无法访问，而这显然发生概率很小。

### 主服务商和从服务商

要想这样配置，需要从服务商支持使用 AXFR 从主服务商获取记录，还需要主服务商也支持 AXFR 传输。其中，NS1、Constellix 均可作为主服务商或从服务商，意味着你可以同时使用这两家，并选择任何一家作为主服务商，另一家作为从服务商。

### 两个主服务商

你也可以使用两个主服务商，并保持所有的记录（包括主域名下的 NS 记录）相同（建议，但不是必须）。建议也将 SOA 的序列号同步。

### 样例

`github.com.` 这个域名就同时使用了 Route 53 和 DYN 的服务。这可以使用 dig 工具验证。

```
$ dig github.com ns +short  
ns1.p16.dynect.net.  
ns2.p16.dynect.net.  
ns3.p16.dynect.net.  
ns4.p16.dynect.net.  
ns-1283.awsdns-32.org.  
ns-421.awsdns-52.com.  
ns-1707.awsdns-21.co.uk.  
ns-520.awsdns-01.net.
```

经检验，在两家 DNS 服务商也配置了相同的记录。

```
$ dig @ns1.p16.dynect.net. github.com a +short  
13.229.188.59  
13.250.177.223  
52.74.223.119  
$ dig @ns-1283.awsdns-32.org. github.com a +short  
13.229.188.59  
13.250.177.223  
52.74.223.119  
```

* * *

下面逐个介绍一下这三家 DNS 提供商。

[所有 DNS 测评一览](https://wiki.tloxygen.com/DNS_提供商)（还包括 CloudXNS、Route 53、Cloudflare、Google Cloud DNS、Rage4 以及阿里云解析）

## Azure DNS

微软 Azure 产品线下的 DNS 服务。使用 Anycast 技术，并且国内能够直接连接到香港/亚洲节点，所以速度很快。

值得注意的是，类似 Route 53，Azure 所分配的四个服务器使用的是不同的网段、不同的线路，可能有更高的可用性。

注意：Azure 的 DNS 的分区解析可能不兼容 IPv6，这意味的解析结果可能会被 Fallback 到默认线路。

*   国外速度：★★★★☆，36 ms
*   北美速度：★★★★☆，27 ms
*   亚洲速度：★★★★☆，39 ms
*   欧洲速度：★★★★☆，29 ms
*   国内速度：★★★★☆，49 ms
*   最短 TTL：**0s**
*   国内分区解析：★★★★★，支持配置到中国，支持 IP 段配置（配合 Traffic Manager）
*   国外分区解析：★★★★★，支持配置到大州、国家以及部分国家的州，支持 IP 段配置（配合 Traffic Manager）
*   DNSSEC：**不支持**
*   IPv6：**支持**
*   记录类型：支持 A、AAAA、CNAME、NS、MX、TXT、SRV、CAA、PTR。
*   根域名 CNAME 优化：**不支持**
*   优先级：**支持**（配合 Traffic Manager）
*   自定义 NS：**仅支持添加额外的 NS**
*   价格：每个域名 $0.5/月，**$0.4/百万个请求**
*   用例 A 价格：**$0.90**
*   用例 B 价格：**$10.50**
*   SLA：100%

## NS1

NS1 也使用了 Anycast 技术，并且国内能够直接连接到香港/亚洲节点，所以速度很快。

*   国外速度：★★★★★，23 ms
*   北美速度：★★★★★，9 ms
*   亚洲速度：★★★★☆，40 ms
*   欧洲速度：★★★★★，20 ms
*   国内速度：★★★☆☆，65 ms
*   最短 TTL：**0s**
*   国内分区解析：★★★★★，支持配置到中国，支持 IP 段配置。
*   国外分区解析：★★★★★，支持配置到大州、国家、美国的州，支持 AS 号、IP 段配置。
*   DNSSEC：**支持**
*   IPv6：**不支持**
*   记录类型：支持 A、AAAA、AFSDB、CAA、CERT、CNAME、DS、HINFO、MX、NAPTR、NS、PTR、RP、SPF、SRV、TXT。
*   根域名 CNAME 优化：**支持**
*   优先级：**支持**（配合 Traffic Manager）
*   自定义 NS：**仅支持添加额外的 NS**
*   价格：每月前 500k 请求免费，超出部分 **$8.0/百万个请求**
*   用例 A 价格：**$4**
*   用例 B 价格：**$156**
*   用例 C 价格：**$156**
*   SLA：100%

## Constellix

此 DNS 以 GeoDNS 优势著称，使用了 Anycast 保证最低的延迟。

Constellix 三组有六个 DNS 服务器，每一组使用了不太相同的线路。

*   国外速度：★★★★☆，31.51 ms
*   北美速度：★★★★★，9.81 ms
*   亚洲速度：★★★★☆，48.87 ms
*   欧洲速度：★★★★★，23.77 ms
*   国内速度：★★☆☆☆，108.9 ms
*   最短 TTL：**0s**
*   国内分区解析：★★★★★，可以精确到每一个省、市，可以配置 ASN 以实现运营商分区解析。
*   国外分区解析：★★★★★，精确到了各个国家、省、市
*   DNSSEC：**不支持**
*   IPv6：**支持**
*   记录类型：**更加**齐全，只支持 A、AAAA、CNAME、NS、MX、TXT、SRV、HINFO、NAPTR、CAA、CERT、PTR、RP、SPF。
*   根域名 CNAME 优化：**支持**
*   优先级：**支持**
*   自定义 NS：**支持**
*   价格：第一个域名 **$5/月**，此后每个域名 **$0.5/月**。**$0.4/百万个请求**。分区解析 $0.6/百万个请求
*   用例 A 价格：**$5.39**
*   用例 B 价格：**$14.90**
*   用例 C 价格：**$19.30**
*   统计功能：**支持**，可以看到每个国家、城市的请求数。甚至还可以启用日志，看到每一个请求的客户端 IP、IP 数据包类型等等。
*   SLA：100%
