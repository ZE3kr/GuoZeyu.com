---
title: Cloudflare 的全新功能体验——Load Balancing、Rate Limiting
tags:
  - CDN
id: '2031'
categories:
  - - 开发
date: 2016-10-04 09:26:00
cover: <img src="https://cdn.tlo.xyz/6T-behmofKYLsxlrK0l_MQ/643d7c6c-859b-4f5f-42e3-44bd338c1101/extra" alt="Load Balancing 截图" width="1600" height="877"/>
---

Cloudflare 在 2016 年末终于增加了两个重磅的功能，分别是：

*   Load Balancing（原名：Traffic Manager）
*   Rate Limiting（原名：Traffic Control）

Load Balancing 支持更加高级的负载均衡功能，并终于支持了大家很需要的跨区域负载均衡功能；Rate Limiting 支持了高级的访问次数限制功能。越来越多的原本需要在服务器上配置的功能，现在在 Cloudflare 上也能进行配置了。（目前这两个功能还属于 Beta 阶段，需要认证用户才能使用）
<!-- more -->

## Load Balancing

此功能可以把 Cloudflare 当作负载均衡器使用，而不需要再在主机提供商上配置负载均衡，[官网介绍](https://www.cloudflare.com/load-balancing/)。目前此功能仅向 Enterprise 账户和部分有资格的用户开放。该功能有两种实现方式，分别如下：

### 通过 Cloudflare 的 CDN 实现（Anycast）

负载均衡的功能是在 Cloudflare 的边缘服务器上实现，是通过第 7 层反代的方式实现，其实很类似于原本的 CDN 功能，不过回源可以高度定制。源站可以配置多个地区（需手动设置服务器的位置），每个地区也可以配置多个服务器，可以将这些众多服务器设置为一个 Group。将域名指向这个 Group，然后 Cloudflare 的边缘服务器的回源可以根据服务器的地区来**自动**选择最近的源站服务器。这样可以非常有效的降低首字节的延迟，对动态资源速度的提升会有很大的帮助。

<img src="https://cdn.tlo.xyz/6T-behmofKYLsxlrK0l_MQ/b61a7a83-cbf0-401c-495c-2f340bfb9201/extra" alt="可配置服务器的地区 (两种方式一样)" width="1008" height="736"/>

此外，Cloudflare 还自带了 Health Check 功能，可以当服务器宕机后能够自动更改回源。虽然通过 DNS 的方式也可以实现宕机后切换，但是 DNS 方式毕竟会收到缓存时长影响，若使用 CDN 切换，则可以实现秒级切换。 我的一个 WordPress 站点 tlo.xyz 就使用了这个功能，默认是美国东部和亚洲东部跨区域负载均衡，两者有一者宕机自动切换。如果全部宕机，则 fallback 到 Google Cloud Storage 上的静态页面。你可以观察 https://tlo.xyz 上的 TLO-Hostname 的 Header 来判断是哪一个服务器做的响应。 

<img src="https://cdn.tlo.xyz/6T-behmofKYLsxlrK0l_MQ/643d7c6c-859b-4f5f-42e3-44bd338c1101/extra" alt="Load Balancing 截图" width="1600" height="877"/>

### 通过 DNS 实现（GeoDNS + 权重）

使用此功能不需要开启它的 CDN 功能，故访客是直接连接源站的。同上一种方式也是配置一个 Group，只是不开启 CDN 功能，然后 Cloudflare 会只作为 DNS 服务器的功能。它会自动进行 GeoDNS，给访客返回最近的服务器的 IP 地址，同样也支持 Health Check 功能，当服务器宕机后会自动切换解析。 不同于其他的 DNS 解析商，Cloudflare 真正做到了智能，只需要配置一个 Group 即可，剩下的 Cloudflare 会自动搞定，而不是去手动地选择哪一个地区解析到哪一个服务器。 此功能已经被我测试，目前的测试期间，GeoDNS 的定位功能是根据请求最终抵达的 CloudFlare 的服务器来决定的，也就是说是依靠 Anycast 系统来决定的。然而中国用户绝大多数会被运营商定向到 CloudFlare 的美国西部服务器，于是就会被 GeoDNS 系统解析道美国西部位置所对应的结果。所以此功能还是十分有限，不适合国内使用。

## Rate Limiting

Cloudflare 终于可以限制 IP 的请求速率，此功能能够相当有效的过滤 CC 攻击，而且对于普通访客几乎没有影响（以前只能通过 I'm under attack 功能实现，然而这个功能会让所有用户等 5 秒才能载入）。它可以根据不同的路径配置不同的请求速率，能够实现防止暴力破解密码、防止 API 接口滥用等功能。 
