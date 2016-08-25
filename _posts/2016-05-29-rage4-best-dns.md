---
layout: post
status: publish
published: true
title: Rage4、CloudXNS、Route53 和 CloudFlare — 国内外 DNS 解析对比
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 1668
wordpress_url: https://ze3kr.com/?p=1668
date: '2016-05-29 14:43:00 -0400'
date_gmt: '2016-05-29 06:43:00 -0400'
categories:
- 科技
tags:
- 网站
- 网络
- DNS
---
<p>DNS（<a href="https://zh.wikipedia.org/wiki/域名系统" target="_blank">域名系统</a>）是因特网的一项服务。它能够将域名指向一个 IP（服务器），这样你就可以通过域名来访问一个网站。能够通过域名访问的网站，都需要一个 DNS 服务器。</p>
<p>Rage4 是一家很棒的解析商，要我说，它是 Route53 的完美替代品，其本身服务器支持 IPv6，DNS 支持 DNSSEC、DANE，甚至还支持自定义 NS！我拿它和国内 CloudXNS，以及国外 Route53、CloudFlare 进行一下对比：<!--more--></p>
<h2>基本 DNS 功能</h2>
<p>那些基本的记录就不说了，从<strong>最短 TTL 说起</strong>：CloudFlare 最低 120 s，CloudXNS 最低 60 s，Rage4 和 Route53 都没有最低时长，或者说最低时长为 0。</p>
<p><strong>分区解析</strong>（Geo Routing/Latency Based Routing）：CloudFlare 完全不支持这个功能，但其本身提供 CDN 服务，也就是说，如果你只有一个服务器，那用 CloudFlare 是最合适的。剩下的三者都对其有所支持，都能精确到国家，其中只有 CloudXNS 能够精确到中国各省份的各运营商，但是 Rage4 支持到了一些国家的不同区域（但是对于一些大洲还没有支持到国家），Route53 支持一些国家的不同城市。Rage4 和 CloudXNS 支持智能解析，CloudXNS 会自动识别你的多个服务器，实现真对不同的运营商、不同的区域有不同的解析，但是只对国内的服务器有效；Rage4 的智能解析不是自动的，但却更强大：你可以输入你的服务器的经纬度，然后它会根据访客 IP 的地理位置，返回最近的IP。注意，目前</p>
<p><strong>服务器位置</strong>：Rage4、Route53 和 CloudFlare 的服务器都遍布全球，每个 DNS 域名都只对应一个 IP 地址，因为它们都使用了 Anycast 技术，能保证最低的延迟，但它们在国内有没有服务器。CloudXNS 的服务器在国外几乎没有，国内有不少，但没有使用 Anycast 技术，所以到最后，解析的时候只是随机连一个国内的服务器，无法做到最低延迟，但对于国内来说，至少不会很慢。目前 Rage4 似乎不支持 EDNS client subnet，其他两个似乎支持。</p>
<p><strong>DNSSEC</strong>：目前只有 Rage4 和 CloudFlare 支持，能够防止 DNS 污染，但很大程度上还是取决于运营商的配置。Rage4 几乎支持所有的 DNSSEC 加密技术，通用型更强，而 CloudFlare 则是给你选一种。</p>
<p><strong>IPv6</strong>：目前只有 Rage4 和 CloudFlare 的 DNS 服务器正确配置了 IPv6，但它们四者都能解析 AAAA 记录。</p>
<p><strong>DANE</strong>（TLSA 记录）：目前只有 Rage4 支持，安全性方面的，本文暂不做过多介绍。</p>
<p><strong>自定义 NS</strong>（Vanity NS/Traffic Flow/Custom Nameservers）：就是配置的 DNS 服务器为自己的域名，目前只有 CloudXNS 不支持，其他三个都支持。但是只有 Rage4 和 Route53 的这项服务是免费的。</p>
<p>举个例子，就是这个效果：</p>
<p>[img id="1673" size="large"][/img]</p>
<p><strong>A 记录镜像</strong>（ANAME/CNAME Flattening）：“A 记录镜像” 是我编的中文名，其实就是可以将根域名解析到 CNAME，实际上是解析商在每次解析前，会检查这个 CNAME 指向的 A 记录，然后直接返回这个 A 记录而不是 CNAME，这样不仅能提高解析速度，还可以允许你在根域名下绑定 CNAME。目前只有 CloudFlare 和 Rage4 支持。</p>
<p><strong>CDN 功能</strong>：准确的说，这不应该写在这里，不过我还是说一下。CloudFlare 提供免费的 CDN 功能和基本 DDOS 防御，只需点一下就能开启；CloudXNS 可以和牛盾配合使用，也能提供加速功能和 DDOS 防御；Route53 可以配合它们自己的 CloudFront 和 WAF 实现加速功能和 DDOS 防御；Rage4 不支持 CDN 或是 DDOS 防御，这些功能要取决于你的主机，你也可以使用第三方的服务。</p>
<p>所以，从基本 DNS 功能来说，Rage4 无论从功能、安全性还是分区解析上，都要好一些。CloudFlare 也是不错的选择，能很方便且免费的提供 CDN 和 DDOS 防御，CloudXNS 对于国内支持的是最好的，然而国外就一般了。</p>
<h2>监控功能</h2>
<p>CloudXNS 提供免费的监控功能，监测点仅限中国，灵活性非常有限；CloudFlare 虽不提供监控，但其本身直接提供 Always Online 功能，保证网站一直在线；Route53 有 Health Check，但也是付费的。</p>
<p>Rage4 本身没有监控功能，但可以很方便的和很多别的监控服务搭配使用，例如免费的 StatusCake，灵活性最大。只需要在设置里添加一个 Webhook，再在监控的地方输入 Rage4 提供的 Webhook 即可。</p>
<p>[img id="1671" size="large"][/img]</p>
<p>[img id="1672" size="large"][/img]</p>
<p>当 Rage4 收到了宕机通知之后，会暂停解析，或者更改解析内容，这取决于你的设置。</p>
<h2>统计功能</h2>
<p>CloudXNS 和 Rage4 都能统计解析量，都能精确到国家，CloudXNS 更是能精确到省份和运营商。CloudFlare 虽然没有解析量统计，但提供了更详尽的访客统计（前提是你得开了 CDN）；Route53 尚且不清楚有没有这个功能。</p>
<h2>速度测试</h2>
<p>速度测试是随机挑的 IP，做的 Ping 测试，Ping 的速度很大程度上就决定了解析的速度。</p>
<p>如果大家想要自己测试，可直接测试下方域名：</p>
<ul>
<li>CloudFlare: <code>gordon.ns.cloudflare.com</code></li>
<li>Rage4: <code>ns1.ze3kr.com</code></li>
<li>CloudXNS: <code>lv3ns1.ffdns.net</code></li>
<li>Route53: <code>ns-1084.awsdns-07.org</code></li>
</ul>
<h3>国内速度</h3>
<h4>CloudXNS</h4>
<p>在国内，速度还是一流的</p>
<p>[img id="1675" size="large"][/img]</p>
<h4>Route53</h4>
<p>国内速度还凑合，但有一些地区超级慢</p>
<p>[img id="1676" size="large"][/img]</p>
<h4>Cloudflare</h4>
<p>国内速度最慢的，有不少地区超级慢</p>
<p>[img id="1677" size="large"][/img]</p>
<h4>Rage4</h4>
<p>国内速度仅次于 CloudXNS，没有国内服务器，还能到这个地步，很难得</p>
<p>[img id="1678" size="large"][/img]</p>
<h3>国外速度</h3>
<h4>CLOUDXNS</h4>
<p>在国外，速度不出意料是最慢的</p>
<p>[img id="1682" size="large"][/img]</p>
<h4>ROUTE53</h4>
<p>总体速度还可以</p>
<p>[img id="1680" size="large"][/img]</p>
<h4>CLOUDFLARE</h4>
<p>国外速度最快的，当然也是国内最慢的</p>
<p>[img id="1679" size="large"][/img]</p>
<h4>RAGE4</h4>
<p>总体速度还可以</p>
<p>[img id="1681" size="large"][/img]</p>
<h2>价格</h2>
<p>Rage4、CloudXNS 和 CloudFlare 都有永久免费的服务，也都可以按需升级。Route53 是按需付费的，每个域名每月 0.5 美元，请求单独按需计费。</p>
<p><strong>免费额度</strong>：Rage4 每月每域名 50 万次，在此之后每 100 万次 2 欧元；CloudXNS 每月每域名 1 亿次，基本上就是无限的了；CloudFlare 没有写额度，有可能是不限的。</p>
