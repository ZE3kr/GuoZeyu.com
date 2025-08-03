---
title: 毫米波 5G 有多快? 2000Mbps! 快来看看你的 iPhone 是否支持毫米波
tags:
  - Apple
  - iPhone
categories:
  - 科技
date: 2022-02-20 23:59:59
languages:
  en-US: https://www.ze3kr.com/2022/02/iphone-5g-mmwave/
excerpt: 本文对比了中频 5G 和毫米波 5G 的速度差异，提供了判断 iPhone 是否使用毫米波的方法，讲述了 5G 不同图标的含义，对比了 5G 的低频、中频和毫米波，并列举了 iPhone/iPad 不同型号对毫米波支持的情况。最近分别使用手头上的国行和美版 iPhone 对比了一下中频和毫米波 5G。都是相同运营商 (Verizon Prepaid) 的相同套餐 (Unlimited Plus)，使用实体 SIM 卡，在完全相同的地理位置进行的测试。
---

本文对比了中频 5G 和毫米波 5G 的速度差异，提供了判断 iPhone 是否使用毫米波的方法，讲述了 5G 不同图标的含义，对比了 5G 的低频、中频和毫米波，并列举了 iPhone/iPad 不同型号对毫米波支持的情况。

最近分别使用手头上的国行和美版 iPhone 对比了一下中频和毫米波 5G。都是相同运营商 (Verizon Prepaid) 的相同套餐 (Unlimited Plus)，使用实体 SIM 卡，在完全相同的地理位置进行的测试。

<!-- more -->

<img src="https://cdn.tlo.xyz/images/b55d08fb-5dc8-464a-e9d3-9702299bfc00/extra" alt="毫米波 5G (28 GHz)" width="1125" height="2436"/>

可以看到，毫米波 5G (高频，mmWave) 轻松跑到了 2000Mbps。

<img src="https://cdn.tlo.xyz/images/cce30090-e7d1-497b-0a1a-e62ea576e700/extra" alt="最高也可以跑到 4000Mbps" width="750" height="400"/>

