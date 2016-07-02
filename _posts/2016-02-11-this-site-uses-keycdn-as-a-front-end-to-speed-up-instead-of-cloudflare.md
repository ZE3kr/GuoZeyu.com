---
layout: post
status: publish
published: true
title: 本站使用了 KeyCDN 作为前端加速，代替 CloudFlare
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 1153
wordpress_url: https://www.ze3kr.com/?p=1153
date: '2016-02-11 15:24:05 +0000'
date_gmt: '2016-02-11 07:24:05 +0000'
categories:
- 公告
tags:
- 网站
---
<p>注：2016 年五月中旬，服务器已经不在 CloudFlare/KeyCDN 上了。</p>
<p>由于之前在 CloudFlare 上感觉起来还是比较慢。要想加速，需要开启 CloudFlare 的 Cache Everything，这样存在很多问题，比如过滤 Cookie，而且 CloudFlare 在中国速度也不佳，于是现在换用了 KeyCDN。</p>
<p>KeyCDN 会缓存页面上所有的内容，包括 HTML 页面。缓存周期为 1 周，自动使用 Let’s Encrypt 的 SSL。在中国有香港节点。我已经在后台配置好，当有以下操作时，清除该页缓存和首页缓存：</p>
<ul>
<li>文章/页面内容更新/发布</li>
<li>文章/页面被删除</li>
</ul>
<p>现在页面的速度，堪称完美！</p>
<p>你或许也想尝试这样做？现在只需要[a href="https://wordpress.org/plugins/full-site-cache-kc/"]安装我的插件并按照说明对其进行配置[/a]即可。</p>
