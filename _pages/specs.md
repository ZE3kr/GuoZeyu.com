---
ID: 578
post_title: 技术参数
author: ZE3kr
post_date: 2016-01-03 13:33:56
post_excerpt: ""
layout: page
permalink: https://www.ze3kr.com/specs/
published: true
dsq_thread_id:
  - ""
---
<h2>视频格式</h2>
此网站使用自己的 CDN 加速视频，使用 MP4 格式，从 2016 年开始，所有的视频还将有 WEBM 格式。

<h3>MP4 压缩方式</h3>
视频：H.264 多通路，平均码率 1000k，540p 画质，降分辨率使用线性滤镜
音频：AAC 单声道，平均码率 64k

<h3>WEBM 压缩方式</h3>
视频：libvpx-VP9，平均码率 1000k，540p 画质
音频：Vorbis 单声道，平均码率 64k

<h4>压制参数</h4>

<pre class="lang:sh decode:true " >ffmpeg -i &lt;source&gt; -c:v libvpx-vp9 -pass 1 -b:v 1000K -threads 8 -speed 4 \
  -tile-columns 6 -frame-parallel 1 -b:a 64k\
  -ac 1 -s 960x540 -g 150 -r 30000/1001\
  -an -f webm /dev/null

ffmpeg -i &lt;source&gt; -c:v libvpx-vp9 -pass 2 -b:v 1000K -threads 8 -speed 1 \
  -tile-columns 6 -frame-parallel 1 -auto-alt-ref 1 -lag-in-frames 25 \
  -ac 1 -s 960x540 -g 150 -r 30000/1001\
  -c:a libopus -b:a 64k -f webm out.webm</pre>