---
layout: post
status: publish
published: true
title: 如何配置以实现纯 IPv6-Only 网络访问
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 1804
wordpress_url: https://ze3kr.com/?p=1804
date: '2016-08-09 20:32:16 -0400'
date_gmt: '2016-08-09 12:32:16 -0400'
categories:
- 开发
tags: []
---
<p>在今年 5 月 4 日，Apple 就开始要求新的应用程序支持 IPv6 DNS64/NAT64 网络，这意味着苹果开始力推 IPv6 网络，在<a href="https://developer.apple.com/library/mac/documentation/NetworkingInternetWeb/Conceptual/NetworkingOverview/UnderstandingandPreparingfortheIPv6Transition/UnderstandingandPreparingfortheIPv6Transition.html#//apple_ref/doc/uid/TP40010220-CH213-SW1" target="_blank">苹果的官网上</a>就有介绍一些 IPv6 的优势，主要来说就是对移动网络更加友好，并能提高一些性能，减少一些传输上的开销。</p>
<p>最近，我也将我的所有服务器全面部署 IPv6，完全支持 IPv6-Only 网络。</p>
<p><!--more--></p>
<h2>什么是 IPv6-Only 网络</h2>
<p>严格上来讲，IPv6-Only 网络下只能连接上 IPv6 地址，这也就意味着 DNS 缓存服务器也必须是 IPv6 地址，只能连接上支持 IPv6 的服务器。如果要解析一个域名，这个域名本身及其所属的根域名的 DNS 服务器也必须统统支持 IPv6。总之，在整个过程中不存在 IPv4。所以想要支持 IPv6-Only 网络，几乎需要在所有的环节下功夫。</p>
<h2>使用支持 IPv6 的根域名和 DNS 服务器</h2>
<p>为了支持 IPv6，首先你所使用的根域名必须支持 IPv6，好在大多数根域名都支持 IPv6 了。<a href="http://bgp.he.net/ipv6-progress-report.cgi" target="_blank">从这里</a>可以看到已经有 98% 的根域名都支持了 IPv6。</p>
<p>一般大家还是喜欢使用第三方的 DNS 而不是去自建，那么这就需要给自己使用支持 IPv6 的 DNS 服务器。好在也有不少 DNS 服务器支持 IPv6 了，比如 CloudFlare、OVH、Vultr、DNSimple、Rage4 等等大多数国外 DNS 解析，哦，除了 Route53；国内目前就见到了百度云加速、sDNS 的 DNS 解析支持，其他常用的 CloudXNS、DNSPod、阿里云等等都不支持。</p>
<p>检测根域名或者是某个域名所使用的 DNS 服务器是否支持 IPv6，只需要执行指令，替换其中的 <code>&lt;domain&gt;</code> 为根域名（如 com）或者任意一级域名（如 example.com）：</p>
<pre class="lang:sh decode:true">$ dig -t AAAA `dig &lt;domain&gt; ns +short` +short</pre>
<p>然后查看输出的 IP 中是否全是 IPv6 地址，如果什么也没有输出，说明 DNS 服务器不支持 IPv6。</p>
<p>正确配置 IPv6 的例子：</p>
<p>[img id="1805" size="large"]根域名 com 和一级域名 example.com 都正确配置了支持 IPv6 的 DNS 服务器。[/img]</p>
<p>如果想要自建 DNS 服务器，可以参考<a href="https://ze3kr.com/2016/08/self-host-dns/">自建 PowerDNS 方法</a>。</p>
<p>如果你的根域名不支持 IPv6，那么你可以联系根域名那里让他们去支持，或者换一个根域名。如果你的一级域名不支持 IPv6，那就联系 DNS 解析商让他们支持，或者直接换走。</p>
<h2>让网站、API 等服务器支持 IPv6</h2>
<h3>方案一，直接配置 IPv6</h3>
<p>也就是让你自己的服务器支持 IPv6，这需要联系你的服务器提供商，让他们给你分配一个 IPv6 地址，如果还是不支持 IPv6，那么可以使用 IPv6 Tunnel Broker，比如 Hurricane Electric 的免费 Tunnel Broker，这样肯定没有服务商给你的原生的 IPv6 好（但也未必），但是在服务商支持原生 IPv6 之前只能先用着。Tunnel Broker <strong>相当于建立在网络层（第三层）上的代理</strong>，需要你的服务器的操作系统支持，而且服务器必须要有一个固定的 IPv4 地址。为了使用它你需要再系统里重新配置网卡（所以共享主机就没戏了），然后就能按照正常方法使用 IPv6 了，简直零成本支持 IPv6。</p>
<p>很可惜，我目前的两个服务器暂时都没有原生的 IPv6 可以用，于是只能用 Tunnel Broker 了，使用后发现虽然是免费的，但是效果也不错：下载时似乎没有限速，我 100M 的独享带宽在原生的 IPv4 上下载速度为 12Mbyte/s，在 IPv6 上还几乎是这个速度。Ping 延迟还是会有一些增加的，主要是因为 Tunnel Broker 的服务器连接到你的服务器会有一些延迟，它相当于一个代理，所以创建时一定要选择离你服务器最近的 Tunnel Broker，而不是里用户最近的 Tunnel Broker。</p>
<p>在服务器支持了 IPv6 后，确保域名上也新设置了 AAAA 记录解析到了 IPv6 地址上。</p>
<h3>方案二，上 CDN/HTTP Proxy</h3>
<p>刚才所介绍的方案是直接支持，或者使用 Tunnel Broker 建立在网络层的代理。当然还有另一种代理的方式，那就是建立在应用层（第七层）上的，建立在第七层上的其实就是 HTTP Proxy，不过大多数提供 HTTP Proxy 功能的地方都能够缓存静态内容，所以也就是 CDN。</p>
<p>最佳解决方案是直接使用免费的 CloudFlare，然后开启 CDN 功能，这需要更换 DNS 服务器，不够这样的话连 DNS 在内的所有服务都能支持 IPv6 了，类似的还有 Akamai，它们在代理了之后都能给你 IPv6 支持。</p>
<p>但是如果使用这种方案，原先收集用户真实 IP 的功能就会失效，包括防火墙和 Web 应用程序在内的配置都需要稍作一些修改，就又能收集访客 IP 了。</p>
<hr />
<p>在做好了这些配置之后，网站就能够被 IPv6-Only 的网络访问了。然而这只是其中一步，别忘了网站上的 CDN 和域名上的邮件服务还没支持 IPv6 哦，最后，我来列一下：</p>
<h2>一些支持 IPv6 的服务列表</h2>
<h3>CDN</h3>
<ul>
<li><a href="https://www.cloudflare.com" target="_blank">CloudFlare</a></li>
<li><a href="https://www.akamai.com" target="_blank">Akamai</a></li>
</ul>
<h3>VPS</h3>
<ul>
<li><a href="https://www.vultr.com/?ref=6886257" target="_blank">Vultr</a></li>
<li><a href="https://www.linode.com" target="_blank">Linode</a></li>
<li><a href="https://www.digitalocean.com" target="_blank">DigitalOcean</a></li>
</ul>
<h3>DNS</h3>
<p>注：CDN、VPS 中列出的那几家服务自己都提供了支持 IPv6 的 DNS，在此不再列出（其中 Linode 和 DigitalOcean 所提供的 DNS 服务实际上也是 CloudFlare 的）</p>
<ul>
<li><a href="https://rage4.com" target="_blank">Rage4</a></li>
<li><a href="https://dns.he.net" target="_blank">Hurricane Electric DNS</a></li>
</ul>
<h3>Tunnel Broker</h3>
<ul>
<li><a href="https://tunnelbroker.net" target="_blank">Hurricane Electric Tunnel Broker</a></li>
</ul>
<h3>邮件服务</h3>
<ul>
<li><a href="https://mail.google.com/" target="_blank">Gmail</a> / <a href="https://apps.google.com/products/gmail/" target="_blank">Gmail for Work</a></li>
</ul>
<hr />
<p>当你配置好后，你可以<a href="http://ipv6-test.com/validate.php">在 IPv6 Test 上测试你的网站</a>。</p>
<p>[img id="1816" size="medium"]测试截图[/img]</p>
<p>直至现在，支持 IPv6-Only 网络访问在生产中仍然不是必须的，因为实际上很少存在 IPv6-Only 的网络，一般都兼容 IPv4，很多大网站也完全不支持 IPv6。苹果所说的要求支持 IPv6-Only，只是程序内部要使用 IPv6 通信，程序中不能有 IPv4 地址，能够在只分配了 IPv6 地址的运营商使用（然而实际上这些运营商还是支持 IPv4 的）。</p>
