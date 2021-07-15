---
title: 修复 WordPress 2017 主题中奇怪字形的 Bug
tags:
  - WordPress
id: '2282'
categories:
  - - 开发
date: 2016-12-17 17:18:28
---

最近国外掀起了一股用 system-font 的热潮（额，好像国内一直都在用系统字体）。最近更新了 WordPress 4.7，并用上了其 2017 主题，这个主题正是使用系统字体。但是在 macOS 上查看发现中文显示的是繁体字形。 实在是太丑了，究其原因，其实是因为新版的主题的 system-font 用的是繁体中文字体：
<!-- more -->

```
html[lang^="zh-"] body,
html[lang^="zh-"] button,
html[lang^="zh-"] input,
html[lang^="zh-"] select,
html[lang^="zh-"] textarea {
	font-family: "PingFang TC", "Helvetica Neue", Helvetica, STHeitiTC-Light, Arial, sans-serif;
}
```


显示效果如下，字形错误明显的已用下划线标出： ![](https://cdn.landcement.com/sites/2/2016/12/Screenshot-2016-12-17-16.46.42-1600x688.png) 看来这是 WordPress 的 Bug，[已经提交并修复](https://core.trac.wordpress.org/changeset/39942)。在官方修复前，可以在外观的 “自定义” 里面添加以下 CSS 解决此问题：

```
html[lang="zh-CN"] body,
html[lang="zh-CN"] button,
html[lang="zh-CN"] input,
html[lang="zh-CN"] select,
html[lang="zh-CN"] textarea {
    font-family: "PingFang SC", "Helvetica Neue", Helvetica, STHeitiSC-Light, Arial, sans-serif
}
```

为什么这次 WordPress 要为每个语言单独指定系统字体呢？因为如果将字体设置为 `-apple-system` 后，在 Safari 下能够正确显示苹方字体，但是在 Chrome 下显示的还是老的字体。但如果特定指明字体种类，就能正确显示了。
