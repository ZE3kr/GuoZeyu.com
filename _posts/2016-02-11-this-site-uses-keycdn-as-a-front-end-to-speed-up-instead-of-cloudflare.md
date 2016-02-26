---
ID: 1153
post_title: >
  本站使用了 KeyCDN
  作为前端加速，代替 CloudFlare
author: ZE3kr
post_date: 2016-02-11 15:24:05
post_excerpt: ""
layout: post
permalink: >
  https://www.ze3kr.com/2016/02/this-site-uses-keycdn-as-a-front-end-to-speed-up-instead-of-cloudflare/
published: true
dsq_thread_id:
  - "4569615456"
---
由于之前在 CloudFlare 上感觉起来还是比较慢。要想加速，需要开启 CloudFlare 的 Cache Everything，这样存在很多问题，比如过滤 Cookie，而且 CloudFlare 在中国速度也不佳，于是现在换用了 KeyCDN。

KeyCDN 会缓存页面上所有的内容，包括 HTML 页面。缓存周期为 1 周，自动使用 Let’s Encrypt 的 SSL。在中国有香港节点。我已经在后台配置好，当有以下操作时，清除该页缓存和首页缓存：

+ 文章/页面内容更新/发布
+ 文章/页面被删除

现在页面的速度，堪称完美！

你或许也想尝试这样做？现在只需要[a href="https://wordpress.org/plugins/full-site-cache-kc/"]安装我的插件并按照说明对其进行配置[/a]即可。