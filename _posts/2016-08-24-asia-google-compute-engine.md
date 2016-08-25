---
layout: post
status: publish
published: true
title: 亚洲主机换用 Google Compute Engine
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 1933
wordpress_url: https://ze3kr.com/?p=1933
date: '2016-08-24 15:50:48 -0400'
date_gmt: '2016-08-24 07:50:48 -0400'
categories:
- 短文
tags: []
---
<p>之前一直用 SunnyVision 的香港服务器，但是毕竟带宽还是太小了，想要在服务器上更新个软件光下载就需要很长时间，容易被 DDOS，然后直接 null route 24小时，虽然我做一个宕机自动切换，但还是有些麻烦。而且 SunnyVision 只是连中国速度很快，亚洲其他地区以及美洲欧洲地区 ping 值都很高，而且丢包也比较严重。于是直接上 Google Compute Engine 亚洲区，中国至少不绕道，不限带宽。使用起来类似 AWS 的 EC2 但是却更方便，价格也比 AWS 低不少。所以目前我就用 AWS 的 S3 存储、 OVH 的 VPS 当主要服务器、Google Compute Engine 作为亚洲加速，目前 Google 的 Anycast 功能还是有点小贵，不然我就可以部署 Anycast 了。</p>
