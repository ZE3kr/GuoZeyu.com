---
title: Google Compute Engine 新手教程及使用体验
tags:
  - Google Cloud Platform
  - VPS
  - 网络
id: '1933'
categories:
  - - 开发
date: 2016-10-01 11:00:48
cover: <img src="https://cdn.tlo.xyz/6T-behmofKYLsxlrK0l_MQ/c3c14a40-2f52-4401-a62d-b0975cc88c00/extra" alt="一些基本的配置" width="1526" height="1204"/>
---

2017 年 4 月更新：由于 GCE 在国内经常不稳定，本站主机已经换到了 [TlOxygen 的虚拟主机](https://domain.tloxygen.com/web-hosting/index.php)上了。 最近想要寻找按流量计费、连接中国速度比较快的 VPS，最终选择了 Google Compute Engine（下文简称 GCE）的亚洲区。GCE 的后台配置页面虽不能在中国访问，但是其 GCE 实例是可以在中国访问的。 创建一个新的 GCE 的流程十分简单，只需要自定义配置、选择操作系统、配置 SSH Key，然后选择创建就好了，整个流程十分像 VPS，但是可自定义的功能却远比 VPS 多。
<!-- more -->

## 价格与配置

[具体价格请参见官方价格表](https://cloud.google.com/compute/pricing)。（由于有持续使用折扣，每月实际价格比按照每小时更低） GCE 的价格比较亲民，最低配 (f1-micro) 1 共享核-0.6 GB 内存-10GB HDD 每月只需要不到 5 美元，而且由于 CPU、内存大小和磁盘大小都是可调的，所以可以根据自己的需要去购买最适合的，能省去不必要的开销。 而且对于北美的部分机房而言，账户首个最低配 (f1-micro) 实例可以享受到[永久免费配额](https://cloud.google.com/free/?hl=zh-cn)，对于建站而言 (再配合 Cloudflare 使用) 还是很划算的。流量的话对于所有的可用区，**连中国大陆 $0.23/Gbyte**、美欧地区 $0.12/Gbyte，流量的价格有些小贵，但是如果是连接 Google 自己的服务的话（包括但不限于 Gmail、YouTube），流量不计费（但是流量是双向的，所以是本地通过 GCE 上传完全免费，下载还是原价）。 GCE 还有一点比较特殊的是它是按分钟计费的，当服务处于终止状态（相当于关机，磁盘数据保留）时，不收取费用（除了少量的磁盘使用费用）。每次计算 Uptime 时，如果不到 10 分钟则一律按十分钟算，超过 10 分钟后才是真正的按分钟计费，不过还是很划算了。

### 关于共享核的实例的补充

f1-micro（0.6 GB）和 g1-small（1.7GB）这两个版本使用的是共享核心（其余配置都是独立核心），根据 Google 的说明，0.60GB 是 0.2 vCPU，1.70GB 是 0.5 vCPU。但是却支持 Bursting，也就是短时间内最高能使用到 1.0 vCPU。 那么 1.0 vCPU 是多少呢？查 cpuinfo，是 Intel(R) Xeon(R) CPU @ 2.50GHz。也就是说这两个版本最高能占用到 2.5GHz。但是假如长时间占用，速度就会压缩到 0.5GHz 和 1.75 GHz。 

<img src="https://cdn.tlo.xyz/6T-behmofKYLsxlrK0l_MQ/e164b6b1-f90f-4f51-b705-a2fb91c50800/extra" alt="监控图" width="1364" height="482"/>

我的 f1-micro 装了监控软件，对比 GCE 给的 CPU 占用率（深蓝色）和系统自己监控到的占用率（浅蓝色），发现 GCE 图表上统计的 CPU 占用率正好是本地统计的 5 倍，也就是说如果本地看到的 CPU 占用是 20%，GCE 图表上显示的就正好是 100%，本地为 20~100%，GCE 图表上就是 100~500%，这时就算作 Bursting 了。 和其他 VPS 对比，其他的 VPS 也几乎都是共享核心，但你却无从判断是否超售。比如有 10 个用户共用一个核心，如果那 10 个人都在不停的占用 CPU，那么你的 CPU 速度会低于单核的十分之一。而 Google 的共享核心，保证了一个最低的速度（0.2 vCPU 和 0.5 vCPU），就算其他用户用的再狠，也能给你保证一定的速度。

## 使用流程以及配置方法

首先需要前往[创建实例](https://console.cloud.google.com/compute/instancesAdd)的页面，然后进行配置

### 基础配置

<img src="https://cdn.tlo.xyz/6T-behmofKYLsxlrK0l_MQ/c3c14a40-2f52-4401-a62d-b0975cc88c00/extra" alt="一些基本的配置" width="1526" height="1204"/>

### 其他一些选项配置介绍

*   **“抢占”**：该模式能够获得更低廉的价格，但是不能用做需要长期保持在线的服务（比如 Web 服务），它最长的使用期限是 24 小时，然而在我的使用中，它有时候不到 1 小时就会被终止使用。它只适合短时间去计算一些东西，计算完后中止它，平常的一般使用不要开启此功能。
*   **自动重启**：推荐开启，以获得在云端的好处，以及更好的 Uptime
*   **主机维护期间**：推荐选择 “迁移”，原因同上
*   **IP 转发**：建议关闭，几乎不会用得着此功能，关闭有助于提高安全性
*   **SSH**：这可能不同于其他一些 VPS，它默认不自动生成用户密码，所以为了远程登录必须配置好公钥私钥。而且所填写的公钥末尾的用户名是有作用的，所填写的用户名就是所需要登录的用户名，默认不支持 root 登陆，除非你将用户名设置成了 root。

<img src="https://cdn.tlo.xyz/6T-behmofKYLsxlrK0l_MQ/52cfe5e9-c089-4610-cd74-10f615514f00/extra" alt="SSH 配置截图" width="860" height="358"/>

### 防火墙配置

GCE 默认开启了防火墙且不能关闭，只能允许你自己指定的协议和端口的流量；经过我自己的实际测试，GCE 能够自动过滤相当的 DDOS 攻击流量。 由于防火墙不能关闭，所以不能配置类似 IPv6 Tunnel 的服务，所以导致目前的 GCE 是不能够支持 IPv6 的，不过相信以后 Google 还是会启用 IPv6 支持。 在 “网络” 里，可以找到[防火墙规则](https://console.cloud.google.com/networking/firewalls/list)，然后可以[添加防火墙规则](https://console.cloud.google.com/networking/firewalls/add)。 默认已经允许了 SSH 和 ICMP 等（以 default 开头的）

<img src="https://cdn.tlo.xyz/6T-behmofKYLsxlrK0l_MQ/c64b9397-a7d7-45b3-cf9d-650f7b1fd300/extra" alt="我所启用的所有规则列表" width="1378" height="876"/>

<img src="https://cdn.tlo.xyz/6T-behmofKYLsxlrK0l_MQ/757dde15-414d-4809-7380-d1e3067b2700/extra" alt="SNMP 监控配置" width="968" height="908"/>

我只需要另一个主机去访问 SNMP 监控，不需要将其公开到互联网上，所以限制了 IP。 

<img src="https://cdn.tlo.xyz/6T-behmofKYLsxlrK0l_MQ/bfc7ce68-e6c8-4f79-6482-83e1d2fc6900/extra" alt="用做权威 DNS 服务器的配置" width="968" height="908"/>

有的 DNS 请求是通过 TCP 发送的，所以需要同样启用 TCP 请求。 如果配置了目标标记，那么就不是默认应用到所有实例的防火墙规则，还需要在实例上配置好同样的标记才可以。

<img src="https://cdn.tlo.xyz/6T-behmofKYLsxlrK0l_MQ/23490937-e0fe-4094-e125-52f4ac361c00/extra" alt="添加上相同的标记" width="952" height="140"/>

## 网络——如同 Google 自身一样棒

由于 GCE 是 Google 的，其网络其实也是 Google 的 AS15169，于是不用想也知道连接 Google 旗下的服务会很快，但是实际使用后发现远比我想象的快，GCE 连接 Google 的服务简直就像内网一样。

```
$ ping google.com
PING google.com (74.125.203.102) 56(84) bytes of data.
64 bytes from th-in-f102.1e100.net (74.125.203.102): icmp_seq=1 ttl=53 time=0.631 ms
64 bytes from th-in-f102.1e100.net (74.125.203.102): icmp_seq=2 ttl=53 time=0.433 ms
64 bytes from th-in-f102.1e100.net (74.125.203.102): icmp_seq=3 ttl=53 time=0.330 ms
64 bytes from th-in-f102.1e100.net (74.125.203.102): icmp_seq=4 ttl=53 time=0.378 ms
64 bytes from th-in-f102.1e100.net (74.125.203.102): icmp_seq=5 ttl=53 time=0.413 ms
^C
--- google.com ping statistics ---
5 packets transmitted, 5 received, 0% packet loss, time 3999ms
rtt min/avg/max/mdev = 0.330/0.437/0.631/0.103 ms
```

要是 traceroute 的话就更神奇了：

```
$ traceroute google.com
traceroute to google.com (64.233.189.113), 64 hops max
  1   64.233.189.113  0.826ms  0.313ms  0.435ms
```

中国目前连接 GCE 亚洲区的速度还算不错，按流量计费，实际测试能够超过 100M 带宽。亚洲区的位置在台湾，中国连接通常会绕香港，然后从香港到台湾这条路线走的是 Google 自己的骨干网，所以最终的结果只是比香港服务器慢了十几毫秒而已。

其他国家连接的话优势就更明显了，Google 的网络实在太强大，无论是亚洲还是欧美，几乎还没有出这个城市/国家就立即跳进 Google 的骨干网络里了，然后 Google 自身的骨干网的选路通常要比运营商的选路要好一些，几乎不会出现绕路的情况。

### 配置 Anycast

**详细内容已在[下一篇文章](https://www.guozeyu.com/2016/10/build-a-anycast-network-gce/)中介绍**，通过 Load Balancing 可以分配到一个**独立的** IPv4 地址，还能够有原生的 IPv6，支持 CDN 和 HTTPS。Google 的 Anycast 有多快？和 Google 一样快。

### 内部网络互通

每个实例都有其对应的内网 IP，方便两个主机间建立直接的连接，这个内网 IP 的神奇之处在于：它可以跨可用区使用，包括跨大洲。经过实际测试，使用内网 IP 建立的连接速度会稍快，而且收取的价格（如果是跨州的话）也有优惠。

## 功能

### 统计功能

在 GCE 的后台，能够显示长达 30 天的 CPU 利用率、磁盘字节数、磁盘操作字节数、网络字节数、网络数据包量等数据，都能够精确到分钟级别的记录，而且是实时更新的。

### 快照

你可以为主机增加快照，方便在其他实例中恢复快照，或者用做备份功能。GCE 的快照是增量备份，这意味着如果有两个快照，第二份快照只备份与第一份快照的差异部分。[使用这个脚本](https://github.com/jacksegal/google-compute-snapshot)可以实现自动备份，并且能够实现到期自动删除老备份。

### 附加硬盘

你可以轻松的添加任意（大于 10 GB）大小的硬盘，有多种磁盘种类可供选择。经过我的测试，如果执行长时间的高 I/O 操作，硬盘读写速度会明显地降低。而且并不一定 SSD 就比 HDD 快，硬盘的大小与吞吐量限制和随机 IOPS 限制呈正相关，也就是说 40G 的 HDD 的速度相当于 10G 的 SSD。

## 适用场景

低配置的版本用来建站或者是当 “梯子” 都很不错，每月 5 美元的价格还是很吸引人的（做 “梯子” 的一定要注意流量价格！）。已经相当完善的 Web 控制页面可以大大降低使用和学习的门槛。高配置的版本拥有独立 CPU，而且可伸缩性很大。与 Google Cloud 各种功能配合使用，可以搭建大型网站或者[游戏服务器](https://cloud.google.com/solutions/gaming/)。

## GCE 的一些坑

下面来讲一讲它的一些坑，尤其是从传统 VPS 转到 GCE 会遇到的一些问题。由于 GCE 是基于云端的，所以有很多东西都不太一样。

### 主机的外网 IP 并不是直接分配的

GCE 虽然是提供独立 IP 的，但是当你执行 `ifconfig -a` 时，你会看到默认分配的 IP 是一个内网的保留 IP（形如 10.123.0.1），这是为了方便 instance 里之间互相连接的 IP。当你配置一些需要外网的服务要 bind 到指定 IP 时，你只需要 bind 到这个 IP 即可，所分配给你的外网 IP 会自动将流量转发到这个 IP 上。而且，你的主机并不知道那个外网 IP 就是主机本身，所以所有发向主机对应的外网 IP 的流量都会经过一个路由器，然后再由路由器返回，并且会应用防火墙策略。所以，如果需要本地的通讯，建议尽量多使用环回地址（如 127.0.0.1，::1）或那个内网地址。

### 防火墙

不能关闭的防火墙对于初次接触的人来说可能有些不适，不过一旦习惯后便会爱上这个功能，这个基于在路由器上的防火墙功能要比在 iptables 里做限制更方便。强烈建议利用好防火墙功能，不要默认允许所有端口。比如你的网站流量全部经过 Cloudflare，那么就可以通过防火墙来只允许 Cloudflare 的 IP 与你的主机连接。

### 不支持多 IP 与 IPv6

GCE 上不能通过加钱的方式去购买多个 IPv4 地址，所以一个实例只能有一个 IPv4 地址，需要多个 IPv4 需求的可以尝试多个实例（或者可以通过 Load Balancing 来实现多个 IP 地址）。 同时，GCE 目前不支持 IPv6，这实在是很可惜的。目前已经可以[通过负载均衡器来实现 IPv6](https://www.guozeyu.com/2016/10/build-a-anycast-network-gce/)。
