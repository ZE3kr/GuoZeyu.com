---
layout: post
status: publish
published: true
title: 移动优先 &#8211; 起源
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 1000
wordpress_url: http://webdev.tlo.xyz/?p=32
date: '2015-05-16 13:36:43 -0400'
date_gmt: '2015-05-16 05:36:43 -0400'
categories:
- 开发
tags:
- HTML5
- 响应式设计
---
<p>现有的桌面上的操作系统大多都有浏览器，浏览器可以让我们方便的浏览一个网页。</p>
<p>然而最初的手机浏览器访问网页是很困难的，因为它们分辨率不高、屏幕很小而且浏览器不支持众多前沿的 CSS 以及 JavaScript，使它们没有能力访问桌面版网站。那时的手机访问的网站都是专门为设备大幅度简化的版本，就像这样:</p>
<p>[img id="926"][/img]</p>
<p>不过，直到 iPhone 的发布，这一切都得到了很大的改变。因为 iPhone 上的浏览器 —— Safari 是一个真正的且非常前沿的 Web 浏览<!--more-->器，而且它还支持 HTML5 标准。它配备有几乎和电脑一样的浏览器 —— 以至于它支持 CSS3 和 JavaScript。但是这一切，却是在一个宽度仅有 320 个像素的设备上显示。</p>
<p>然而当一个桌面浏览器宽度缩小到 320 个像素时，它就变的大不一样，当他访问一个没有对它优化的网页时，它就会将其缩放（宽度将缩放至 1/3），以百度新闻为例，它将会这样显示:</p>
<p>[img id="1016" size="medium"][/img]</p>
<p>这个页面字很小，但是用户可以通过缩放就能够解决，所以并没有什么问题。然而，百度新闻做了一个专门为 iPhone 的版本，像这样:</p>
<p>[img id="1018" size="medium"][/img]</p>
<p>它能够更好的适应触摸屏，并且没有了字体小的问题，也不需要缩放。但是请要注意，这个页面已经不是刚才的那个页面了，也就意味着百度需要专门重新设计一个为 iPhone 版本的网页，成本是很高的。</p>
<p>然而在 CSS 的帮助下，不需要准备两个网页就可以同时适配电脑和手机，它们加载到的页面相同，但是应用的样式表并不同。例如没有针对移动而优化的 <a href="https://www.wikipedia.org/">Wikipedia 导航页</a>，iPhone 上的 Safari 访问时将会缩放显示，布局就如同电脑访问一样:</p>
<p>[img id="1012" size="medium"][/img]</p>
<p>然而通过增加的 CSS 样式表（当然还包括 <code>head</code> 中的 <code>viewport</code>），这个页面在手机上就会完美的显示了，就像下图:</p>
<p>[img id="1014" size="medium"][/img]</p>
<p>像 Wikipedia 导航页这样先根据电脑布局设计，再新增 CSS 使其支持移动设备的网页，就是桌面优先而不是移动优先。而移动优先则正好相反，页面根据手机而设计，然后再让它适配电脑，电脑与手机也是访问同一个页面，这就属于移动优先。由于移动优先的网站是根据手机设计，自 iPhone 发布之后，很多智能手机也使用了前沿的浏览器，所以移动优先的网站自然会使用 HTML5、CSS3 中的众多新特性，大大缩短了一个网页所需要的开发时间。而且移动优先的网站根据手机的高延迟网络而设计，减少了页面大小，无论身处什么网络环境，加载速度都有很大的提升。移动优先的优点众多，这就是导致其流行起来的原因。</p>
