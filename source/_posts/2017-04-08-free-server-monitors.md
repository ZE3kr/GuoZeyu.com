---
title: 几个免费的服务器监控服务推荐
tags:
  - VPS
  - 安全
id: '2456'
categories:
  - - 开发
date: 2017-04-08 08:00:34
languages:
  en-US: https://www.ze3kr.com/2017/04/free-server-monitors/
---

有了服务器监控服务，就能知道网站运行的情况以及在线时间。当服务器出现问题时，便能第一时间收到通知。个人网站运维需要这些服务来保证稳定性。但是，大量的此类服务都是面向企业，导致价格十分昂贵，而且并用不到其所提供的功能。本文就给各位站长推荐几个免费的服务器监控服务。
<!-- more -->

## 在线率（Uptime）监控服务

### StatusCake

StatusCake 同时提供免费和付费的监控服务。免费版本可以创建无限多个 HTTP(s)、TCP、DNS、SMTP、SSH、Ping 和 Push 的协议监控，监控周期最短为 5 分钟，提醒主要支持 E-mail 和 Webhook 两种方式。免费版本不支持监控服务器配置信息，所以也无需在服务器上安装任何软件。

<img src="https://cdn.tloxygen.com/images/93db5287-8f3b-4f3e-40f7-570470575f00/extra" alt="StatusCake 面板截图" width="2244" height="1232"/>

此外，StatusCake 支持 Public Reporting，你可以利用 StatusCake 建立一个监控页面。它还支持将在线率图像及网页嵌入在你自己的网页中，十分方便。

[注册地址](https://app.statuscake.com/Try/?Plan=FREE)

### UptimeRobot

UptimeRobot 也提供免费的监控服务，支持 HTTP(s)、端口检测、Ping，监控周期最短为 5 分钟。同样不支持监控服务器配置信息，所以也无需在服务器上安装任何软件。最多只能创建 50 个监控，支持 E-mail 提醒。

<img src="https://cdn.tloxygen.com/images/15ebe1ec-7d54-4be2-bcb2-7564c834bf00/extra" alt="UptimeRobot 面板截图" width="2906" height="1264"/>

相比 StatusCake，它的监控功能要少，但是 Public Reporting 的页面要漂亮一些。由于 StatusCake 所多的那些功能个人站长也几乎用不到，所以 UptimeRobot 也是 StatusCake 的一个良好的替代。

[注册地址](https://uptimerobot.com/signUp)

### 监控宝

监控宝是一家国内的监控服务，长期提供的免费版可以创建 6 个 HTTP(s)、Ping、DNS、FTP、TCP、UDP、SMTP 甚至是使用 SNMP 对服务器性能监控（需要软件），监控周期最短为 15 分钟。如果你需要检测国内到你的主机的速度，或者你的主机在国内，监控宝是一个不错的选择。它的免费监控是从中国内 3 个位置同时监控。它支持 E-mail 和**手机短信**告警。它不支持 Public Reporting，但是可以给分享网站的 SLA 证书。功能相当齐全，就是网站的界面设计比较欠缺。

[注册地址](https://www.jiankongbao.com/new_signup)

## 服务器指标监控服务

### Stackdriver

最后，就要介绍我最近开始使用的强大的 Stackdriver。Stackdriver 是 Google Cloud Platform（下文简称 GCP）旗下的服务器监控服务，支持监控、调试、跟踪、日志。其中的 Uptime Check 支持 HTTP(s) 和 TCP，监控周期最短为 **1 分钟**。它支持 E-mail 、手机短信和 APP 内告警。它的 Uptime Check 是从全球 6 个地区同时监控，可以看到每一个地区的延迟。用来检测 CDN 的速度肯定会很不错。

<img src="https://cdn.tloxygen.com/images/6e4bccc4-c6cc-4f37-53d9-a3c66b59a600/extra" alt="Stackdriver 面板截图" width="2580" height="1248"/>

如果你正在使用 Google Compute Engine（下文简称 GCE） 或者其他 GCP 的服务，那么这个服务还可以帮你记录服务器日志，每月有 5 GB 额度，超出后每 $0.5/GB。此外，它还能进行服务器性能监控，监控服务器各项指标。虽然原本 GCE 面板也能提供 CPU 等信息，但是这个是需要在服务器上安装 Agent，于是就能提供更丰富更准确的信息。安装过程如下：

```
curl -O https://repo.stackdriver.com/stack-install.sh
sudo bash stack-install.sh --write-gcm
curl -sSO https://dl.google.com/cloudagents/install-logging-agent.sh
sudo bash install-logging-agent.sh # 谨慎安装，见下文
```

毕竟是 Google 自家的服务，安装不需要任何登陆等操作。安装完毕后就会自动采集数据。它分为两个部分，Stack Monitoring 用于监控服务器指标，包括硬盘存储空间、内存占用（包括 Used、Buffered）等 GCE 默认无法监控到的数据。还有就是 Logging，它可以自动同步日志到 Google 的云端，你可以集中的执行搜索等操作。然而 Logging 这个进程会大概占用 100M 内存，小内存实例谨慎使用。需要注意的是，**只有 Logging 是免费的，Monitoring 是收费的，每月 $8**。 然后，你还可以为某一项指标建立告警，比如我创建了一个当磁盘空间高于 90% 的时候给我发邮件。 它可以创建公开页面分享服务器指标和 Uptime Check 延迟的图标，但是却不能显示在线率，实在是一个奇怪的设计。

### Observium

[Observium](http://observium.org/) 通过 snmp 可以用来监控服务器的各项指标，包括内存、储存、网卡等。它可以[免费安装](http://observium.org/wiki/Installation)在自己的服务器上，需要 MySQL 和 PHP 环境。官网给的是 Apache 的范例，如果你用 Nginx 就不需要安装 Apache。

<img src="https://cdn.tloxygen.com/images/050a7711-4b36-466b-5976-389ca7e52f00/extra" alt="Observium 面板截图" width="2590" height="1548"/>
 
它通过在服务端生成 PNG 来显示图表，所以图表很漂亮精致，但是由于不是矢量图，所以很难做到实时增量更新而且不能精细看到某一个时刻的数值。 Observium 可以轻松的管理许多个服务器。你可以体验[在线 Demo](http://demo.observium.org)。
