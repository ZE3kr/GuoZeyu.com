---
layout: post
title:  "HTTPS 时代即将到来"
date:   2015-06-28 8:00:00+08:00
image: 
  title: unsplash.com/type-away-numero-dos-2.jpg

tag: 
- HTTPS
- 网站

category: article
---

最近，苹果公司向开发者发布了 iOS 9 的测试版，这个系统将在几个月后和大众界面。除了带来了许多新的功能之外，还提升了整个系统安全性。

> 如果你正在开发一个新的程序，你仅应该使用 HTTPS。如果你已经有一个程序，你现在就应该尽可能多的使用 HTTPS，并准备好对剩下部分迁移的计划。另外，如果你的程序使用更高层级的 API 进行通信，则需要使用 TLS 1.2 或以上的版本。如果你试图建立一个不遵守这些需求的通信，就会引发错误。（If you’re developing a new app, you should use HTTPS exclusively. If you have an existing app, you should use HTTPS as much as you can right now, and create a plan for migrating the rest of your app as soon as possible. In addition, your communication through higher-level APIs needs to be encrypted using TLS version 1.2 with forward secrecy. If you try to make a connection that doesn't follow this requirement, an error is thrown.）<br><cite>[iOS 开发者资源](https://developer.apple.com/library/prerelease/ios/releasenotes/General/WhatsNewIniOS/Articles/iOS9.html){:target="_blank"}</cite>

即使现在已有的程序在 iOS 9 中仍可以在非 HTTPS 情况下工作。但是相信在不久的将来，所有程序都会使用 HTTPS，而且 HTTP 将会完全淘汰。

那么为什么要使用 HTTPS？那些情况下要使用HTTPS呢？

## 使用 HTTPS 原因

HTTPS 能够加密数据传输，防止中间人截取或是修改。能够实现加密用户信息和网站内容。

比如使用大众所说的 “不安全的免费 Wi-Fi”，如果用户访问的网页全部是 HTTPS 的，那么这个 Wi-Fi 对用户没有任何影响。也就是说，媒体报道的 “免费 Wi-Fi 不安全” 纯属造谣，没有任何道理。当启用了 HTTPS 和 HSTS 后，免费 Wi-Fi 完全不能截获到用户密码等任何信息，用户可以安心的进行付款等操作。显然央视 315 没有任何专业知识及解释就在骗大家 “免费 Wi-Fi 不安全”，完全就是恐吓观众。之所以微信朋友圈所有照片都能被获取，是因为**微信朋友圈的上传是明文的**，这分明是微信自己的问题，显然并不是所有的软件都存在这样的问题。随着 iOS 9 的发布以及强制 HTTPS 措施，这一类问题将不复存在了。

其次，使用 HTTPS 不仅仅是为了防止信息被盗窃，还可以防止信息被中途修改。比如中国联通和中国移动会修改网站内容，投放自己的广告让用户升级产品，而这些广告并不是网站主准备的，网站主事先也不知道。虽然它们这样做就是没有行业道德底线，但是我们仅需要使用 HTTPS，这些运营商就统统无能为力了。

包括小米路由器的 “404错误页面优化” 也是利用了同样的原理，对非 HTTPS 页面进行篡改，给用户提供自己的广告从而谋取利益。其本身就是**劫持**，**绝没有夸大之言**。除此之外，有的用户还发现就算是正常页面，也有小米通过**劫持**网页**代码注入**而加入的广告信息。但当 HTTPS 普及之后，这一切都会无影无踪。然而在 HTTPS 普及之前，一些不支持 HTTPS 的网站主只能忍受被运营商、路由器的劫持了。

## 使用 HTTPS 的地方

我认为，**所有的网页以及程序**都有必要**全部且强制的使用 HTTPS**，可以避免上述情况的发生。包括个人网站在内，也应该全面启用 HTTPS，防止因为被篡改植入的广告而流失读者。

使用 HTTPS 并不会增加太多的成本，还可以让页面速度变得更快。SPDY 协议可以最小化网络延迟，提升网络速度，优化用户的网络使用体验，然而 SPDY 协议只支持 HTTPS。

随着现在的趋势，越来越多的站长会主动或被迫的使用 HTTPS，HTTPS 即将成为主流。中国是 HTTPS 普及程度最小的国家，但是随着百度全站 HTTPS 以及 UPYUN 支持自定义域名的 HTTPS，将推动整个行业 HTTPS 的发展。