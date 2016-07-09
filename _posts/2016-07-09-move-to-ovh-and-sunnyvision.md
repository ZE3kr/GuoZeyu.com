---
layout: post
status: publish
published: true
title: 从 Vultr 迁至 OVH & SunnyVision
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 1742
wordpress_url: https://ze3kr.com/?p=1742
date: '2016-07-09 11:01:00 -0400'
date_gmt: '2016-07-09 03:01:00 -0400'
categories:
- 文章
tags:
- 网站
- 网络
---
<p>已经用了 Vultr 大概一个季度了，Vultr 的带宽、免费的 50GB 附加 SSD 等都是非常不错的，而且 Vultr 按小时的计费模式，超快速的部署都是十分棒的。但是 Vultr 的价格还是偏高，有不少大的 VPS 提供商都比它便宜了，而且 Vultr 的 VPS 方案的内存实在是太小了，每月 20 美元，却只能买到 2GB 内存的主机，实在不爽。于是我便开始寻找其他可供替代的主机商：<!--more--></p>
<p>首先，我需要的是在北美的 VPS，因为网站上的许多数据都是存放在 AWS 美国东岸节点上的，经过在网上找各种对比，最终决定将 OVH 的 VPS 作为主要服务器；然后将 SunnyVision 的香港服务器作为中国的加速。</p>
<h2>OVH VPS</h2>
<p>首先考虑的就是价格，OVH VPS 分为 SSD、Cloud 和 Cloud RAM，后两者都是分布式的服务器，提供更高的可用率（99.99%），Cloud RAM 相比之下提供更大的内存；而前者是高性价比的 SSD 服务器，有更高的性价比，可用率稍低（99.95%），但是比一些其他厂商好的是，它是 RAID10 磁盘阵列，所以能保证高速的 SSD，并且文件可靠性高数倍。</p>
<p>我选择的就是 SSD VPS，它分为内存为 2GB、4GB 和 8GB 版本，价格分别是 $3.49、$6.99、$13.49，这个价格基本比很多其他 VPS 价格便宜 5 倍以上。</p>
<p>经测试，磁盘写入速度要好于其他很多 SSD VPS（中档的写入在 600MB/s 左右），带宽也是 100M，不限流量，速度稳定。只有法国和加拿大两个地方的，不过加拿大的 Ping 美国东岸延迟仅在 7-15ms 左右，完全可替代美国服务器。</p>
<h3>其他特色：</h3>
<ul>
<li>附带免费、无限流量和次数的 DDOS 防御</li>
<li>可添加更大的 SSD 硬盘</li>
</ul>
<h3>缺点：</h3>
<ul>
<li>尚无 IPv6 支持</li>
<li>Snapshot 功能需要付费，并且没有自动备份功能（只能手动添加 Snapshot，Cloud 版本有自动备份功能）</li>
</ul>
<p>OVH 的 VPS 可以直接在 OVH 官网就能买到，首次购买和注册略麻烦，可能需要几天才能激活主机。</p>
<h2>SunnyVision</h2>
<p>SunnyVision 提供直连大陆的香港独立服务器，价格较高，建议从代理商那里买。我使用它的最低配 VPS，512MB 版本，作为给大陆的加速的节点，主要是作为 Nginx 代理服务器，需要的配置并不用很高。</p>
<p>相比 Vultr 在亚洲的日本节点，SunnyVision 的在中国大陆连接终于不会绕道，速度明显提升。</p>
<p>[img id="1748" size="large"]SunnyVision 香港服务器[/img]</p>
<p>[img id="1747" size="large"]Vultr 日本服务器[/img]</p>
<h3>缺点：</h3>
<ul>
<li>尚无 IPv6 支持</li>
</ul>
<h2></h2>
<h2></h2>
