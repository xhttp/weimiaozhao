<?php
include('/www/weimiaozhao/spider/base.php');

$base_url = 'http://www.zxzhijia.com';

$header = 'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
Accept-Encoding:gzip, deflate, sdch
Accept-Language:zh-CN,zh;q=0.8
Cache-Control:no-cache
Connection:keep-alive
Cookie:PHPSESSID=ii4lt507jdui2v44lpa7hvngj1; Hm_lvt_7b35f293bc11ee39082fa12ba454d185=1460639972,1461677214; Hm_lpvt_7b35f293bc11ee39082fa12ba454d185=1461677256; citymsg=370100-jn-%E6%B5%8E%E5%8D%97
Host:img.zxzhijia.com
Referer:http://www.zxzhijia.com/Info/zhishi/5517.html
Pragma:no-cache
Upgrade-Insecure-Requests:1
User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.82 Safari/537.36';

$header_array = explode("\n", $header);

// list
$sql = "SELECT * FROM crawler_article WHERE url LIKE '".$base_url."%'";
//$sql = "SELECT * FROM crawler_article WHERE url = 'http://www.wed114.cn/jiehun/shishangbaobao/2012061165743.html'";
$result = $mysqli->query($sql);
while($row = $result->fetch_assoc()) {
echo $row['url'], "\n";
    $content = $row['content'];

    //
    $content = str_replace("\r", "", $content);
    $content = str_replace("\n", "", $content);
    $content = preg_replace('/<style>.*<\/style>/', '', $content);
    $content = preg_replace('/<script.*<\/script>/isU', '', $content);
    $content = strip_tags($content, '<div> <p> <img>');
    $content = str_replace(' style="text-indent:2em;"', '', $content);
    $content = str_replace(' style="text-align:center;text-indent:2em;"', '', $content);
    $content = str_replace("\t", '', $content);
    $content = str_replace(' align="center" style="text-align:left;"', '', $content);
    $content = str_replace(' style="text-indent:28pt;"', '', $content);
    $content = str_replace(' style="text-indent:21pt;"', '', $content);
    $content = str_replace(' style="text-indent:24pt;background-color:white;" align="left"', '', $content);
    $content = str_replace(' align="left"', '', $content);
    $content = str_replace("  ", '', $content);
    $content = str_replace(" <p>", '<p>', $content);
    $content = str_replace("<p></p>", '', $content);
    $content = str_replace("<p> </p>", '', $content);
    $content = str_replace('<p>　　</p>', '', $content);
    $content = str_replace('<p>&nbsp;</p>', '', $content);
    $content = str_replace('<p>&nbsp; </p>', '', $content);
    $content = str_replace('装修之家网', '', $content);
    $content = str_replace('<p class="MsoNormal">&nbsp;</p>', '', $content);
    $content = str_replace(' class="MsoNormal"', '', $content);
    $content = str_replace(' style="text-align:center;" class="MsoNormal"', '', $content);
    //$content = str_replace(' style="text-align:center;"', '', $content);

//echo $content, "\n";
//exit();
    $pic_array = array();
    $pic = array();

    if(preg_match_all('/src="(http:\/\/[a-zA-Z0-9\/\_\-\.]+\.(jpg|png|jpeg|gif))"/', $content, $array)) {
        foreach($array[1] as $pic_url) {
            array_push($pic_array, $pic_url);
        }
    }
print_r($pic_array);
//exit();
        foreach($pic_array as $pic_url) {
            sleep(1);
            $http_pic_url = $pic_url;
            $pic_host = parse_url($http_pic_url);
            $header_array[6] = 'Host:'.$pic_host['host'];
//print_r($header_array);
//exit();
//echo $http_pic_url;
//xit();
            $ret = http_get($http_pic_url, $header_array);
print_r($ret['info']);
//exit();
            if($ret['info']['http_code'] != 200) {
                $ret = http_get($http_pic_url, $header_array);
            }

            $img_hash = md5($pic_url);
            $img_dir = substr($img_hash, 2, 2).'/'.substr($img_hash, 8, 2);
            $img_path = '/www/weimiaozhao/pic/ori/'.$img_dir;
            $img_thumb_path = '/www/weimiaozhao/pic/thumb/'.$img_dir;
            $img_small_path = '/www/weimiaozhao/pic/small/'.$img_dir;

            if(!is_dir($img_path)) {
                mkdir($img_path, 0777, TRUE);
            }
            if(!is_dir($img_thumb_path)) {
                mkdir($img_thumb_path, 0777, TRUE);
            }
            if(!is_dir($img_small_path)) {
                mkdir($img_small_path, 0777, TRUE);
            }

            file_put_contents($img_path.'/'.$img_hash.'.jpg', $ret['body']);

            // 替换内容
            $content = str_replace($pic_url, '_IMG_URL_'.$img_dir.'/'.$img_hash.'.jpg', $content);

            array_push($pic, '_IMG_URL_'.$img_dir.'/'.$img_hash.'.jpg');

            //
            list($width, $height, $type, $attr) = getimagesize($img_path.'/'.$img_hash.'.jpg');
            $cmd = 'gm convert '.$img_path.'/'.$img_hash.'.jpg'.' -thumbnail \''.$width.'x'.$height.'^\' -gravity center -extent '.$width.'x'.$height.' +profile "*" '.$img_thumb_path.'/'.$img_hash.'.jpg';
            exec($cmd);
 
            $cmd = 'gm convert '.$img_path.'/'.$img_hash.'.jpg'.' -thumbnail \'230x180^\' -gravity center -extent 230x180 +profile "*" '.$img_small_path.'/'.$img_hash.'.jpg';
            exec($cmd);
        }

    $sql = "INSERT INTO online_article(category_id, title, pub_time, url, pic, content, create_time, source, recommend, status) VALUES(1, '".
              $mysqli->real_escape_string($row['title'])."', '".
              $mysqli->real_escape_string($row['pub_time'])."', '".
              $mysqli->real_escape_string($row['url'])."', '".
              $mysqli->real_escape_string(json_encode($pic))."', '".
              $mysqli->real_escape_string($content)."', NOW(), '', 0, 0)";
//echo $sql, "\n";
    $mysqli->query($sql);
//echo $content, "\n";
//exit();
}

$mysqli->close();
