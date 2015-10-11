---
layout: post
title:  "本站使用 KeyCDN 后大陆访问更稳定"
date:   2015-10-11 12:00:00+08:00
image:
  title: unsplash.com/building.jpeg

tag: 
- 服务器升级
- CDN

category: 
- post
- article
---

昨天，本网站开始换用 [KeyCDN](https://www.keycdn.com/?a=7126) 作为 CDN 加速，同时也支持了 HTTP/2 协议，稳定、安全且迅速。

由于此网站没有备案号，所以只能使用外国的服务器，网站使用 OpenShift 部署（即 AWS 服务器），在此之前网站的 HTML 本身并没有使用 CDN。现在由于加了 CDN 之后，网站变得更稳定、更快速了。

## 什么是 CDN

内容分发网络（CDN）是指一种通过互联网互相连接的电脑网络系统，提供高性能、可扩展性、及低成本的网络将内容传递给用户。（摘自维基百科）

## 为何选择 [KeyCDN](https://www.keycdn.com/?a=7126)

首先，我需要 CDN 支持自定义域名、自定义 Header，而且还需要支持自定义域名的 HTTPS；除此之外我还希望这个 CDN 在中国比较稳定。

{% include img.html img="keycdn-logo.png" href="https://www.keycdn.com/?a=7126" %}

KeyCDN 不仅满足了上述所有的要求，还提供了 HTTP/2 支持、强大的统计功能等。

{% include img-small.html title="HTTP/2 测试" img="2015-10-11-14.12.02.png" %}

## 解决缓存问题

之所以 CDN 能够提速，是因为 CDN 会自动缓存文件。每一个 CDN 节点相当于一个代理服务器，在文件被访问到时从原始服务器下载下来，同时也会缓存文件的内容。下次访问时，CDN 节点就不需要再从远程服务器下载文件，直接从缓存的地方返回给你。由于 CDN 节点离你的物理位置相对原始服务器更近，带宽也能有保证，所以延迟更小，速度更快。

但是当网站更新后，需要等待缓存失效，不然用户访问到的内容不是最新的。缓存是有有效期的，有效期到了之后缓存才会失效。不过还有一种更直接的方式，就是通知 CDN 提供商去清楚缓存，我使用了 [KeyCDN](https://www.keycdn.com/?a=7126) 的 API，当网站更新后，自动通知它删除所有缓存，这样基本上 1 分钟内所有人访问网站都是最新的了。

## 成果

现在页面加载的首屏之间可以控制在 3 秒以内，在中国大陆的良好网络环境下，访问 HTTPS 站点也能实现秒开。（网站的 HTML 本身使用 [KeyCDN](https://www.keycdn.com/?a=7126) 加速，其余的 CSS、JavaScript 以及图像使用 UPYUN 加速，速度还是可以的。顺便说一下 UPYUN 是支持 SPDY 的。）

{% include img-small.html title="瀑布视图" img="2015-10-11-15.01.28.png" %}