如上图所示，[也有人在 Reddit 上](https://www.reddit.com/r/verizon/comments/sv5eit/fastest_5g_uw_and_4g_lte_speeds_known_to_man/)跑出了接近理论峰值的 4000Mbps。但我试了多次最高 “也就” 2000Mbps。

<img src="https://cdn.tlo.xyz/images/2a7e5416-85e2-4212-1c2d-462ae8a38100/extra" alt="中频 5G (3.7 GHz)" width="1170" height="2532"/>

中频 5G (Mid-Band) “只” 跑到了 929Mbps。

就本次测试看来，毫米波 5G 要比中频 5G 快 2 倍左右。在各自的理想情况下，毫米波 5G 可以比中频 5G 快 2-3 倍。

## 如何判断 iPhone 是否使用毫米波？

在系统的通话页面输入 `*3001#12345#*`，然后点击呼叫。随后我们就可以看到下方视频所示的 Field Test Mode。选择 5G 中的 Nr ConnectionStats。若看不到 5G 相关选项，则说明当前没有 5G 信号，或机型/运营商不支持 5G。然后看 Band 中的数字。该视频中 band 数字为 78，为中频 5G。

<figure class="my-video" style="max-width: 600px; max-height: 600px">
  <div style="position: relative; padding-top: 100%;"><iframe src="https://customer-qz3v4c7e4vfly110.cloudflarestream.com/ce4c0e7c2f71466f9c7b4ee55034e687/iframe?controls=false&muted=true&preload=metadata&loop=true&autoplay=true" style="border: none; position: absolute; top: 0; left: 0; height: 100%; width: 100%;"  allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;" allowfullscreen="true"></iframe></div>
  <figcaption>查询当前使用的 5G 频段</figcaption>
</figure>

若数字小于 200 (如上图)，则说明没有使用毫米波。若显示大于 200 (常见的有 257-262)，则说明已经连接到了毫米波 5G。具体使用的频率可以参考[这个表格](https://en.wikipedia.org/wiki/5G_NR_frequency_bands#Frequency_bands)。

并非所有支持 5G 的 iPhone 都支持毫米波 5G。目前只有在美国购买的 iPhone 12、13、14 系列在美国才能使用毫米波 5G 网络。

## 5G 图标

根据[苹果官网](https://support.apple.com/zh-cn/HT211828)，5G 是有多种图标的。如果只显示 5G，则是连接到了最普通的 5G，速度比较慢。若看到了 5G+、5G UW 和 5G UC，则说明可能连接到了毫米波 5G，速度更快。但实际上，显示 5G+、5G UW 和 5G UC 并不代表使用了毫米波 5G (也可能只是中频 5G)。此外，在美国之外的其他国家即使连接到了中频 5G，也只显示 5G。

<img src="https://cdn.tlo.xyz/images/be05ace1-821c-4dbf-aae0-60d2e0722900/extra" alt="5G 图标一览" width="828" height="918"/>

## 5G 常见的三种频段：低频、中频、毫米波

低频的范围在 1 GHz 以内，中频在 1-6 GHz，毫米波则在 24-40 GHz。

低频 5G，也叫 5G Nationwide (Verizon)、Extended Range 5G (T-Mobile)、5G (AT&T)。是覆盖最广的 5G，但速度也不是很理想，有的时候甚至不如 4G/LTE 的速度。目前现有的很多 4G 基站都可以比较轻松的升级为低频 5G。在我看来只是准 5G 网络。

中频和毫米波 5G，也叫 5G Ultra Wideband (Verizon)、Ultra Capacity 5G (T-Mobile)、5G+ (AT&T)。是真正意义上的 5G 网络。

目前在中国，所有运营商的 5G 均使用中频。相比毫米波，中频在相同的基站数量情况下，覆盖的更广。这是因为中频的波长更长，传播时相比毫米波更不容易被障碍物遮挡。

LTE-Advance，也叫 5G Evolution (AT&T)。是指使用了载波聚合、4x4 MIMO 和 256 QAM 等技术的 4G 网络。这种网络根本不是 5G 网络，只是比较快的 4G 网络。

## iPhone 不同型号支持的 5G 一览

在[苹果官网](https://www.apple.com.cn/iphone/cellular/)搜索你的手机型号（如 A2629、A2634、A2639、A2644 则为中国大陆及港澳地区的 iPhone 13 系列的型号），然后查看是否支持 n257-262 中的任何一个频段。截止到目前，只有在美国销售的 iPhone 12/13/14 支持了毫米波。你可以在机身背面看到型号。目前支持毫米波的型号有：

+ A2650: iPhone 14 Pro
+ A2651: iPhone 14 Pro Max
+ A2649: iPhone 14
+ A2632: iPhone 14 Plus
+ A2483: iPhone 13 Pro
+ A2484: iPhone 13 Pro Max
+ A2482: iPhone 13
+ A2481: iPhone 13 mini
+ A2341: iPhone 12 Pro
+ A2342: iPhone 12 Pro Max
+ A2172: iPhone 12
+ A2176: iPhone 12 mini

还有一个更简单的方法：看 iPhone 右侧是否有毫米波天线的开口（图源 Apple）

<img src="https://cdn.tlo.xyz/images/9d6dc407-9a7a-4a19-775a-607da66bc800/extra" alt="支持毫米波的 iPhone 的毫米波天线位置" width="2636" height="472"/>

<img src="https://cdn.tlo.xyz/images/06a6420e-8aa5-4509-730b-cd02e5fd1900/extra" alt="不支持毫米波的 iPhone 则没有毫米波天线" width="2636" height="472"/>

这个毫米波天线开口很像 iPad 系列 Apple Pencil 的无线充电开口，但他们确实不是一个东西，不要混淆。

其次，如果你手持 iPhone 14，判断方法更为简单，只要没有实体 SIM 卡槽，就是支持毫米波的美版。

## iPad 不同型号支持的 5G 一览

同样的，你可以在[苹果官网](https://www.apple.com.cn/ipad/cellular/networks/)搜索你的 iPad 型号。你可以在机身背面看到型号。目前支持毫米波的型号有：

+ A2379: 12.9 英寸 iPad Pro (第五代)
+ A2301: 11 英寸 iPad Pro (第三代)
