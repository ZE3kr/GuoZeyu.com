---
layout: page
title: README
permalink: /readme/
---
ZE3kr.com
===============
这是我的个人博客的所有文章的源代码，博客使用 WordPress。

## 自定义 Shortcode
本网站使用了自定义的 Shortcode，规则如下

### 图片
例子：[img id="图片ID（整数）" size="大小（thumbnail、medium、large、full）" exif="on"]标题[/img]

```php
function tlo_img($attr, $content=false) {
	// Example: [img id="123" size="large" exif="on"][/img]
	if(empty($attr['size'])){
		$attr['size'] = 'full';
	}
	$srcset = wp_get_attachment_image_srcset( $attr['id'] );
	$url = wp_get_attachment_url( $attr['id'] );
	$src = wp_get_attachment_image_src( $attr['id'], $attr['size'] );
	if( $attr['size'] == 'full' ) {
		$srcset = '';
		$sizes = '';
	} elseif( $attr['size'] == 'large' ) {
		$sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 62vw, 840px';
	} elseif( $attr['size'] == 'medium' ){
		$sizes = '(max-width: 450px) 85vw, 450px';
	} else {
		$srcset = '';
		$sizes = '';
	}
	$return = <<<HTML
<a href="{$url}" target="_blank" rel="attachment wp-att-{$attr['id']}"><img src="{$src[0]}" width="{$src[1]}" height="{$src[2]}" class="aligncenter size-{$attr['size']} wp-image-{$attr['id']}" srcset="{$srcset}" sizes="{$sizes}"></a>
HTML;
	$content = htmlspecialchars($content);
	if( $attr['exif'] == 'on' ){
		$lang = get_locale();
		$image_meta = wp_get_attachment_metadata( $attr['id'] )['image_meta'];
		if($image_meta['shutter_speed'] < 0.5){
			$image_meta['shutter_speed'] = '1/'.round(1/$image_meta['shutter_speed']);
		} else {
			$image_meta['shutter_speed'] = $image_meta['shutter_speed'].'s';
		}
		$image_meta['focal_length'] = round($image_meta['focal_length']);
		$translate = [
			'zh_CN' => [
				'camera' => '相机',
				'aperture' => '光圈',
				'shutter' => '快门速度',
				'iso' => '感光度',
				'length' => '实际焦距',
			],
			'default' => [
				'camera' => 'Camera',
				'aperture' => 'Aperture',
				'shutter' => 'Shutter Speed',
				'iso' => 'Light Sensitivity',
				'length' => 'Real Focal Length',
			]
		];
		if(isset($translate[$lang])){
			$translate = $translate[$lang];
		} else {
			$translate = $translate['default'];
		}
		$exif = <<<HTML
<ul>
	<li>{$translate['camera']}: {$image_meta['camera']}</li>
	<li>{$translate['aperture']}: f/{$image_meta['aperture']}</li>
	<li>{$translate['shutter']}: {$image_meta['shutter_speed']}</li>
	<li>{$translate['iso']}: ISO{$image_meta['iso']}</li>
	<li>{$translate['length']}: {$image_meta['focal_length']}mm</li>
</ul>
HTML;
		if($content){
			$content = '<h6 style="text-align: center;">'.$content.'</h6>'.$exif;
		} else {
			$content = $exif;
		}
	} elseif($content){
		$content = '<h6 style="text-align: center;">'.$content.'</h6>';
	}
	if($content){
		$return = '<figure id="attachment_'.$attr['id'].'" class="wp-caption aligncenter" style="width: '.$src[1].'px">'.$return.'<figcaption class="wp-caption-text">'.$content.'</figcaption></figure>';
	}
	return $return;
}
add_shortcode('img', 'tlo_img');
```

### AppStore

```php
function app_store_zh($attr, $content) {
	return <<<HTML
<a href="https://itunes.apple.com/app/id{$attr['id']}?at=10lJIS" target="_black" style="width: 135px; height: 40px; position: absolute;"></a>
<div class="svg-appstore-CN"></div>
短链接：<a href="https://itunes.apple.com/app/id{$attr['id']}?at=10lJIS" target="_black"><code>tlo.link/{$attr['short']}</code></a><br/><br/>
HTML;
}
add_shortcode('app_store_zh', 'app_store_zh');
```