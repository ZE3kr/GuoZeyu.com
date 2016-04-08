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
  - 'a:1:{i:0;s:0:"";}'
---
<h2>视频格式</h2>
此网站使用自己的 CDN 加速视频，使用 MP4 格式，从 2016 年开始，所有的视频还将有 WEBM 格式，并且我还会逐渐的让以前的视频也支持 WEBM。
<h2>视频质量</h2>
使用 qHD（540p）准高清，腾讯视频将其称作为超清。同时使用 MP4 封装的 H.264 + AAC 和 WEBM 封装的 VP9 + Vorbis，每个视频的每种格式使用近似相等的码率，通常后者画质更高一些。本站对于 30 帧的视频使用 1000kbps 的平均码率，60 帧的视频使用 1500kbps 的平均码率；音频均为单声道 64kbps 码率。通常本站的视频（无论是什么格式）在 720p 或更低分辨率下的屏幕上都显得十分清晰（例如 iPhone 6，这只是主观测试）。更高分辨率下的屏幕下若使用 MP4 格式，能看到明显的画质下降的痕迹，WEBM 有明显优势。
<h3>MP4 压缩方式</h3>
<ul>
	<li>视频：H.264 多通路，平均码率 1000k（对于 60 帧的，使用 1500k），540p 画质，降分辨率使用线性滤镜，多次通过</li>
	<li>音频：AAC 单声道，平均码率 64k</li>
</ul>
<h3>WEBM 压缩方式</h3>
<ul>
	<li>视频：libvpx-VP9，平均码率 1000k（对于 60 帧的，使用 1500k），540p 画质，两次通过</li>
	<li>音频：Vorbis 单声道，平均码率 64k</li>
</ul>
<h2>压制参数</h2>
<h3>30帧 MP4</h3>
<pre class="lang:sh decode:true ">ffmpeg -y -i input -c:v libx264 -r 30000/1001 -c:a aac -preset veryslow -s 960x540 -b:v 1000k -pass 1 -b:a 64k -ac 1 -f mp4 /dev/null

ffmpeg -y -i input -c:v libx264 -r 30000/1001 -c:a aac -preset veryslow -s 960x540 -b:v 1000k -pass 2 -b:a 64k -ac 1 output.mp4</pre>
<h3>60帧 MP4</h3>
<pre class="lang:sh decode:true ">ffmpeg -y -i input -c:v libx264 -r 60000/1001 -c:a aac -preset veryslow -s 960x540 -b:v 1500k -pass 1 -b:a 64k -ac 1 -f mp4 /dev/null

ffmpeg -y -i input -c:v libx264 -r 60000/1001 -c:a aac -preset veryslow -s 960x540 -b:v 1500k -pass 2 -b:a 64k -ac 1 output.mp4</pre>
<h3>30帧 WEBM</h3>
<pre class="lang:sh decode:true ">ffmpeg -y -i &lt;source&gt; -c:v libvpx-vp9 -pass 1 -b:v 1000K -threads 8 -speed 4 -tile-columns 6 -frame-parallel 1 -b:a 64k -ac 1 -s 960x540 -g 150 -r 30000/1001 -an -f webm /dev/null

ffmpeg -y -i &lt;source&gt; -c:v libvpx-vp9 -pass 2 -b:v 1000K -threads 8 -speed 1 -tile-columns 6 -frame-parallel 1 -auto-alt-ref 1 -lag-in-frames 25 -ac 1 -s 960x540 -g 150 -r 30000/1001 -c:a libopus -b:a 64k -f webm out.webm</pre>
<h3>60帧 WEBM</h3>
<pre class="lang:sh decode:true ">ffmpeg -y -i &lt;source&gt; -c:v libvpx-vp9 -pass 1 -b:v 1500K -threads 8 -speed 4 -tile-columns 6 -frame-parallel 1 -b:a 64k -ac 1 -s 960x540 -g 250 -r 60000/1001 -an -f webm /dev/null

ffmpeg -y -i &lt;source&gt; -c:v libvpx-vp9 -pass 2 -b:v 1500K -threads 8 -speed 1 -tile-columns 6 -frame-parallel 1 -auto-alt-ref 1 -lag-in-frames 25 -ac 1 -s 960x540 -g 250 -r 60000/1001 -c:a libopus -b:a 64k -f webm out.webm</pre>