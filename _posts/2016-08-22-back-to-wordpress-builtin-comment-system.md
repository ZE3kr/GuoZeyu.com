---
layout: post
status: publish
published: true
title: 用回 WordPress 自带评论系统
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 1922
wordpress_url: https://ze3kr.com/?p=1922
date: '2016-08-22 11:32:57 -0400'
date_gmt: '2016-08-22 03:32:57 -0400'
categories:
- 开发
tags: []
---
<p>最近 Disqus 被某国的墙搞的十分不稳定，于是又用回了 WordPress 自带的评论系统，但是这个评论系统却不带评论者被回复时的邮件提醒。我有自己的发信服务器（AWS SES）系统，所以理论上可以配合插件实现这个效果。但是我看了很多插件，基本上操作页面都太复杂，而且回复的邮件通常不支持中文，我只需要一个简单的回复系统，不用那么麻烦，于是干脆自己开发一个，最终比较完美的实现了这个功能。</p>
<p><!--more 阅读更多关于代码的实现--></p>
<p>我开发的这个功能特点是：当评论者被回复时，邮件标题是 “Re: [文章标题]”，这样评论者的一个留言被多个人回复时，会被自动在本地邮件客户端上归为一类；而且评论者收到邮件后可以直接回复邮件，会<strong>直接</strong>给回复的发出者发邮件（不会显示在网站上，我也无法看到，这将是两人的私聊）。</p>
<p>邮件内容简洁，无额外无用的东西，不会被认定为 Spam。</p>
<p>[img id="1926" size="medium"]收件例子[/img]</p>
<p>所有代码已经放到 <a href="https://gist.github.com/ZE3kr/8c51a6349462935cefd2e636e96e93f8" target="_blank">GitHub Gist</a> 上。</p>
<pre class="lang:php decode:true " title="核心代码（英文版，已简化）">&lt;?php
function tlo_comment_mail_notify($comment_id) {
	global $comment_author;
	$comment = get_comment($comment_id);
	$parent_id = $comment-&gt;comment_parent ? $comment-&gt;comment_parent : '';
	$spam_confirmed = $comment-&gt;comment_approved;
	$from = $comment-&gt;comment_author_email;
	$to = get_comment($parent_id)-&gt;comment_author_email;
	if (($parent_id != '') &amp;&amp; ($spam_confirmed != 'spam') &amp;&amp; $from != $to &amp;&amp; $to != get_bloginfo('admin_email') ) {
		$blog_name = get_option('blogname');
		$blog_url = site_url();
		$post_url = get_permalink( $comment-&gt;comment_post_ID );
		$comment_author = $comment-&gt;comment_author;
		$subject = 'Re: '.html_entity_decode(get_the_title($comment-&gt;comment_post_ID));
		$headers[] = 'Reply-To: '.$comment_author.' &lt;'.$comment-&gt;comment_author_email.'&gt;';
		$comment_parent = get_comment($parent_id);
		$comment_parent_date = tlo_get_comment_date( $comment_parent );
		$comment_parent_time = tlo_get_comment_time( $comment_parent );
		$message = &lt;&lt;&lt;HTML
&lt;p&gt;$comment-&gt;comment_content&lt;/p&gt;
&lt;p style="color: #777; font-size: small"&gt;
	&amp;mdash;
	&lt;br&gt;
	Reply to this email to communicate with replier directly, or &lt;a href="$post_url#comment-$comment_id"&gt;view it on $blog_name&lt;/a&gt;.
	&lt;/p&gt;
&lt;/div&gt;
&lt;blockquote type="cite"&gt;
	&lt;div&gt;On {$comment_parent_date}, {$comment_parent_time}，$comment_parent-&gt;comment_author &lt;&lt;a href="mailto: $comment_parent-&gt;comment_author_email"&gt;$comment_parent-&gt;comment_author_email&lt;/a&gt;&gt; wrote：&lt;/div&gt;
	&lt;br&gt;
	&lt;div class="content"&gt;
		&lt;div&gt;
			&lt;p&gt;$comment_parent-&gt;comment_content&lt;/p&gt;
		&lt;/div&gt;
	&lt;/div&gt;
&lt;/blockquote&gt;
HTML;
		add_filter( 'wp_mail_content_type', 'tlo_mail_content_type' );
		add_filter( 'wp_mail_from_name', 'tlo_mail_from_name' );
		wp_mail( $to, $subject, $message, $headers );
	}
}
add_action('comment_post', 'tlo_comment_mail_notify');
function tlo_mail_content_type() {
	return 'text/html';
}
function tlo_mail_from_name() {
	global $comment_author;
	return $comment_author;
}
function tlo_get_comment_time( $comment ) {
	$date = mysql2date(get_option('time_format'), $comment-&gt;comment_date, true);
	return apply_filters( 'tlo_get_comment_time', $date, $comment );
}
function tlo_get_comment_date( $comment ) {
	$date = mysql2date(get_option('date_format'), $comment-&gt;comment_date);
	return apply_filters( 'tlo_get_comment_date', $date, $comment );
}</pre>
