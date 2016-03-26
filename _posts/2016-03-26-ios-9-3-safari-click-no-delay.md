---
ID: 1464
post_title: iOS 9.3 Safari 点击不再有延迟
author: ZE3kr
post_date: 2016-03-26 18:56:25
post_excerpt: ""
layout: post
permalink: >
  https://www.ze3kr.com/2016/03/ios-9-3-safari-click-no-delay/
published: true
---
iOS 9.3 在这周发布了，有一个很不起眼的改进：Safari 针对适配移动版的网页去掉了点击时的 300ms 延迟。对于普通用户来说，会发现浏览网页的速度似乎变快了。对于网页开发者来说，不再需要引入类似 FastClick 这样的 Hack 了。目前测试来看，双击放大的功能仍然可用，但是必须点击链接之外的地方才有效（否则就直接进入链接了）。早在去年，<a target="_blank" href="https://trac.webkit.org/changeset/191072">WebKit 就移除了这个延迟</a>，在最新的 iOS 9.3 已经对 Safari 的 WebKit 内核做了更新。