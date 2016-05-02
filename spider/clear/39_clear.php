<?php
include('/www/weimiaozhao/spider/base.php');

$base_url = 'http://fitness.39.net';

$header = 'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
Accept-Encoding:gzip, deflate, sdch
Accept-Language:zh-CN,zh;q=0.8
Cache-Control:no-cache
Connection:keep-alive
Host:pimg.39.net
Referer:http://fitness.39.net/jfzsk/140205/4202029.html
Pragma:no-cache
Upgrade-Insecure-Requests:1
User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.82 Safari/537.36';

$header_array = explode("\n", $header);

// list
$sql = "SELECT * FROM crawler_article WHERE url LIKE 'http://fitness.39.net%'";
//$sql = "SELECT * FROM crawler_article WHERE url = 'http://www.wed114.cn/jiehun/shishangbaobao/2012061165743.html'";
$result = $mysqli->query($sql);
while($row = $result->fetch_assoc()) {
echo $row['url'], "\n";
    $content = $row['content'];
    $content = preg_replace('/<style>.*<\/style>/', '', $content);
    $content = preg_replace('/<script.*<\/script>/isU', '', $content);
    $content = strip_tags($content, '<p>');

    $content = str_replace('            ', '', $content);
    $content = str_replace('<p>　　', '<p>', $content);
    $content = str_replace("</p>  <p>", '</p><p>', $content);
    $content = str_replace('<p style="text-align:center"></p>', '', $content);
    $content = str_replace('<p></p>', '', $content);

    //
    $pic_array = array();
    $pic = array();

    $pub_time = '';

    if(!empty($row['pic'])) {

        $pic_url = $row['pic'];

        if(preg_match('/(20[\d]{2}\-[\d]{2}\-[\d]{2})/isU', $pic_url, $pub_array)) {
            $pub_time = $pub_array[1];
        }
        $ret = http_get($pic_url, $header_array);

        if($ret['info']['http_code'] != 200) {
            $ret = http_get($pic_url, $header_array);
        }

        if($ret['info']['http_code'] == 200) {
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

            $content = str_replace($pic_url, '_IMG_URL_'.$img_dir.'/'.$img_hash.'.jpg', $content);

            array_push($pic, '_IMG_URL_'.$img_dir.'/'.$img_hash.'.jpg');

            //
            list($width, $height, $type, $attr) = getimagesize($img_path.'/'.$img_hash.'.jpg');
            $cmd = 'gm convert '.$img_path.'/'.$img_hash.'.jpg'.' -thumbnail \''.$width.'x'.$height.'^\' -gravity center -extent '.$width.'x'.$height.' +profile "*" '.$img_thumb_path.'/'.$img_hash.'.jpg';
            exec($cmd);
     
            $cmd = 'gm convert '.$img_path.'/'.$img_hash.'.jpg'.' -thumbnail \'230x180^\' -gravity center -extent 230x180 +profile "*" '.$img_small_path.'/'.$img_hash.'.jpg';
            exec($cmd);

            $content = '<p align="center"><img src="'.$pic[0].'" alt="'.$row['title'].'" border="0" width="'.$width.'" height="'.$height.'" /></p>'.$content;
        }
    }


    $sql = "INSERT INTO online_article(category_id, status, title, pub_time, url, pic, content, create_time) VALUES(1, 0, '".
              $mysqli->real_escape_string($row['title'])."', '".
              $mysqli->real_escape_string($pub_time)."', '".
              $mysqli->real_escape_string($row['url'])."', '".
              $mysqli->real_escape_string(json_encode($pic))."', '".
              $mysqli->real_escape_string($content)."', NOW())";
//echo $sql, "\n";
    $mysqli->query($sql);
//echo $content, "\n";
//exit();
}

$mysqli->close();

