---
ID: 1248
post_title: 本站将新增 WEBM 视频格式支持
author: ZE3kr
post_date: 2016-02-17 09:01:41
post_excerpt: ""
layout: post
permalink: >
  https://www.ze3kr.com/2016/02/16-the-new-webm-video-format-support/
published: true
dsq_needs_sync:
  - "1"
---
为了给大家带来更好的体验，以及跟进时代的步伐，终于在 2016 年打算做对 WEBM 视频格式的支持（并且我还会逐渐的让以前的视频也支持 WEBM），也就是说现在同时支持 MP4 和 WEBM。为什么对这个支持来的这么晚？因为 WEBM 视频格式目前还存在很多问题，尤其是在编码速度上，虽然目前也存在这个问题，但是我忍了。
## 为什么用 WEBM?
我只听说过 MP4，WEBM 是什么鬼，好用吗？

WEBM 是一个完全开源且自由免费的视频编码解决方案，由 Google 推出，主要用于网络视频。目前 YouTube 已经在生产环境中部署了 WEBM。关于 WEBM 的质量，我可以跟你说：不比 MP4 差，甚至超越了 MP4（MP4 包含各种专利等，WEBM 能做到这样已经非常棒了）。

要用就用最新的，本站直接使用 VP9 作为视频编码，分辨率和码率与 MP4 完全一样～
#### 附：压制参数
<pre class="lang:sh decode:true ">ffmpeg -i &lt;source&gt; -c:v libvpx-vp9 -pass 1 -b:v 1000K -threads 8 -speed 4 \
  -tile-columns 6 -frame-parallel 1 -b:a 64k\
  -ac 1 -s 960x540 -g 150 -r 30000/1001\
  -an -f webm /dev/null

ffmpeg -i &lt;source&gt; -c:v libvpx-vp9 -pass 2 -b:v 1000K -threads 8 -speed 1 \
  -tile-columns 6 -frame-parallel 1 -auto-alt-ref 1 -lag-in-frames 25 \
  -ac 1 -s 960x540 -g 150 -r 30000/1001\
  -c:a libopus -b:a 64k -f webm out.webm</pre>