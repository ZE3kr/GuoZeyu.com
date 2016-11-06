---
layout: page
status: publish
published: true
title: 关于交换链接以及 Pingback
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 2114
wordpress_url: https://ze3kr.com/?page_id=2114
date: '2016-11-05 18:49:09 -0400'
date_gmt: '2016-11-05 10:49:09 -0400'
categories: []
tags: []
---
<p>欢迎链接到本站！本站提供交换链接，你只需要链接到本站即可，我会不定期的查看本站访客来源并手动添加链接，或者你也可以通过表格申请（可以提高收录率）。</p>
<h2>链接类型</h2>
<h3>引用链接（Pingback）</h3>
<p>如果你在某一篇特定文章内容中有一段文字链接到了本站的某一篇文章，在本站的那一篇文章上的评论区上方会显示你那一篇文章的标题和链接。通过 Pingback 方式实现的引用链接通常会立即自动显示在文章中。</p>
<p>如果你想要手动申请，请在 “贵站 URL” 上填写贵站链接到了本站的那篇文章的 URL，“贵站标题” 上填写那篇文章的标题以及贵站名，并在 “本站 URL” 上填写所引用的文章链接。</p>
<h3>友情链接</h3>
<p>友情链接即可以在网站首页上直接展示，或在网站首页上折叠，或者在一个字页面中。当你添加了友情链接，我会添加到本页面中，如果被认为内容优质则会添加到首页中。</p>
<p>如果你想要手动申请，请在 “贵站 URL” 上填写贵站首页的链接，“贵站标题” 上填写贵站网站首页的标题，并在 “本站 URL” 上填写当前页面链接，即 `https://ze3kr.com/links/` 。</p>
<h2>申请表格</h2>
<p>请确保已经添加了链接后再申请，否则申请无法通过！</p>
<p><a href="javascript:void(0);" id="show_apply" onclick="jQuery('#apply_form').show();jQuery('#report_form').hide();jQuery(this).hide();jQuery('#show_report').show();">显示申请表格</a></p>
<form action="https://ze3kr.com/wp-content/plugins/add-pingback-manually/add-pingback.php" method="post" id="apply_form">
<a href="javascript:void(0);" onclick="jQuery('#apply_form').hide();jQuery('#show_apply').show();">隐藏申请表格</a></p>
<p class="comment-notes"><span id="email-notes">电子邮件地址不会被公开。</span> 必填项已用<span class="required">*</span>标注</p>
<p><label for="pingback-id">本站 URL <span class="required">*</span></label><input id="pingback-id" name="pingback-id" type="url" value="" size="30" maxlength="200" required="required" aria-required="true"></p>
<p><label for="pingback-url">贵站 URL <span class="required">*</span></label><input id="pingback-url" name="pingback-url" type="url" value="" size="30" maxlength="200" required="required" aria-required="true"></p>
<p><label for="pingback-url">贵站标题 <span class="required">*</span></label><input id="pingback-title" name="pingback-title" type="text" value="" size="30" maxlength="245" aria-required="true" required="required"></p>
<p class="comment-form-email"><label for="pingback-email">电子邮件</label> <input id="pingback-email" name="pingback-email" type="email" value="" size="30" maxlength="100" aria-describedby="email-notes"></p>
<p class="comment-form-url"><label for="pingback-content">内容</label> <textarea id="pingback-content" name="pingback-content" cols="45" rows="3" maxlength="65525"></textarea></p>
<p class="form-submit"><input name="submit" type="submit" id="submit" class="submit" value="提交申请"> <input type="hidden" name="pingback-type" value="pingback" id="pingback-type"></p>
</form>
<p><a href="javascript:void(0);" id="show_report" onclick="jQuery('#report_form').show();jQuery('#apply_form').hide();jQuery(this).hide();jQuery('#show_apply').show();">显示举报表格</a></p>
<form action="https://ze3kr.com/wp-content/plugins/add-pingback-manually/add-pingback.php" method="post" id="report_form">
<a href="javascript:void(0);" onclick="jQuery('#report_form').hide();jQuery('#show_report').show();">隐藏举报表格</a></p>
<p class="comment-notes"><span id="email-notes">可以匿名提交</span></p>
<p class="comment-form-url"><label for="pingback-content">举报内容及原因 <span class="required">*</span></label> <textarea id="pingback-content" name="pingback-content" cols="45" rows="3" maxlength="65525"></textarea></p>
<p><label for="pingback-title">姓名</label> <input id="pingback-title" name="pingback-title" type="text" value="" size="30" maxlength="100"></p>
<p class="comment-form-email"><label for="pingback-email">电子邮件</label> <input id="pingback-email" name="pingback-email" type="email" value="" size="30" maxlength="100" aria-describedby="email-notes"></p>
<p class="form-submit"><input name="submit" type="submit" id="submit" class="submit" value="举报"> <input type="hidden" name="pingback-type" value="" id="pingback-type"><input type="hidden" name="pingback-id" value="https://ze3kr.com/links/" id="pingback-id"></p>
</form>
<p><script type="text/javascript"><br />
jQuery("#apply_form").hide();<br />
jQuery("#report_form").hide();<br />
</script></p>
