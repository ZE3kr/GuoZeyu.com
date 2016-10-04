---
layout: post
status: publish
published: true
title: 利用 GCE 建立一个 Anycast 网络，超快的香港节点
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 1998
wordpress_url: https://ze3kr.com/?p=1998
date: '2016-10-04 09:27:00 -0400'
date_gmt: '2016-10-04 01:27:00 -0400'
categories:
- 开发
tags:
- 网络
- VPS
---
<p><a href="https://ze3kr.com/2016/10/asia-google-compute-engine/">在上一篇文章</a>中，我简单的介绍了 Google Compute Engine（简称 GCE）的基础使用。这篇文章我将介绍如何利用 GCE 建立一个 Anycast 网络，并测试了其速度。</p>
<p>想要实现这个功能，就需要使用 <a href="https://cloud.google.com/compute/docs/load-balancing/http/cross-region-example" target="_blank">Cross-Region Load Balancing</a>（跨地区的负载均衡），此功能就相当于一个 HTTP(S) 的反向代理，所以只能针对 HTTP/HTTPS 请求进行负载均衡。</p>
<p><!--more--></p>
<h2>简要概述</h2>
<p>GCE 上所实现的这个功能是基于第七层的网络代理，所以其拓扑图是这样的：</p>
<p>用户 —> 边缘服务器 —> 实例</p>
<ul>
<li><strong>用户到边缘服务器之间的连接</strong>：使用 HTTP 或 HTTPS；如果是 HTTPS 连接，那么 TLS 加密过程是在边缘服务器上实现。</li>
<li><strong>边缘服务器到实例的连接</strong>：使用 HTTP 或 HTTPS 连接，之前的网络是走的 Google 的专线。</li>
</ul>
<p>不论配置了几个位置的实例，边缘服务器都是使用 Google 全部的边缘服务器。</p>
<p>启用这个功能后，就会得到另一个 Anycast 的 IP 地址，这是个独享的 IP 地址。</p>
<p style="padding-left: 30px;">什么是 Anycast？Anycast 能够让多个主机使用一个 IP 地址，当用户连接这个 IP 地址的时候，连接到的只是这多个主机中的其中之一，通常会选择最快的线路，能有效的降低延迟，所以很多 DNS/CDN 提供商都使用了 Anycast。</p>
<h2>配置方法</h2>
<h3>建立实例</h3>
<p>首先，需要前往到 GCE 后台，建立至少两个不同地区的实例，我专门为测试 Anycast 功能建立了两个新的实例：</p>
<p>[img id="2000" size="medium"][/img]</p>
<p>每个地区也可以建立多个实例以提高可用性，而我只给每个地区建立了一个实例，这两个实例分别叫 anycast-asia 和 anycast-us。</p>
<h3>建立实例组</h3>
<p>然后，需要给每个地区的实例<a href="https://console.cloud.google.com/compute/instanceGroups/add" target="_blank">建立一个实例组</a>：</p>
<p>[img id="2001" size="medium"]实例组配置页面[/img]</p>
<p style="padding-left: 30px;">需要注意的是，实例组配置页面中位置里的 “多地区（<span class="s1">Multi-zone</span>）” 是指同一个<strong>地区</strong>（Region）的不同<strong>可用区域</strong>（Zone），而不是多个不同的地区，所以这实际上是翻译的错误，应该叫做 “多可用区域” 才对。</p>
<p style="padding-left: 30px;">
<hr />
<p style="padding-left: 30px;">刚接触云服务的人可能不理解可用区域的概念，可以参考 <a href="http://docs.aws.amazon.com/zh_cn/AWSEC2/latest/UserGuide/using-regions-availability-zones.html" target="_blank">AWS 的这篇文章</a>来理解。简单点说，地区这个概念就是指离得很远的地区（比如城市之间，如北京和上海），所有在北京的服务器都算北京地区，所有在上海的服务器都算上海地区。但是为了能达到更高的可用性，通常还会在同一个地区设立多个数据中心，也就是可用区域。这些可用区域虽在一个地区中，其之间的距离可能相隔几十甚至几百公里，但这些可用区域之间的距离和不同地区之间的距离相比起来要小得多，所以这些可用区域之间的网络延迟也很低。</p>
<p style="padding-left: 30px;">设立多个可用区域的意义是：可以能加更高的可用性（主要是为了避免外界因素：比如说火灾等），虽然是异地分布，但是可用区域之间的距离并不远，所以网络延迟可以忽略。</p>
<p>我只给每个地区建立了一个实例，所以我只需要选择 “单地区（Single-zone）”。每个地区都需要建立一个实例组，所以一共需要建立两个（或更多的）实例组。我配置了两个实例组，分别叫 asia-group 和 us-group。</p>
<h3>建立负载均衡</h3>
<p>完成前两步之后，就需要建立负载均衡的规则了，需要选择 “HTTP(S) 负载平衡” 来实现 Anycast 的功能。</p>
<p>[img id="2004" size="large"]三种负载均衡模式[/img]</p>
<p>在负载均衡的配置界面，把这两个实例组都添加到 “后端” 中。</p>
<p>该功能还需要创建一个运行状态检查（相当于监控功能），当主机宕机后能实现切换。</p>
<p>暂时先不开启 CDN 功能</p>
<p>[img id="2005" size="large"][/img]</p>
<p>保留默认的 “主机路径和规则”</p>
<p>[img id="2006" size="medium"][/img]</p>
<p>只需要 HTTP 的例子，如果需要 HTTPS，还需要指定一个证书。</p>
<p>[img id="2007" size="medium"][/img]</p>
<p>创建成功后，可以看到如下界面，其中的 IP 地址就是 Anycast IP 了。</p>
<p>[img id="2009" size="large"][/img]</p>
<h3>Nginx 的配置</h3>
<p>这里的配置只是为了方便调试，实际使用不用额外修改 Nginx 的配置。</p>
<p>在这两个主机上都安装 Nginx，然后稍微改动默认的配置文件：增加两个 Header，然后 Reload。</p>
<pre class="lang:ini decode:true">add_header X-Tlo-Hostname $hostname always;
add_header Cache-Control "max-age=36000, public";
</pre>
<h3>检测是否可用</h3>
<p>在测试 Anycast 之前，先测试这两个主机是否存在问题。为了方便阅读，我将 curl 的 IP 地址换为主机名，并省略两个主机都相同的 Header 字段</p>
<pre class="lang:default decode:true">$ curl anycast-us -I
HTTP/1.1 200 OK
…
ETag: "57ef2cb9-264"
X-Tlo-Hostname: <strong>anycast-us</strong>

