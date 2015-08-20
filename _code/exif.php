<?
$mmc = memcache_init();

/**
 * 将分数转化为浮点数
**/
function Deci_Con($coordPart){
	$parts = explode('/', $coordPart);// 将 "/" 的两边数据转化为数组

	if (count($parts) <= 0)
		return 0;

	if (count($parts) == 1)
		return $parts[0];

	return floatval($parts[0]) / floatval($parts[1]);
}

/**
 * 将 EXIF 中经纬度坐标转化为浮点数
 * @see http://stackoverflow.com/questions/2526304/php-extract-gps-exif-data
**/
function getGps($exifCoord, $hemi) {

	$exifCoord = explode(",", $exifCoord);

	$degrees = count($exifCoord) > 0 ? Deci_Con($exifCoord[0]) : 0;
	$minutes = count($exifCoord) > 1 ? Deci_Con($exifCoord[1]) : 0;
	$seconds = count($exifCoord) > 2 ? Deci_Con($exifCoord[2]) : 0;

	$flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

	return sprintf( "%.6f",$flip * ($degrees + $minutes / 60 + $seconds / 3600) );// 小数点后六位

}

$localization = [
	"ZH" => [
		"Camera" => "相机",
		"ISO" => "感光度",
		"Aperture" => "光圈",
		"Exposure" => "快门速度",
		"Focal length" => "焦距",
		"Lens" => "镜头",
		"HDR" => "HDR 已启用",
		"Real length" => "实际焦距:",
		"Exposure Program" => [
			"1" => "手动",
			"2" => "自动（程序曝光）",
			"3" => "光圈优先",
			"4" => "快门优先",
			"5" => "自动（创意）",
			"6" => "自动（运动）",
			"7" => "自动（人像）",
			"8" => "自动（风景）",
			"9" => "手动（B 门）",
		],
		"Program" => "模式",
		"Flash" => [
			"0" => [
				"fire" => false,
				"name" => "闪光灯关闭",
			],
			"1" => [
				"fire" => true,
				"name" => "闪光灯开启",
			],
			"5" => [
				"fire" => true,
				"name" => "闪光灯开启，未检测到闪光",
			],
			"7" => [
				"fire" => true,
				"name" => "闪光灯开启，检测到闪光",
			],
			// 第 8 个原本是“闪光灯强制开启，不闪光”。我觉得没有意义，跳过。
			"9" => [
				"fire" => true,
				"name" => "闪光灯强制开启",
			],
			"13" => [
				"fire" => true,
				"name" => "闪光灯强制开启，未检测到闪光",
			],
			"15" => [
				"fire" => true,
				"name" => "闪光灯强制开启，检测到闪光",
			],
			"16" => [
				"fire" => false,
				"name" => "闪光灯强制关闭",
			],
			"20" => [
				"fire" => false,
				"name" => "闪光灯强制关闭，未检测到闪光",
			],
			"24" => [
				"fire" => false,
				"name" => "闪光灯自动关闭",
			],
			"25" => [
				"fire" => true,
				"name" => "闪光灯自动开启",
			],
			"29" => [
				"fire" => true,
				"name" => "闪光灯自动开启，未检测到闪光",
			],
			"31" => [
				"fire" => true,
				"name" => "闪光灯自动开启，检测到闪光",
			],
			"32" => [
				"fire" => false,
				"name" => "无闪光功能",
			],
			"65" => [
				"fire" => true,
				"name" => "闪光灯开启，消除红眼",
			],
			"69" => [
				"fire" => true,
				"name" => "闪光灯开启，消除红眼，未检测到闪光",
			],
			"71" => [
				"fire" => true,
				"name" => "闪光灯开启，消除红眼，检测到闪光",
			],
			"73" => [
				"fire" => true,
				"name" => "闪光灯强制开启，消除红眼",
			],
			"77" => [
				"fire" => true,
				"name" => "闪光灯强制开启，消除红眼，未检测到闪光",
			],
			"79" => [
				"fire" => true,
				"name" => "闪光灯强制开启，消除红眼，检测到闪光",
			],
			// 第 80 个原本是“闪光灯强制关闭，消除红眼”，我觉得没有意义，跳过。
			"89" => [
				"fire" => true,
				"name" => "闪光灯自动开启，消除红眼",
			],
			"93" => [
				"fire" => true,
				"name" => "闪光灯自动开启，消除红眼，未检测到闪光",
			],
			"95" => [
				"fire" => true,
				"name" => "闪光灯自动开启，消除红眼，检测到闪光",
			],
		],
	],
	"EN" => [
		"Camera" => "Camera",
		"ISO" => "Film speed",
		"Aperture" => "Aperture",
		"Exposure" => "Shutter speed",
		"Focal length" => "Focal length",
		"Lens" => "Camera lens",
		"HDR" => "HDR is enabled",
		"Real length" => "Real length:",
		"Exposure Program" => [
			"1" => "Manual",
			"2" => "Auto (Program exposure)",
			"3" => "Aperture priority",
			"4" => "Shutter priority",
			"5" => "Auto (Creative)",
			"6" => "Auto (Action)",
			"7" => "Auto (Portrait)",
			"8" => "Auto (Landscape)",
			"9" => "Auto (Blub)",
		],
		"Program" => "Program",
		"Flash" => [
			"0" => [
				"fire" => false,
				"name" => "Flash did not fire",
			],
			"1" => [
				"fire" => true,
				"name" => "Flash fired",
			],
			"5" => [
				"fire" => true,
				"name" => "Flash fired, return light not detected",
			],
			"7" => [
				"fire" => true,
				"name" => "Flash fired, return light detected",
			],
			"9" => [
				"fire" => true,
				"name" => "Flash compulsorily fired",
			],
			"13" => [
				"fire" => true,
				"name" => "Flash compulsorily fired, return light not detected",
			],
			"15" => [
				"fire" => true,
				"name" => "Flash compulsorily fired, return light detected",
			],
			"16" => [
				"fire" => false,
				"name" => "Flash compulsorily did not fired",
			],
			"20" => [
				"fire" => false,
				"name" => "Flash compulsorily did not fired, return light not detected",
			],
			"24" => [
				"fire" => false,
				"name" => "Flash automatically did not fired",
			],
			"25" => [
				"fire" => true,
				"name" => "Flash automatically fired",
			],
			"29" => [
				"fire" => true,
				"name" => "Flash automatically fired, return light not detected",
			],
			"31" => [
				"fire" => true,
				"name" => "Flash automatically fired, return light detected",
			],
			"32" => [
				"fire" => false,
				"name" => "No flash function",
			],
			"65" => [
				"fire" => true,
				"name" => "Flash fired, red-eye reduced",
			],
			"69" => [
				"fire" => true,
				"name" => "Flash fired, red-eye reduced, return light not detected",
			],
			"71" => [
				"fire" => true,
				"name" => "Flash fired, red-eye reduced, return light detected",
			],
			"73" => [
				"fire" => true,
				"name" => "Flash compulsorily fired, red-eye reduced",
			],
			"77" => [
				"fire" => true,
				"name" => "Flash compulsorily fired, red-eye reduced, return light not detected",
			],
			"79" => [
				"fire" => true,
				"name" => "Flash compulsorily fired, red-eye reduced, return light detected",
			],
			"89" => [
				"fire" => true,
				"name" => "Flash automatically fired, red-eye reduced",
			],
			"93" => [
				"fire" => true,
				"name" => "Flash automatically fired, red-eye reduced, return light not detected",
			],
			"95" => [
				"fire" => true,
				"name" => "Flash automatically fired, red-eye reduced, return light detected",
			],
		],
	],
];

