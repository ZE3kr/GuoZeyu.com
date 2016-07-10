ZE3kr.com
===============
这里是我的个人博客的所有文章的源代码，带历史，每日自动从 WordPress 更新，博客使用 WordPress。

欢迎 Fork、下载、转载，文件使用格式为 HTML。如果你想转载，你可以直接拷贝 `_posts` 文件夹下的文章内容，并前往我的网站**重新下载图片并上传至你自己的服务器上**，并确保你的网站上**能显示作者名并链接到了原始文章**以满足 CC BY-NC-SA 4.0 协议。

主机使用 LNMP 配置，使用 OVH 的 8GB 内存版本，在香港和美国东岸都有服务器。

## 自定义 Shortcode

本网站使用了自定义的 Shortcode，以插件形式实现，转载时请注意要替换这些 Shortcode，[具体代码见此处](https://git.tlo.xyz/ZE3kr/ZE3kr.com/snippets/8)，使用方法如下：

### 图片

优雅的插入图片

用法：`[img id="media id (int)" size="thumbnail/medium/large/full" exif="on/only"]Caption[/img]`

### 链接

优雅的插入链接，包括 Aff 链接，站内链接等。

例子：`[a id="694647259" short="e" type="appstore"][/a]`
