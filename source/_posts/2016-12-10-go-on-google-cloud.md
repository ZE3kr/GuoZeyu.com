---
title: 全面迁移到 Google Cloud Platform
tags:
  - CDN
  - DNS
  - Google Cloud Platform
  - WordPress
  - 网站
id: '2254'
categories:
  - - 开发
date: 2016-12-10 19:24:36
---

2017 年 4 月更新：由于 GCE 在国内经常不稳定，本站主机已经换到了 [TlOxygen 的虚拟主机](https://domain.tloxygen.com/web-hosting/index.php)上了。 关于 GCE 的使用方法，可以参见 [Google Compute Engine 新手教程及使用体验](https://guozeyu.com/2016/10/asia-google-compute-engine/)。 这一周，终于将这个网站**全面迁移到 Google Cloud Platform** 上了。WordPress 原站服务器从 OVH 迁移到了 Google Compute Engine（简称 GCE），对象存储从 Amazon S3 换到了 Google Cloud Storage。同时，原先自建的 DNS 也换到了 Google Cloud DNS。
<!-- more -->

所使用的 GCE 是 1.7GB 版本（经过几周的使用，发现 0.6GB 加上合理的 SWAP 就足够了，已经降级）的，主要是因为有比较占用内存的 Piwik 统计软件，目前实际占用长期在三分之一以下。原先 WordPress 是安装在 OVH 的服务器上的，然后 GCE 亚洲东区缓存加速。现在重新迁移到了 GCE 亚洲东区上，由于减少了网络延迟，动态内容的速度快了很多。看来现在 GCE 的确是 VPS 的首选。其实，0.6GB 的版本应该也是够用的。10GB 硬盘价格：0.6GB @ $5.00/月；1.7GB @ $15.73/月。若要换用 SSD，需要再加 $1.3/月。详细的 [GCE](https://guozeyu.com/2016/10/asia-google-compute-engine/) 介绍

对象存储换成了 **Google Cloud Storage**，与 GCE 同区，配合 [gcsfuse](https://github.com/GoogleCloudPlatform/gcsfuse) 几乎可以当作本地硬盘使用。但是，要注意目录和文件数量不要太多，否则会严重影响性能。我使用的是 Regional Storage，价格 $0.02/GB/月。价格比 S3 稍稍低一些。经测试，Google Cloud Storage 的静态文件存储服务在中国似乎可以正常访问，这相比几乎无法访问的 S3 要好不少。前端我使用了 UPYUN 和 Cloudflare 直接进行分发。[价格表](https://cloud.google.com/storage/pricing)

**Google Cloud DNS** 是具备 Anycast、IPv6 和 DNSSEC (需要申请) 的，而且中国连接也不怎么绕道。但是需要注意的是，Google Cloud DNS 所给的四个 NS 中第一个在中国是被屏蔽了的，所以配置时将其删除即可。价格十分低廉：每个域名 @ $0.2/月，$0.40/百万个请求。[价格表](https://cloud.google.com/dns/pricing)

至此，原本分布式的两个 VPS 改为了一个，管理起来终于方便多了，而且亚洲的访问速度反而是变快了。全部上云后灵活性以及稳定性有明显改善！

注：WordPress 主题已经换成 2017 版新版主题。
