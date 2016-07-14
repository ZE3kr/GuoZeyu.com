---
layout: post
status: publish
published: true
title: DDOS 压力测试
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 1773
wordpress_url: https://ze3kr.com/?p=1773
date: '2016-07-14 09:15:33 -0400'
date_gmt: '2016-07-14 01:15:33 -0400'
categories:
- 日志
tags: []
---
<p>今天，我在 Google 上搜了几个免费的 DDOS 压力测试软件，对我的服务器进行 NTP 攻击，为预防真正 DDOS 攻击做实战，具体结果如下：</p>
<p><!--more--></p>
<h2>攻击对象</h2>
<p>攻击对象均为我自己的 VPS，系统为 Ubuntu 16.04 LTS，在软件上均无特殊防护，服务提供商和位置为：</p>
<ul>
<li>Vultr 硅谷，未购买 DDOS 防御</li>
<li>Vultr 日本，无 DDOS 防御</li>
<li>OVH 加拿大，包含 DDOS 高级防御</li>
<li>36cloud 香港，无 DDOS 防御</li>
</ul>
<h2>结果</h2>
<p>Vultr 的两个服务器在每次被攻击时，检查 Vultr 自己提供的面板，都能看到有 1o0Mbps 的带宽（服务器不限带宽），单核 CPU 上升至 40%，服务器的 Ping 服务未遭到影响。</p>
<p>OVH 在被攻击时，能看到有 50Mbps 的带宽（服务器是 100Mbps 带宽），CPU 没有增加，应该是有 DDOS 防御的效果。</p>
<p>36cloud 被直接打挂，截止到目前已经被指向黑洞快一小时了，毕竟本来带宽就很小。之后还会继续更新，看什么时候恢复。</p>
