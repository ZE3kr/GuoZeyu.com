---
ID: 158
post_title: >
  GitHub 实时同步到
  GitCafe，并解决百度无法抓取
  GitHub Pages 问题
author: ZE3kr
post_date: 2015-08-07 22:28:37
post_excerpt: ""
layout: post
permalink: >
  https://ze3kr.com/2015/08/github-sync-to-gitcafe/
published: true
dsq_thread_id:
  - "4079471239"
---
本文将提供一个把 GitHub 自动且实时地同步到 GitCafe 方案。

如果你是 GitHub Pages 用户，你需要进行如下操作：

1. 首先，需要给 GitHub 项目改个名，如果你原先是 `[用户名].github.io` 形式的话，请改成别的，改完后把 master 分支改名为 gh-pages。
2. 然后在 GitCafe 上创建一个存放处，存放处名称需要是你的用户名，设置好自定义域名等等。
3. 待完成“步骤”后，修改域名解析。

<!--more-->
## 步骤
为了实现同步需要一个虚拟主机，本文讲解使用免费的 OpenShift 的方法。

进入 [OpenShift](https://www.openshift.com) 注册账号。

注册成功后再创建一个程序，由于我不特别会后端，索性选择 PHP5.4。

然后在本地安装上 OpenShift 的 `rhc`：
<pre class="lang:sh decode:true ">~ $ gem install rhc
</pre>
配置 <code>rhc</code>，需要输入刚注册时用户名、密码：
<pre class="lang:sh decode:true ">~ $ rhc setup
</pre>
然后进行 SSH（这里仅是用来示范，请自行替换 <code>&lt;app&gt;</code> 部分）：
<pre class="lang:sh decode:true ">~ $ rhc ssh 
</pre>
进入 SSH 后，把 GitHub 仓库 Clone 下来，官方推荐大家把文件存储在$OPENSHIFT_DATA_DIR里，所以就存在这里好了（这里仅是用来示范，请自行替换 <code>ZE3kr/ZE3kr.tlo.xyz</code> 部分）：
<pre class="lang:sh decode:true ">~ $ cd $OPENSHIFT_DATA_DIR
data $ git clone https://github.com/ZE3kr/ZE3kr.tlo.xyz.git
</pre>
Clone 完毕后，修改一下这个文件（这里仅是用来示范，请自行替换 <code>ZE3kr.tlo.xyz</code> 部分）：
<pre class="lang:sh decode:true ">~ $ vim $OPENSHIFT_DATA_DIR/ZE3kr.tlo.xyz/.git/config
</pre>
修改成如下格式，将原本的GitHub前添加上用户名和密码（我是直接把 Password 打在这里了，如果你觉得不安全请使用 SSH 公私钥那种方式），再添加一行是你的 GitCafe 的 HTTPS 地址，也要添加上用户名和密码，格式如下：

[img size="medium" id="833"][/img]

然后，执行下面这个指令，完成第一次同步：
<pre class="lang:sh decode:true ">~ $ cd $OPENSHIFT_DATA_DIR/ZE3kr.tlo.xyz
ZE3kr.tlo.xyz $ git pull
ZE3kr.tlo.xyz $ git push
</pre>
以后的同步通过 GitHub Webhooks 实现，Webhooks 能够在你每次 Push 到 GitHub 时，给你的服务器发送一个 Push 请求。你是不是已经想到该怎么做了？

创建一个下面的程序，让外部可以进行 HTTPS 的请求，然后自动同步。
<pre class="lang:php decode:true ">&lt;?php
if( $_GET['key'] == 'KEY' ) {
    echo shell_exec('cd $OPENSHIFT_DATA_DIR/ZE3kr.tlo.xyz;git fetch origin;git pull;git push');
}
else {
    header('HTTP/1.1 400 Bad Request');
    echo &lt;&lt;&lt;HTML
// Fallback
HTML;
}
</pre>
首先进入你的 GitHub 项目中的 Webhooks（Settings &gt; Webhooks &amp; Services &gt; Add webhook），然后填写内容。Secret可以不填写，为了方便起见只好用一种不安全的方式——加一个 GET 请求。Payload URL 里就填写刚才添加的 Openshift 程序的网址＋新创建的实现自动同步的文件目录＋GET 请求。

[img size="medium" id="832"][/img]
<a href="https://media.landcement.com/sites/2/20160131134448/sync2.png" rel="attachment wp-att-832"><img src="https://media.landcement.com/sites/2/20160131134448/sync2-450x319.png" alt="sync2" width="450" height="319" class="aligncenter size-medium wp-image-832" /></a>

等到下次 Push 后，进这里查看结果，查看是否成功。

同样的，如果你想当 GitCafe 被 Push 后也同步到 GitHub，也只需要在 GitCafe 上配置好相同的 Webhooks。

[img size="medium" id="831"][/img]