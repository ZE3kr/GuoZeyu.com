---
title: 用回 WordPress 自带评论系统
tags:
  - WordPress
id: '1922'
categories:
  - - 开发
date: 2016-08-22 11:32:57
---

最近 Disqus 被某国的墙搞的十分不稳定，于是又用回了 WordPress 自带的评论系统，但是这个评论系统却不带评论者被回复时的邮件提醒。我有自己的发信服务器（AWS SES）系统，所以理论上可以配合插件实现这个效果。但是我看了很多插件，基本上操作页面都太复杂，而且回复的邮件通常不支持中文，我只需要一个简单的回复系统，不用那么麻烦，于是干脆自己开发一个，最终比较完美的实现了这个功能。

我开发的这个功能特点是：当评论者被回复时，邮件标题是 “Re: \[文章标题\]”，这样评论者的一个留言被多个人回复时，会被自动在本地邮件客户端上归为一类；而且评论者收到邮件后可以直接回复邮件，会**直接**给回复的发出者发邮件（不会显示在网站上，我也无法看到，这将是两人的私聊）。

邮件内容简洁，无额外无用的东西，不会被认定为 Spam。

![收件例子](/images/2016/comment-1.png)

所有代码已经放到 [GitHub Gist](https://gist.github.com/ZE3kr/8c51a6349462935cefd2e636e96e93f8) 上。

```
<?php
function tlo_comment_mail_notify($comment_id) {
	global $comment_author;
	$comment = get_comment($comment_id);
	$parent_id = $comment->comment_parent ? $comment->comment_parent : '';
	$spam_confirmed = $comment->comment_approved;
	$from = $comment->comment_author_email;
	$to = get_comment($parent_id)->comment_author_email;
	if (($parent_id != '') && ($spam_confirmed != 'spam') && $from != $to && $to != get_bloginfo('admin_email') ) {
		$blog_name = get_option('blogname');
		$blog_url = site_url();
		$post_url = get_permalink( $comment->comment_post_ID );
		$comment_author = $comment->comment_author;
		$subject = 'Re: '.html_entity_decode(get_the_title($comment->comment_post_ID));
		$headers[] = 'Reply-To: '.$comment_author.' <'.$comment->comment_author_email.'>';
		$comment_parent = get_comment($parent_id);
		$comment_parent_date = tlo_get_comment_date( $comment_parent );
		$comment_parent_time = tlo_get_comment_time( $comment_parent );
		$message = <<<HTML
<!DOCTYPE html>
<html lang="zh">
	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<title>$blog_name</title>
	</head>
	<body>
		<style type="text/css">
		img {
			max-width: 100%; height: auto;
		}
		</style>
		<div class="content">
			<div>
				<p>$comment->comment_content</p>
			</div>
		</div>
		<div class="footer" style="margin-top: 10px">
			<p style="color: #777; font-size: small">
				&mdash;
				<br>
				Reply to this email to communicate with replier directly, or <a href="$post_url#comment-$comment_id">view it on $blog_name</a>.
				<br>
				You're receiving this email because of your comment got replied.
			</p>
		</div>
		<blockquote type="cite">
			<div>On {$comment_parent_date}, {$comment_parent_time}，$comment_parent->comment_author &lt;<a href="mailto: $comment_parent->comment_author_email">$comment_parent->comment_author_email</a>&gt; wrote：</div>
			<br>
			<div class="content">
				<div>
					<p>$comment_parent->comment_content</p>
				</div>
			</div>
		</blockquote>
	</body>
</html>
HTML;
		add_filter( 'wp_mail_content_type', 'tlo_mail_content_type' );
		add_filter( 'wp_mail_from_name', 'tlo_mail_from_name' );
		wp_mail( $to, $subject, $message, $headers );
	}
}
add_action('tlo_comment_post_async', 'tlo_comment_mail_notify');

function tlo_comment_mail_notify_async($comment_id) {
	wp_schedule_single_event( time(), 'tlo_comment_post_async', [$comment_id] );
}
add_action('comment_post', 'tlo_comment_mail_notify_async');
// add_action('comment_post', 'tlo_comment_mail_notify');

function tlo_mail_content_type() {
	return 'text/html';
}
function tlo_mail_from_name() {
	global $comment_author;
	return $comment_author;
}

function tlo_get_comment_time( $comment ) {
	$date = mysql2date(get_option('time_format'), $comment->comment_date, true);

	return apply_filters( 'tlo_get_comment_time', $date, $comment );
}
function tlo_get_comment_date( $comment ) {
	$date = mysql2date(get_option('date_format'), $comment->comment_date);

	return apply_filters( 'tlo_get_comment_date', $date, $comment );
}
```