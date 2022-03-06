---
title: 在自己服务器上安装 GitLab，代替 GitHub！
tags:
  - Git
  - VPS
  - 安全
  - 效率
  - 网站
  - 软件
id: '1731'
categories:
  - - 开发
date: 2016-06-25 21:12:08
languages:
  en-US: https://ze3kr.com/2016/06/use-gitlab-on-own-server/
---

我的服务器上部署的代码、配置文件等内容大多是使用 Git 进行版本控制。为了能够使用、配置起来更方便，通常使用一整套系统去管理。很显然，在一些代码和配置文件里会有一些机密的内容，如一些密钥什么的，所以必须不能公开。GitHub.com 虽然提供了 Private 存放处功能，但是由于此功能是付费的，而且对于 Organization 的 Plan 还是极贵，并不十分划算；就算能有免费的 Private 存放处，把自己的很多重要的密钥放在第三方服务器上还是很不安全，所以能够 Host 在自己的主机上的，并且能够替代 GitHub.com 的软件/服务就是不错的选择。 本文将讲一下我在自己服务器上安装 GitLab 遇到的坑，进阶使用，包括使用 `.gitlab-ci.yml` 文件实现自动 Build，实时同步镜像到 GitHub。
<!-- more -->

能够 Host 在自己的服务器上的软件/服务其实有很多，比如 GitHub Enterprise，Bitbucket Server。不过再此还是推荐完全开源、免费、由社区维护的 GitLab Community Edition，没有任何限制，只是相比 Enterprise Edition 少了些本来也用不着的功能。

## 安装及遇到的坑

