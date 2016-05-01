ZE3kr.com
===============
这是我的个人博客的所有文章的源代码，带历史，实时更新。博客使用 WordPress。

主机使用 LNMP 配置，使用 [Vultr VPS](https://www.vultr.com/?ref=6886257) 的 2GB 版本。

## 自定义 Shortcode
本网站使用了自定义的 Shortcode，规则如下

### 图片

优雅的插入图片

例子：[img id="media id (int)" size="thumbnail/medium/large/full）" exif="on"]Caption[/img]

源代码（PHP），以插件的形式使用。

```php
function tlo_exif($id){
	$lang = get_locale();
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
	$image_meta = wp_get_attachment_metadata( $id )['image_meta'];
	if($image_meta['shutter_speed'] < 0.5){
		$image_meta['shutter_speed'] = '1/'.round(1/$image_meta['shutter_speed']);
	} else {
		$image_meta['shutter_speed'] = $image_meta['shutter_speed'].'s';
	}
	$image_meta['focal_length'] = round($image_meta['focal_length']);
	$exif = <<<HTML
<ul>
	<li>{$translate['camera']}: {$image_meta['camera']}</li>
	<li>{$translate['aperture']}: f/{$image_meta['aperture']}</li>
	<li>{$translate['shutter']}: {$image_meta['shutter_speed']}</li>
	<li>{$translate['iso']}: ISO{$image_meta['iso']}</li>
	<li>{$translate['length']}: {$image_meta['focal_length']}mm</li>
</ul>
HTML;
	return $exif;
}

function tlo_img($attr, $content=false) {
	// Example: [img id="123" size="large" exif="on"][/img]
	if( $attr['exif'] == 'only' ){
		return tlo_exif($attr['id']);
	}

	if(empty($attr['size'])){
		$attr['size'] = 'full';
	}
	$url = wp_get_attachment_url( $attr['id'] );
	$src = wp_get_attachment_image_src( $attr['id'], $attr['size'] );
	if($attr['size'] == 'large'){
		$src[0] = wp_get_attachment_image_src( $attr['id'], 'medium' )[0];
	}
	$srcset = wp_get_attachment_image_srcset( $attr['id'] );
	$sizes = wp_get_attachment_image_sizes( $attr['id'], $attr['size']);
	$return = <<<HTML
<a href="{$url}" target="_blank" rel="attachment wp-att-{$attr['id']}"><img src="{$src[0]}" width="{$src[1]}" height="{$src[2]}" class="aligncenter size-{$attr['size']} wp-image-{$attr['id']}" srcset="{$srcset}" sizes="{$sizes}"></a>
HTML;
	$content = htmlspecialchars($content);
	if( $attr['exif'] == 'on' ){
		$exif = tlo_exif($attr['id']);
		if($content){
			$content = '<h6 style="text-align: center; margin-top: 0.5em;">'.$content.'</h6>'.$exif;
		} else {
			$content = $exif;
		}
	} elseif($content){
		$content = '<h6 style="text-align: center; margin-top: 0.5em;">'.$content.'</h6>';
	}
	if($content){
		$return = '<figure id="attachment_'.$attr['id'].'" class="wp-caption aligncenter" style="width: '.$src[1].'px">'.$return.'<figcaption class="wp-caption-text">'.$content.'</figcaption></figure>';
	}
	return $return;
}
add_shortcode('img', 'tlo_img');
```

### Link

优雅的插入链接

源代码（PHP），以插件的形式使用。

```php
function tlo_a($attr, $content) {
	$lang = get_locale();
	$translate = [
		'zh_CN' => [
			'short' => '短链接',
			
		],
		'default' => [
			'short' => 'Short Link',
		],
	];
	if(isset($translate[$lang])){
		$translate = $translate[$lang];
	} else {
		$translate = $translate['default'];
	}

	if($attr['type'] == 'appstore'){
		return <<<HTML
<a href="https://itunes.apple.com/app/id{$attr['id']}?at=10lJIS" target="_black" style="width: 135px; height: 40px; position: absolute;"></a>
<div class="svg-appstore-CN"></div>
{$translate['short']}: <a href="https://itunes.apple.com/app/id{$attr['id']}?at=10lJIS" target="_black"><code>tlo.link/{$attr['short']}</code></a>
HTML;
	}
	if($attr['type'] == 'amazon') {
		return <<<HTML
<h6><a href="http://www.amazon.cn/gp/product/{$attr['id']}?tag=ze3kr-23" target="_blank">在亚马逊上购买正品</a></h6>
{$translate['short']}: <a href="http://www.amazon.cn/gp/product/{$attr['id']}?tag=ze3kr-23" target="_black"><code>tlo.link/{$attr['short']}</code></a><br/><br/>
HTML;
	}
	if($attr['href']){
		return '<a href="'.$attr['href'].'" target="_blank" rel="noreferrer">'.$content.'</a>';
	}
	if($attr['id']){
		return '<a href="'.get_permalink($attr['id']).'">'.$content.'</a>';
	}
	return '<a href="'.home_url( '/?s=' ).urlencode($content).'">'.$content.'</a>';
}
add_shortcode('a', 'tlo_a');
```