---
ID: 1483
post_title: >
  新开发的网页软件：“猜猜我说了什么”
author: ZE3kr
post_date: 2016-04-03 21:17:31
post_excerpt: ""
layout: post
permalink: >
  https://www.ze3kr.com/2016/04/guess-what-i-said/
published: true
dsq_thread_id:
  - "4716439189"
---
最近本人新开发了一个网页软件，中文名叫 “猜猜我说了什么”。它允许你输入一段话，等待完成计算后，分享当前页面给别人，别人就可以猜你刚才输入的内容。别人只能知道是猜对还是猜错，没有介于两者之间的提示。

它是什么原理？其实就是在本地通过某种不可逆的算法对你所输入的内容进行加密，然后将算出来的一段密码放在网址上。由于不可逆，所以几乎是没有可能对其进行解密的，只能通过猜或者是暴力破解（几乎不可能做到）的方法。具体的算法和实现是这样的<!--more-->：

首先，对于加密来说，我没有引用什么第三方库，而是使用了原生的 <a href="http://caniuse.com/#feat=cryptography" target="_blank">Web Cryptography API</a>，可以看出，现在在大多浏览器里都支持了。我使用它生成一个 Key，然后把这个 Key 作为 Salt 对用户所输入的文本进行 SHA-256 的计算，这个计算是重复很多次的，若使用 <a href="https://app.tlo.xyz/Guess-What-I-Said/sha-256-short-v4-zh.html" target="_blank">SHA-256-short-v4</a> 这个算法，就意味着是重复计算 65536 次，这在 Mac 上的 Safari 上通常不到 5 秒就能完成计算。当将其分享出去让别人猜时，别人每猜一次，就要经历一遍相同方式的计算。所以暴力破解几乎是不可能的。为了适应移动端以及其他低端设备，还有 v0 ~ v5 不同的算法，重复计算次数为 16 的 N 次方，比如 v3 就是计算 4096 次。short 的意思代表完成计算后会将算出的 Hash 截取，这样可以让 URL 更加简短。
<h2>这有什么用？</h2>
看不懂上面在讲啥？那直接看这里：
<ol>
	<li>可以用来告诉别人你不想告诉它们的事。比如别人问你一个问题，你不想正面回答，想让它自己猜，那么这是最合适的选择（选择 v0 ~ v5 不同的算法，可以缩短或延长对方每猜一次所用需要时间）。对方猜出来了，那他就知道了，否则这永远是一个谜。</li>
	<li>用来娱乐，比如发在微博里或者微信朋友圈中，让大家一起来猜，一定很有意思。如果有大微发那就更好玩了，全民一起猜（但估计很难知道谁是第一个猜出来的）。</li>
</ol>
多有意思，赶紧来体验吧，选择一个你喜欢的速度即可：
<ul>
	<li><a href="https://app.tlo.xyz/Guess-What-I-Said/sha-256-short-v2-zh.html">SHA-256-short-v2</a> / 中等</li>
	<li><a href="https://app.tlo.xyz/Guess-What-I-Said/sha-256-short-v4-zh.html">SHA-256-short-v4</a> / 慢但是安全</li>
</ul>
微信扫码：

<strong>SHA-256-short-v2</strong> 较慢速

[img id="1493" size="thumbnail"][/img]

<strong>SHA-256-short-v1</strong> 快速

[img id="1491" size="thumbnail"][/img]