具体安装方法[见文档](https://about.gitlab.com/downloads/)，目前官方推荐的系统环境是 Ubuntu 16.04 LTS，安装起来非常简便，整个 Web 环境都会配置好。安装后的更多配置请[参见文档](http://docs.gitlab.com/omnibus/)。如果你的主机上跑了不只一个 Web 程序，那就需要对现有的 Web 软件做修改，需要参见官方的 Nginx 的配置文档。我的代码中使用了 `sub_filter` 来实现替换默认的标题，实现更好的 SEO，更加品牌化。 然后为了能达到更好的使用效果，还应该配置 SMTP 发件服务器，我使用的是 AWS SES；然后还需要一个支持 IMAP 的收件服务器实现 Reply by email，我使用的是 Gmail，收邮件的限制总比发邮件的限制少吧～这些的具体设置方法官方文档里都有。 安装后默认是允许注册的，如果你不想让外人注册，你需要直接去 Web 后台禁用。如果你想要开放注册，那么最好先想好新注册用户能干什么，比如和我一样：只允许新用户创建 Issues 和 Snippets，那就在 Web 后台将 Default projects limit 设置为 `0`，然后编辑后台的配置文件，禁止新用户创建 Group。同时建议在 Web 后台启用 reCAPTCHA 和 Akismet，防止恶意注册和恶意发 Issues。既然允许注册，那么也建议[使用 OmniAuth](https://gitlab.com/gitlab-org/gitlab-ce/blob/master/doc/integration/omniauth.md) 来支持第三方 OAuth 的方式登陆。

## GitLab Runner

[GitLab Runner](https://gitlab.com/gitlab-org/gitlab-ci-multi-runner) 十分强大，但是并不是内置的，它可以极其方便的实现自动部署等非常有用的功能。安装配置好 Runner 后，在项目根目录下添加一个名为 `.gitlab-ci.yml` 的文件，以 master 分支为例，为了实现每次 commit 到 master 都将文件部署到 `/var/gitlab/myapp` ，那么文件内容应该是这样的：

```
pages:
stage: deploy
script:
- mkdir -p /var/gitlab/myapp
- git --work-tree=/var/gitlab/myapp checkout -f
only:
- master
```

注意，你需要先创建 `/var/gitlab` 文件夹，并设置这个文件夹的用户组为 `gitlab-runner:gitlab-runner`

```
$ sudo chown -R gitlab-runner:gitlab-runner /var/gitlab
```

`.gitlab-ci.yml` 核心的部分就是 `script:` ，这里的脚本都是由用户 `gitlab-runner` 执行的，你可以根据需要修改，后文中也给了几种范例。 然后 commit，去设置页面里里激活这个项目的 Runner。建议在设置里设置 Builds 为 `git clone` 而不是 `git fetch` ，因为后者常常出现奇奇怪怪的问题，前者的速度瓶颈主要在于网络传输。

### 部署 Runner 在同一个主机上，Or not？

官方的文档里强烈不推荐把 Runner 部署在同一个主机上，其实这种说法并不正确。官方不推荐这样做是因为一些 build 会花费很长时间，占用很多的 CPU 和内存资源。但是如果你执行的 build 脚本并不会这样，那么安装在同一个主机上也未尝不可。

### 常见的部署范例

这几种部署是我比较常用的，大家可以当作范例，具体根据自己的需要弄各种不同的部署。 以下几种 Web 的部署方式所消耗的系统资源都不多，而且由于使用了 `nice` ，并不会阻塞其他任务，可以部署在同一台主机上。

#### Jekyll

修改之前那个 `.gitlab-ci.yml` 文件的 `git checkout` 一行，替换为：

```
jekyll build --incremental -d /var/gitlab/myapp
```

#### 检查 PHP 的编译错误

也是添加以下代码到 `.gitlab-ci.yml` 即可自动检查所有 PHP 文件的编译错误，编译通过的文件不会显示，只会显示编译错误的：

```
if find . -type f -name "*.php" -exec nice php -l {} \;  grep -v "No syntax errors"; then false; else echo "No syntax errors"; fi
```

#### 自动与 GitHub 同步

以下过程需要 root 权限登陆到主机，或者在每行命令前添加 `sudo`。 首先，需要先给 `gitlab-runner` 用户一个单独的 SSH Key：

```
$ ssh-keygen -f /home/gitlab-runner/.ssh/id_rsa
```

然后，创建 `/home/gitlab-runner/.ssh/known_hosts` ，内容是：

```
github.com ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEAq2A7hRGmdnm9tUDbO9IDSwBK6TbQa+PXYPCPy6rbTrTtw7PHkccKrpp0yVhp5HdEIcKr6pLlVDBfOLX9QUsyCOV0wzfjIJNlGEYsdlLJizHhbn2mUjvSAHQqZETYP81eFzLQNnPHt4EVVUh7VfDESU84KezmD5QlWpXLmvU31/yMf+Se8xhHTvKSCZIFImWwoG6mbUoWf9nzpIoaSjB+weqqUUmpaaasXVal72J+UX2B+2RPW3RcT0eOzQgqlJL3RKrTJvdsjE3JEAvGq3lGHSZXy28G3skua2SmVi/w4yCE6gbODqnTWlg7+wC604ydGXA8VJiS5ap43JXiUFFAaQ==
```

之后，获取 `/home/gitlab-runner/.ssh/id_rsa.pub` 文件内容，[在 GitHub 上添加这个 SSH Key](https://github.com/settings/keys)。 由于是使用 root 帐号，弄完了之后不要忘了修改用户组：

```
$ sudo chown -R gitlab-runner:gitlab-runner /home/gitlab-runner/.ssh
```

然后，同样是通过 `.gitlab-ci.yml` 实现自动同步：

```
git push --force --mirror git@github.com:[Organization]/[Project].git
```

修改 `[Organization]` 和 `[Project]` 为你自己的名称即可。

## 谈谈安装在自己服务器上的 GitLab 的好处

文件都存储在自己的服务器里，安全性比较有保障，自己有最高权限，不会遇到项目被删的情况。部署时延迟极低，可靠性也高，不会遇到自己服务器没问题但是第三方服务宕机导致无法部署的窘况。 可以根据情况部署到离自己最近的服务器，或者是内部服务器，像 GitHub 的服务器就在美国东岸，亚洲这边连接并不快，国内也不稳定。 最关键的是，如果你本来就有个 VPS 什么的，也有很大的空闲，那么相当于你可以免费获得私有存放处，但是[要注意性能需求](http://docs.gitlab.com/ce/install/requirements.html#hardware-requirements)，没有足够的空闲还是不要启用。 由于能够配置好实时同步镜像到 GitHub，GitLab 还有那么多 GitHub 没有的功能，其实已经可以完全使用 GitLab 作为主要的版本控制工具，GitHub 只是存一份镜像备用。
