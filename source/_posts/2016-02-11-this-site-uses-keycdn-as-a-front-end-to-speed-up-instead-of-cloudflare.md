---
title: 本站使用了 KeyCDN 作为前端加速，代替 CloudFlare
tags:
  - CDN
  - 网站
id: '1153'
categories:
  - - 公告
date: 2016-02-11 15:24:05
---

注：2016 年五月中旬，服务器已经不在 CloudFlare/KeyCDN 上了。 由于之前在 CloudFlare 上感觉起来还是比较慢。要想加速，需要开启 CloudFlare 的 Cache Everything，这样存在很多问题，比如过滤 Cookie，而且 CloudFlare 在中国速度也不佳，于是现在换用了 KeyCDN。 KeyCDN 会缓存页面上所有的内容，包括 HTML 页面。缓存周期为 1 周，自动使用 Let’s Encrypt 的 SSL。在中国有香港节点。我已经在后台配置好，当有以下操作时，清除该页缓存和首页缓存：

*   文章/页面内容更新/发布
*   文章/页面被删除

现在页面的速度，堪称完美！ 你或许也想尝试这样做？现在只需要[安装我的插件并按照说明对其进行配置](https://wordpress.org/plugins/full-site-cache-kc/)即可。 

本文讨论的内容在《[敲开网络世界的大门](https://j.youzan.com/fzAiLY)》中有更详细的介绍。