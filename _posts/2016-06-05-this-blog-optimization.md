---
layout: post
status: publish
published: true
title: 本网站底层的具体配置和优化
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 1691
wordpress_url: https://ze3kr.com/?p=1691
date: '2016-06-05 14:49:00 -0400'
date_gmt: '2016-06-05 06:49:00 -0400'
categories:
- 开发
tags:
- 网站
- 网络
- WordPress
- 安全
- VPS
---
<p>维护这个网站已经有一段时间了，是时候谈一谈这个网站的具体细节了。</p>
<p>我有多个网站，好几个不同的域名，不过这篇文章就只从 ze3kr.com 这一个网站做具体的介绍。跳过域名注册和 DNS 解析，直接从网站 Web 服务用的主机开始。<!--more--></p>
<h2>Web 服务器</h2>
<p>本网站拥有两个 Web 服务器，主服务器使用 OVH 的 VPS，还有一个台湾的 VPS 作为代理服务器，均使用 Ubuntu 16.04 LTS，并开启了自动更新，使用 LEMP （Linux + Nginx + MySQL + PHP）配置。写这篇文章时，Nginx 版本是 1.10.0，支持 HTTP/2 协议；MySQL 版本是 5.7，<a href="https://www.mysql.com/why-mysql/benchmarks/" target="_blank">比上一代快了 3 倍</a>；PHP 版本是 7.0，<a href="https://www.zend.com/en/resources/php7_infographic" target="_blank">也比上一代快了 3 倍</a>。</p>
<p>MySQL 和 PHP 仅是在主要的服务器有，因为主要的服务器配置最高。在访问网站时，访客会被分区解析到其他地区，连接到最近的服务器。</p>
<p>[img id="1692" size="large"]中心服务器[/img]</p>
<p>如果只有亚洲（东岸）的服务器，那么欧洲的速度会极慢，北美的速度也不会太好。如果只有北美东岸服务器，那么亚洲这边就很慢。相比之下，美国西岸似乎是一个不错的选择，整个美国的速度都不错，其他各地速度也都不太慢，但是在中国，线路也可能能给你绕死，本来也就 100ms 的事，有时能给你整到 700ms 甚至 1 秒以上，还不如纽约。</p>
<h2>Nginx 配置</h2>
<p>Nginx 的配置在东京和纽约的服务器都差不多，均开启了 HTTP/2，且都开启了反向代理，连向真正的后端 PHP 的端口。后端的 PHP 端口有两个，一个是无 SSL 加密的，供纽约服务器本地代理；另一个端口是有 SSL 加密的，供东京服务器代理。至于为什么要这样做，其实只是为了更好的动静分离，之后还会再讲关于这方面的内容。</p>
<p>真正面向访客的端口，才是 80 和 443 的 Web 端口，两个服务器都正确配置了 IPv6 的 AAAA 记录，访客连接时也是会优先尝试 IPv6。</p>
<h2>攻击防御</h2>
<p>我在 Nginx 上同时使用了 <code>set_real_ip_from</code> 和 <code>limit_req</code>，这样可以一定程度上避免 CC 攻击。具体细节就不透露，不同的页面有着不同的限制。</p>
<p>我的 DNS 解析支持监控自动切换功能，当我的亚洲服务器被 DDOS 时，会解析到 OVH，OVH 服务器的防御是相当不错的，只有每秒几十 G 的话几乎没事，秒解禁 IP 的。</p>
<h2>页面静态化与动静分离</h2>
<p>我的网站使用了 WordPress，这是一个动态博客引擎，所以静态化对缓存和防攻击都有很大的帮助。我选择了使用 “WP Super Cache” 作为静态化工具，当页面被访问后，该页面的 HTML 会以静态文件的形式存储下来，下次访问时直接由 Nginx 提供缓存文件，不经过 PHP，速度大大提高了。同时我的两个主机配置好了同步功能，缓存会几乎实时的同步（延迟 3 秒），这不仅对搜索引擎友好，还能明显的提高访问速度。除此之外，这个插件还有 Preload 功能，它会定期在后台缓存网站上的所有页面。</p>
<p>除此之外，Nginx 自身也会自动缓存所有静态文件以及可以缓存的由 PHP 生成的文件，充分优化性能。</p>
<h2>图片、视频存储</h2>
<p>这个网站的图片和视频等静态文件同时存储在服务器和 Amazon S3 上。服务器上的文件仅存放一个月，一个月后自动删除，以为我的主机提供更多的空间。我的网站上的图片有很多，所以存在 S3 上，是最经济且安全的选择。为了最大减少成本，我设置了当文件上传后 30 天，自动转化存储类型至<label class="lifecycle-rule-modal-checkbox-label" for="goldilocks-checkbox">标准低频率访问存储类。</label></p>
<p>[img id="1693" size="large"][/img]</p>
<p>这样做可以减少我的主服务器的带宽，至于如何让这些文件被访问，请看下面：</p>
<h2>CDN 网络</h2>
<p>本网站将文件存在了 S3 上，但为了防止恶意刷流量，S3 的数据必须要经过 CloudFlare 后才能被访问。具体可以通过添加桶策略的方式解决，<a href="https://git.tlo.xyz/ZE3kr/ZE3kr/snippets/5" target="_blank">详情参见代码</a>。</p>
<p>这样做的目的主要是 CloudFlare 公布了它们自己的 IP 段，这样做桶策略就比较方便，并不是所有 CDN 提供商都公布自己的 IP 段的。而且，这样做还能避免国内访问被墙，还能增加 HTTPS 功能。在 CloudFlare 之上又有 KeyCDN 和 UPYUN 两个 CDN，分别为国外和国内准备。分别开启了 Origin Shield 和镜像存储功能，再次减少到 S3 的请求并提高速度。</p>
<p>[img id="1779" size="large"]缓存服务器（CDN）[/img]</p>
<p>CloudFlare 是免费的，所以不怕被盗链。对于 KeyCDN 和 UPYUN，我使用了 Token 防盗链技术，Token 有效期不会超过两周。</p>
<p>所以，如果想拿我网站上的图片进行外链，请将 URL 中的域名替换为 s3.tlo.link，并删除链接后面 <code>?</code> 及其之后的东西。否则，链接是不会长期有效的。</p>
<h2>统计功能优化</h2>
<p>本网站使用自己的 Piwik 进行统计，所有报表都能存到自己的数据库里，而不是别人的。为了提高统计速度，本网站对发往统计脚本的请求做了特殊的处理：先立即返回 204，然后再后台异步的发送请求给后端服务器，达到速度最优化。</p>
<h2>网站备份及灾难恢复</h2>
<p>本网站使用 BackWPup 插件对我的 MySQL 数据库及关键配置文件进行备份，同时存储在本地文件夹和 S3 上。一旦服务器出现严重问题，可以直接从备份中恢复到任何一个服务器上。Amazon 自身提供很好的灾难恢复服务，存储在 S3 上的内容一般不会丢失。</p>
<p>[img id="1694" size="large"]后台截图[/img]</p>
<p>备份有多么重要？假如你的服务商把你的服务停掉了，或者是服务商硬盘损坏，那么你的所有数据可就没了啊。而且备份还有助于那天不小心进 root 给 <code>rm -rf /*</code> 了之后还能恢复……而且这个还有助于方便你更换主机服务商，说换就换，直接从备份中恢复。</p>
<h2>自动部署</h2>
<p>我的网站基本上可以说是自动部署的，以下部分使用自建 GitLab + CI Runner 部署的：</p>
<ul>
<li>WordPress 的一个自己定制的 “插件”</li>
<li>Nginx 的配置文件，自动部署到所有主机并 Reload</li>
<li>放在同一个服务器上的静态网站，和一些 PHP 脚本</li>
</ul>
<p>服务器上几乎所有的部分都开启了自动更新，包括但不限于：</p>
<ul>
<li>所有基于 apt 和 gem 安装管理的软件包会自动更新</li>
<li>WordPress 内核会自动更新到最新的 Stable 版本；所有的插件会自动升级</li>
<li>Piwik、GitLab 内核会自动更新到最新的 Stable 版本</li>
</ul>
<p>在配置好了自动部署之后，就算有很多服务器，需要有修改时也十分方便。</p>