$in35mmFilmData = [
	/**
	 * ███╗   ██╗██╗██╗  ██╗ ██████╗ ███╗   ██╗
	 * ████╗  ██║██║██║ ██╔╝██╔═══██╗████╗  ██║
	 * ██╔██╗ ██║██║█████╔╝ ██║   ██║██╔██╗ ██║
	 * ██║╚██╗██║██║██╔═██╗ ██║   ██║██║╚██╗██║
	 * ██║ ╚████║██║██║  ██╗╚██████╔╝██║ ╚████║
	 * ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝ ╚═════╝ ╚═╝  ╚═══╝
	**/

	/* 专业级系列 */
	"D1" => 1.52,
	"D1H" => 1.52,
	"D1X" => 1.52,
	"D2X" => 1.52,
	"D2H" => 1.52,
	"D2Hs" => 1.52,
	"D2X" => 1.52,
	"D2Xs" => 1.52,

	/* 高档系列 */
	"D100" => 1.52,
	"D200" => 1.52,
	"D300" => 1.52,
	"D300S" => 1.52, "D300s" => 1.52,

	/* 中档系列 */
	"D40" => 1.52,
	"D40x" => 1.52,
	"D50" => 1.52,
	"D60" => 1.52,
	"D70" => 1.52,
	"D70S" => 1.52, "D300s" => 1.52,
	"D80" => 1.52,
	"D90" => 1.52,
	"D3000" => 1.52,
	"D3100" => 1.56,
	"D3200" => 1.55,
	"D3300" => 1.53,
	"D5000" => 1.52,
	"D5100" => 1.52,
	"D5200" => 1.52,
	"D5300" => 1.53,
	"D5500" => 1.52,
	"D7000" => 1.52,
	"D7100" => 1.52,
	"D7200" => 1.52,
	"Coolpix A" => 1.53,

	/**
	 *  ██████╗ █████╗ ███╗   ██╗ ██████╗ ███╗   ██╗
	 * ██╔════╝██╔══██╗████╗  ██║██╔═══██╗████╗  ██║
	 * ██║     ███████║██╔██╗ ██║██║   ██║██╔██╗ ██║
	 * ██║     ██╔══██║██║╚██╗██║██║   ██║██║╚██╗██║
	 * ╚██████╗██║  ██║██║ ╚████║╚██████╔╝██║ ╚████║
	 * ╚═════╝╚═╝  ╚═╝╚═╝  ╚═══╝ ╚═════╝ ╚═╝  ╚═══╝
	**/
	/* 专业级系列 */
	"EOS 1D" => 1.255,
	"EOS 1D Mark II" => 1.255,
	"EOS 1D Mark III" => 1.28,
	"EOS 1D Mark IV" => 1.29,

	/* 高档系列 */
	"EOS 7D" => 1.62,
	"EOS 7D Mark II" => 1.62,

	/* 中档系列 */
	"EOS D30" => 1.62,
	"EOS D60" => 1.62,
	"EOS 10D" => 1.62,
	"EOS 20D" => 1.62,
	"EOS 30D" => 1.62,
	"EOS 30Da" => 1.62,
	"EOS 40D" => 1.62,
	"EOS 50D" => 1.62,
	"EOS 60D" => 1.62,
	"EOS 60Da" => 1.62,
	"EOS 70D" => 1.62,

	/* 入门系列 */
	"EOS 300D" => 1.62, "EOS Rebel" => 1.62, "EOS Kiss" => 1.62,
	"EOS 350D" => 1.62, "EOS Rebel XT" => 1.62, "EOS Kiss N" => 1.62,
	"EOS 400D" => 1.62, "EOS Rebel XTi" => 1.62, "EOS Kiss X" => 1.62,
	"EOS 450D" => 1.62, "EOS Rebel XSi" => 1.62, "EOS Kiss X2" => 1.62,
	"EOS 500D" => 1.62, "EOS Rebel T1i" => 1.62, "EOS Kiss X3" => 1.62,
	"EOS 550D" => 1.62, "EOS Rebel T2i" => 1.62, "EOS Kiss X4" => 1.62,
	"EOS 600D" => 1.62, "EOS Rebel T3i" => 1.62, "EOS Kiss X5" => 1.62,
	"EOS 650D" => 1.62, "EOS Rebel T4i" => 1.62, "EOS Kiss X6i" => 1.62,
	"EOS 700D" => 1.62, "EOS Rebel T5i" => 1.62, "EOS Kiss X7i" => 1.62,
	"EOS 750D" => 1.62, "EOS Rebel T6i" => 1.62, "EOS Kiss X8i" => 1.62,
	"EOS 760D" => 1.62, "EOS Rebel T6s" => 1.62,  "EOS 8000D" => 1.62,
	"EOS 1100D" => 1.62, "EOS Rebel T3" => 1.62,  "EOS Kiss X50" => 1.62,
	"EOS 1200D" => 1.62, "EOS Rebel T5" => 1.62,  "EOS Kiss X70" => 1.62,

	/* 微单系列 */
	"EOS M1" => 1.62,
	"EOS M2" => 1.62,
	"EOS M3" => 1.62,
];