$ curl anycast-asia -I
HTTP/1.1 200 OK
…
ETag: "57ef2b3b-264"
X-Tlo-Hostname: anycast-asia</pre>
<p>可以看到这两个主机都没有什么问题。然后，我用我的电脑去测试那个 Anycast IP：</p>
<pre class="lang:default decode:true">$ curl anycast-ip -I
HTTP/1.1 200 OK
…
ETag: "57ef2b3b-264"
X-Tlo-Hostname: <strong>anycast-asia</strong>
Accept-Ranges: bytes
Via: 1.1 google</pre>
<p>可以看到，这是 anycast-asia 主机响应的。现在我使用另一个在美国的主机继续测试这个 Anycast IP：</p>
<pre class="lang:default decode:true">$ curl anycast-ip -I
HTTP/1.1 200 OK
…
ETag: "57ef2cb9-264"
X-Tlo-Hostname: <strong>anycast-us</strong>
Accept-Ranges: bytes
Via: 1.1 google</pre>
<p>此时就是 anycast-us 主机响应的，是因为客户端离这个服务器更近。</p>
<p>当通过 Anycast IP 访问时，就可以看到 HTTP Header 中的 <code>Via: 1.1 google</code> 字段。</p>
<h2>速度测试</h2>
<h3>Ping 测试</h3>
<p>Ping 测试发现速度很快，看来反代的操作是放在 Google 的边缘服务器上了。</p>
<p>[img id="2010" size="large"][/img]</p>
<p>中国的速度那更是一流的快，Google 有香港的边缘节点，所以基本上是直接走的香港节点，比原本的连接台湾可用区快不少。</p>
<p>[img id="2011" size="large"][/img]</p>
<h3>HTTP GET 测试</h3>
<p>在开启 CDN 功能之前，负载均衡器是不会对任何内容缓存的，所以会发现 Connect 的速度很快，但是 TTFB 延迟还是有不少。</p>
<p>[img id="2012" size="large"][/img]</p>
<p>可以预测，如果启用了 HTTPS 功能，其 TLS 所需要的等待时间也会很短，TTFB 时间不变，总时长不会延长太多。</p>
<h4>开启 CDN 后进行 HTTP GET 测试</h4>
<p>当将 CDN 开启后，负载均衡器就会自动地对静态资源做缓存了，当缓存命中后会显示 Age 字段，这个字段是表示自缓存命中了，后面的数字代表这是多少秒之前缓存的内容。</p>
<pre class="lang:default decode:true ">curl anycast-ip -I
HTTP/1.1 200 OK
…
Via: 1.1 google
Age: 10</pre>
<p>经过多次执行这个指令，会发现有一定几率 Age 字段消失，这可能是流量指到了同一个地区的不同可用区上。但总之，是缓存命中率不高，即使之前曾访问过了。</p>
<p>[img id="2013" size="large"][/img]</p>
<p>多次运行测试确保有缓存之后，发现速度似乎并没有太多明显的提升。能够明显的看出改善的是：巴黎和阿姆斯特丹的 TTFB 延迟从 200ms 减少到了 100ms，然而还是不尽人意。可能的原因是：Google 并没有将内容缓存到离访客最近的边缘节点上，而是别的节点上。</p>
<h2>总结</h2>
<p>GCE 所能实现的 Anycast 功能，只能通过 HTTP 代理（第七层）的方式实现，所以只能代理 HTTP 请求，其他功能（如 DNS）无法实现。所以很多功能受限于负载均衡器的功能（比如证书和 HTTP2 都需要在负载均衡器上配置），然而由于 TLS 加解密过程是在边缘服务器上实现，而且其本身也带有 CDN 功能，所以会比单纯的 Anycast（比如基于 IP 层，或是 TCP/UDP 层）的更快一些。</p>
<h2>对比</h2>
<h3>Cloudflare</h3>
<p>通过使用 Cloudflare 所提供的服务也能实现 Anycast，也是基于第七层的，即将也能实现 Cross-Region Load Balancing 的功能。虽然它还不能根据主机的 CPU 占用率去调整权重（毕竟它拿不到这些数据），却有强大的 Page Rules 功能以及 WAF 功能。</p>
<p>CloudFlare 并不提供独立 IP 地址，不过这不是什么大问题。</p>
<p>由于它属于第三方服务，不受服务提供商的限制，于是就可以给多种不同的服务提供商去做 Anycast 功能；而且无论服务商是否支持，都能够使用。</p>
<p>连接速度上，GCE 的在中国连接速度有明显的优势。</p>
<h3>BuyVM</h3>
<p>BuyVM 是一家 VPS 提供商，却提供免费的 Anycast 功能，其 Anycast 功能是直接基于 IP 层的 Anycast，所以可以配置 HTTP 之外的各种服务。BuyVM 没有所谓的边缘服务器一说，只能有三个节点，Ping 的结果不像前两家那么快，而且 TLS 过程也是在原本的主机（这三个主机中里用户最近的一个）上进行，也会有一定延迟。</p>
<p>BuyVM 并不提供任何亚洲的主机，所以中国的连接速度也没有比 Cloudflare 快多少，整个亚洲的速度也不是很快。</p>
