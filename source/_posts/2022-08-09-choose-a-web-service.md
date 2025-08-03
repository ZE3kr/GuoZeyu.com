---
title: 云服务推荐及选择指南
date: 2022-08-09 09:40:00
# ads: false
tags: 
 - 网站
 - 网络
categories:
 - 开发
---

本文推荐一些本人用过或者正在使用的云服务，持续更新。未来还会增加购买建议。

## 云计算 Compute/VPS/EC2

### 最便宜: Scaleway Stardust

0.5GB RAM ，每月 €0.36，限速 100Mbps 不限流量（该价格为**仅 IPv6** 的价格，约¥2.48）[购买链接](https://console.scaleway.com/instance/servers/create?area=fr-par-1&imageKey=1123148c-7660-4cb2-9fd3-7b5b4896f72f&offerName=STARDUST1-S)

| 内存 | 存储 | CPU 核心数 | CPU 基线 | 带宽 | 价格 |
| --- | --- | --- | --- | --- | --- |
| 1GB | 10GB SSD | 1 | N/A | 100Mbps | €0.36 |

> * N/A 代表共享核心

目前只有 Paris 1 和 Amsterdam 1 有。前往上方购买链接（如果跳转到了登录/注册页面，请在登录后再点一次，即可直达 Stardust 购买页面），如果遇到无货，就拉到最底部复制 API，用 API 创建机器一般可以绕过前端的无货购买限制。

<!--API 调用方法一般如下：

```shell
scw instance server create type=STARDUST1-S zone=fr-par-1 image=ubuntu_jammy root-volume=l:10G name=scw-affectionate-morse ip=new project-id=64f4694b-0521-4cc7-9851-1cfb8a12f3c3
```

需要将 `64f4694b-0521-4cc7-9851-1cfb8a12f3c3` 换成自己的 `project-id`。此处创建的是 Paris 1，如果需要创建 Amsterdam 1，将 `zone` 处的 `fr-par-1` 换为 `nl-ams-1`。同理，这里镜像我选择了 Ubuntu 22.04，如需更换则修改 `image` 即可。-->

### 性价比: BuyVM

1GB RAM ，每月 CA$3.50 ，1Gbps 不限流量（约¥17.77 ）。[购买链接](https://my.frantech.ca/aff.php?aff=5211)。注意，BuyVM 的计费周期是每月 1 日，所以若购买时不是 1 日，那么首月的价格会有所变化（因为到下个月或下下个月的 1 日不是整一个月）。

| 内存 | 存储 | CPU 核心数 | CPU 基线* | 带宽 | 价格 |
| --- | --- | --- | --- | --- | --- |
| 1GB | 20GB SSD | 1 | N/A | 1000Mbps | CA$3.50 |
| 2GB | 40GB SSD | 1 | N/A | 1000Mbps | CA$7.00 |
| 4GB | 80GB SSD | 1 | 100% | 1000Mbps | CA$15.00 |
| 8GB | 160GB SSD | 2 | 100% | 1000Mbps | CA$30.00 |

> * N/A 代表共享核心，100% 代表每个核心均为独立核心

配置更高的主机价格是同比例增加的，可以参考 4GB 版本乘以 N。

付款时使用支付宝即可使用 CA$ 同币值缴费，相对要比美元便宜很多。本网站就在使用 BuyVM，如果你从美国西部访问此页面，那就会使用 BuyVM 的服务器。

BuyVM 还可以购买 Block Storage Slab，每 TB 仅需要 $5.00，最低每月 $1.25 就可以买到 256GB。

### 国内网络好: AWS Lightsail

0.5GB RAM ，每月$3.50 ，限流量 1TB ，可开日本、法兰克福、美国西部均可直连（约¥22.21 ）[购买链接](https://aws.amazon.com/lightsail/)

| 内存 | 存储 | CPU 核心数 | CPU 基线 | 流量 | 价格 |
| --- | --- | --- | --- | --- | --- |
| 0.5GB | 20GB SSD | 1 | 5% | 1TB | $3.50 |
| 1GB | 40GB SSD | 1 | 10% | 2TB | $5.00 |
| 2GB | 60GB SSD | 1 | 20% | 3TB | $10.00 |
| 4GB | 80GB SSD | 2 | 20% | 4TB | $20.00 |

> * 代表每个 CPU 的基线性能，均可 Burst，[Lightsail CPU 基线详情](https://lightsail.aws.amazon.com/ls/docs/en_us/articles/amazon-lightsail-viewing-instance-burst-capacity)

本网站就在使用 Lightsail，如果你从美国东部或者亚洲 (中国大陆外) 访问此页面，那就会使用 Lightsail 的服务器。Lightsail 可以免费附加静态 IPv4；IPv6 则是随机器附带的，暂不可保留或迁移。此外，Lightsail 的自动快照功能是免费的，可以作为自动备份。

#### 与其他同类产品对比

Vultr/Linode/Digital Ocean 都是与 Lightsail 类似的产品，价格也与 Lightsail 十分接近。他们可能没有 CPU 基线的限制，但这也未必是好事，因为失去了限制更有可能受到邻居（同机器上其他用户）的影响。这里有一些优惠码：

+ [Vultr $100 优惠码](https://www.vultr.com/?ref=8963265-8H): 赠金 14 日内有效
+ [DigitalOcean $100 优惠码](https://m.do.co/c/3676c36d032c): 赠金 60 日内有效

### 大内存、多 IP: OVH VPS

2GB RAM，每月 $3.5（或 3 欧元），限速 100Mbps 不限流量，法兰克福可直连中国，但美国可能绕欧洲。OVH 两欧就可以买一个额外 IPv4 ，该 IPv4 没有月费，只要机器在 IP 就一直是你的。一个账户可以买 16 个额外 IPv4。[美国区购买链接](https://us.ovhcloud.com/vps/)、[国际美元区购买链接](https://www.ovhcloud.com/en/vps/)、[国际欧元区购买链接](https://www.ovhcloud.com/en-ie/vps/)

美国境内机器只有美国区可以购买，其他位置机器需去国际区购买。

| 内存 | 存储 | CPU 核心数 | CPU 基线* | 带宽 | 价格 |
| --- | --- | --- | --- | --- | --- |
| 2GB | 20GB SSD | 1 | N/A | 100Mbps | $3.50/€3.00 |
| 2GB | 40GB SSD | 1 | N/A | 250Mbps | $6.00/€5.00 |
| 4GB | 80GB SSD | 1 | N/A | 500Mbps | $11.50/€10.00 |
| 8GB | 160GB SSD | 2 | N/A | 1000Mbps | $23.00/€20.00 |

> * N/A 代表共享核心

本网站就在使用 OVH，如果你从欧洲访问此页面，那就会使用 OVH 的服务器。

### 永久免费: Google Cloud Compute Engine

美国 `us-central-1`，`us-west-1`，`us-east-1` 区域，1GB RAM ，30GB 最基础的盘，仅 IPv4 ，出站流量自费。这个试用期过了也免费！

| 内存 | 存储 | CPU 核心数 | CPU 基线* | 流量 | 价格 |
| --- | --- | --- | --- | --- | --- |
| 1GB | 30GB HDD | 2 | 12.5% | 按量计费 | 永久免费 |

> * 12.5% 代表每个 CPU 可以有 12.5% CPU time，可 Burst，[GCP E2 共享 CPU 详情](https://cloud.google.com/compute/docs/general-purpose-machines#e2-shared-core)

### 最灵活: AWS EC2

[AWS 国际区](https://aws.amazon.com/ec2/)，[AWS 中国区](https://www.amazonaws.cn/ec2/)

本网站就在使用 EC2 (中国区)，如果你从中国大陆访问此页面，那就会使用 EC2 的服务器。目前 AWS 中国区仅限企业注册。

#### 与 Google Cloud 的 Compute Engine 和 Azure 的 VMs 对比

Google Cloud 的按量付费可能是三者中的最便宜的，此外个人感觉 Google Cloud 的操作页面使用起来相比 Azure 和 AWS 更简单。这几家提供的产品都大同小异，此外他们也都可以预留实例一年到三年，实现最大化折扣

#### 与阿里云、腾讯云对比

最大的区别是计费模式。AWS 无论是国内版还是国际版，均默认使用后付费模式，即类似信用卡，每月支付上一个月结算的账单。而阿里云和腾讯云无论是个人用户还是企业用户，均使用预付费模式。哪怕使用的是按量付费的机器，竟然也需要提前充值；余额耗尽后会直接停机。所以用阿里云或腾讯云的按量计费服务之前，无比要保证有充足的余额，不然将欠费了将直接影响生产环境。

此外，阿里云和腾讯云在 API 使用上没有 AWS 方便。虽然 AWS 国内版在国内没有阿里云那么大的占有率，但由于 AWS 国内版和国际版 API 保持了一致，因此 AWS 的文档资源要远比阿里云、腾讯云丰富的多。

个人认为，AWS 国际版完胜阿里云、腾讯云的海外区域，AWS 国内版核心问题是支持的可用区太少，只支持北京和宁夏两个区域；Azure 国内版支持的区域比 AWS 国内版要多一点；阿里云和腾讯云在国内区域数量上是远多于 AWS 的。不过在网络方面，目前 AWS 国内版也是 BGP 接入，线路完全不比阿里云差。

数据跨境方面（指中国大陆境内与境外之间的跨境），阿里云提供了香港精品网、云企业网，这是 AWS 所没有的。

## 虚拟主机

这里推荐氧熙科技/TlOxygen 的虚拟主机，香港位置采用 CN2 GIA 线路，美国加州也是国内直连；均无需备案，购买后即用。相比之下，美国加州提供更多的流量和存储，带宽也更大。他们的虚拟主机还支持 SSH 访问、Let’s Encrypt 证书自动签发（不可 root）。[香港/加州虚拟主机购买链接](https://www.yangxi.tech/web-hosting/index.php)


<img src="https://cdn.tlo.xyz/images/0cc92be8-8ca0-4693-a9da-0b901641b100/extra" alt="氧熙科技的香港 CN2 GIA 虚拟主机测速" width="2436" height="2060"/>

## CDN

等待补充
<!-- ### CloudFront

CloudFront 分为国内版和国际版，两者完全独立，并且国际版 -->

## DNS

等待补充

## 域名注册类

等待补充