$fixLens = [
	/*
	"EF70-300mm f/4-5.6L IS USM" => "EF 70-300mm f/4-5.6L IS USM",
	"EF-S15-85mm f/3.5-5.6 IS USM" => "EF-S 15-85mm f/3.5-5.6 IS USM",
	*/
];

$camerasLink = [
	"iPhone 6" => "asin=B00NQGP5M8&CN=B00OB5T6DM",
	"Apple iPhone 6" => "asin=B00NQGP5M8&CN=B00OB5T6DM",
	"HERO4 Silver" => "asin=B00NIYJF6U&CN=B00R24SK4K",
	"GoPro HERO4 Silver" => "asin=B00NIYJF6U&CN=B00R24SK4K",
	"Canon EOS 7D" => "asin=B002NEGTTW&CN=B002QB2H98",
	"Canon EOS 7D Mark II" => "asin=B00NEWZDRG&CN=B00OXKZWX8",
];

$lensLink = [
	"EF70-300mm f/4-5.6L IS USM" => "asin=B0040Y83X8",
	"EF-S15-85mm f/3.5-5.6 IS USM" => "asin=B002NEGTTM&CN=B004H3W8WE",
];

$catchurl = 'http://tlimage.b0.upaiyun.com';// UPYUN 图片位置
if( substr($_SERVER["REQUEST_URI"], 0, 6) == '/'.'lang/' )
{
	$path = substr($_SERVER["REQUEST_URI"], 8, strpos($_SERVER["REQUEST_URI"]."?", "?"));
	$language = $localization[ substr($_SERVER["REQUEST_URI"], 6, 2) ];
}
else
{
	$path = substr($_SERVER["REQUEST_URI"], 0, strpos($_SERVER["REQUEST_URI"]."?", "?"));
	$language = $localization[ 'EN' ];
}
if(substr($path,-13) == '!exif.json-js')// UPYUN EXIF 信息后缀为 "!exif.json"
{
	$path = substr($path,0,-3);
	$get = memcache_get($mmc,$path);
	if($get != '')
	{
		if($get['status'] == 200)
		{
			$pagecontent = $get['content'];
		}
		$info = [ 'http_code'=>$get['status'], ];
		$cache = "OK";
	}
	else
	{
		$curl = curl_init();
		$szUrl = $catchurl.$path;
		curl_setopt($curl, CURLOPT_URL, $szUrl);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 不显示网页内容
		curl_setopt($curl, CURLOPT_ENCODING, '');
		$data=curl_exec($curl);
		$info = curl_getinfo($curl);
		$httpHeaderSize = $info['header_size'];
		$pHeader = substr($data, 0, $httpHeaderSize);
		$pagecontent = substr($data, $httpHeaderSize);
		$regex = "/Content-Length:\s([0-9].+?)\s/";
		$count = preg_match($regex, $pHeader, $matches);
		$cache = "No";
	}
}
if($info['http_code'] == 200)
{
	$value = [ 'status' => '200', 'content' => $pagecontent ];
	memcache_set($mmc,$path,$value);// 缓存 EXIF 内容
	header('HTTP/1.1 200 OK');
	header('Content-Type: text/javascript');
	header('Cache-Control: public, max-age=86400');
	echo <<<JS
/* The original code is at here: https://ze3kr.tlo.xyz/specs/#code */
JS;
	$pagecontent = json_decode($pagecontent,true);
	$exif = $pagecontent['EXIF'];// 抓取 EXIF

	$FocalLengthIn35mmFilm = sprintf("%.0f", Deci_Con($exif['FocalLength']));// 格式化焦距

	/**
	 * 计算部分非全画幅相机的等效焦距
	 * @see https://en.wikipedia.org/wiki/APS-C#Multiplier_factors
	**/

	$model = $exif['Model'];
	$model = str_replace( 'Nikon ', '', $model);
	$model = str_replace( ' Digital', '', $model);
	$model = str_replace( ' DIGITAL', '', $model);
	$model = str_replace( 'Canon ', '', $model);
	$model = str_replace( $exif['Make'].' ', '', $model);
	if( isset($in35mmFilmData[ $model ]) ) {
		$FocalLengthIn35mmFilm = sprintf("%.0f", $in35mmFilmData[ $model ] * Deci_Con($exif['FocalLength']));
	}

	if( isset($exif['FocalLengthIn35mmFilm']) ) {
		$FocalLengthIn35mmFilm = sprintf("%.0f", Deci_Con($exif['FocalLengthIn35mmFilm']));// 格式化等效焦距
	}

	$ApertureValue = sprintf("%.1f", pow(1.41,Deci_Con($exif['ApertureValue'])));// 计算光圈
	$ExposureTime = Deci_Con($exif['ExposureTime']);// 格式化快门时间
	/**
	 * 格式化快门时间
	**/
	if( $ExposureTime <= 0.1 )
	{
		$ExposureTime = '1/'.sprintf("%.0f", 1/$ExposureTime).' s';// 快门时间小于等于 0.1s 时, 显示 1/$x + s 的形式
	}
	elseif( $ExposureTime <= 1 )
	{
		$ExposureTime = sprintf("%.2f", $ExposureTime).'&quot;';// 快门时间小于等于 1s 又大于 0.1s 时, 显示保留小数点后 2 位的定点数
	}
	elseif( $ExposureTime <= 10 )
	{
		$ExposureTime = sprintf("%.1f", $ExposureTime).'&quot;';// 快门时间大于 1s 又小于等于 10s 时, 显示保留小数点后 1 位的定点数
	}
	elseif( $ExposureTime <= 60 )
	{
		$ExposureTime = sprintf("%.0f", $ExposureTime).'&quot;';// 快门时间大于 10s 又小于等于 60s 时, 显示整数
	}
	else
	{
		$ExposureTime = sprintf("%.1f", $ExposureTime/60).'min';// 快门时间大于 60s 显示保留小数点后 1 位的定点数 (表示分钟)
	}

	/**
	 * 赞助商链接
	**/
	if( isset( $camerasLink[ $exif['Model'] ] ) )
	{
		$link = 'https://cdn-tlo.b0.upaiyun.com/html/redirecting21.html#' . $camerasLink[ $exif['Model'] ];
		$cameraText = "<li><b>{$language['Camera']}:</b> <i><a href='{$link}' style='color:#0078A8;' target='_black'>{$exif['Make']} {$model}</a></i></li>";
	}
	else
	{
		$cameraText = "<li><b>{$language['Camera']}:</b> <i>{$exif['Make']} {$model}</i></li>";
	}

	if( isset( $language['Exposure Program'][ $exif['ExposureProgram'] ] ) ) // 显示模式
	{
		$programText = "<li><b>{$language['Program']}:</b> <i>{$language['Exposure Program'][ $exif['ExposureProgram'] ]}</i></li>";
	}

	/**
	 * 基本信息
	**/
	$html = <<<HTML
<li><b><i class='exif-li-i'>{$FocalLengthIn35mmFilm}mm</i><i class='exif-li-i'>f/{$ApertureValue}</i><i class='exif-li-i'>{$ExposureTime}</i><i class='exif-li-i'>ISO {$exif['ISOSpeedRatings']}</i></b></li>{$programText}{$cameraText}
HTML;

	/**
	 * 额外信息
	**/

	if( isset( $exif['0xA434'] ) ) { // 显示相机镜头
		$lens = $exif['0xA434'];
		if( isset( $fixLens[ $lens ] ) )
		{
			$lens = $fixLens[ $lens ];
		}

		if( isset( $lensLink[ $exif['0xA434'] ] ) )
		{
			$link = 'https://cdn-tlo.b0.upaiyun.com/html/redirecting21.html#' . $lensLink[ $exif['0xA434'] ];
			$html = $html."<li><b>{$language['Lens']}:</b> <i><a href='{$link}' style='color:#0078A8;' target='_black'>{$lens}</a></i></li>";
		}
		else
		{
			$html = $html."<li><b>{$language['Lens']}:</b> <i>{$lens}</i></li>";
		}
	}

	if( isset( $exif['CustomRendered'] ) && $exif['CustomRendered'] !== '0' ) { // 显示 HDR
		$html = $html."<li><b>{$language['HDR']}</b></li>";
	}

	if( isset($language['Flash'][ $exif['Flash'] ]) ) {
		$html = $html."<li><b>{$language['Flash'][ $exif['Flash'] ]['name']}</b></li>";
	}

	if( isset($exif["GPSLongitude"]) && isset($exif["GPSLatitude"]) ) { //显示地图
		$html = $html . <<<HTML
<style>.tlo-map{display:block!important;}</style>
HTML;
		$echo = '$(document).ready(function(){$("#exif").html("'.$html.'");});';
		$lon = getGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);
		$lat = getGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
		$gps = <<<JS

var lat = {$lat};
var lon = {$lon};
var gps = 'isset';
JS;
		if( isset($exif["GPSLatitude"]) ){
			$gps = $gps.<<<JS
var gps3d = 'isset';
var height = '{$exif["GPSLatitude"]}';
JS;
		}

		$echo = $gps.$echo;
	}
	else
	{
		$echo = '$(document).ready(function(){$("#exif").html("'.$html.'");});';
	}
	echo $echo;// 输出 JavaSript
}
else
{
	header('Cache-Control: no-cache');
	header('Content-Type: text/javascript');
	echo <<<JS
/* The original code is at here: https://ze3kr.tlo.xyz/specs/#code */
/* 404 Page Not Find */
JS;
}

if($info['http_code'] == 404)// 针对 404 页面缓存 1 小时
{
	$value = [ 'status' => '404' ];
	memcache_set($mmc,$path,$value, 0, 3600);
}