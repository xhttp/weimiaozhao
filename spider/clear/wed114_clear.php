<?php
include('/www/weimiaozhao/spider/base.php');

$base_url = 'http://www.wed114.cn';

$header = 'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
Accept-Encoding:gzip, deflate, sdch
Accept-Language:zh-CN,zh;q=0.8
Cache-Control:no-cache
Connection:keep-alive
Host:www.wed114.cn
Referer:http://www.wed114.cn/jiehun/shenghuomiaozhao/20160318144167.html
Pragma:no-cache
Upgrade-Insecure-Requests:1
User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.82 Safari/537.36';

$header_array = explode("\n", $header);

// list
$sql = "SELECT * FROM crawler_article WHERE url LIKE 'http://www.wed114.cn%'";
//$sql = "SELECT * FROM crawler_article WHERE url = 'http://www.wed114.cn/jiehun/shishangbaobao/2012061165743.html'";
$result = $mysqli->query($sql);
while($row = $result->fetch_assoc()) {
echo $row['url'], "\n";
    $content = $row['content'];

    //
    $content = str_replace("<a href='http://www.wed114.cn/' target='_blank'>", '', $content);
    $content = preg_replace('/<a href=\'http:\/\/www\.wed114\.cn\/[a-z]+\/[a-z]+\/\' target=\'_blank\'>/', '', $content);
    $content = preg_replace('/<a href=\'http:\/\/www\.wed114\.cn\/[a-z]+\/[a-z]+\/[a-z]+\/\' target=\'_blank\'>/', '', $content);
    $content = preg_replace('/<a href="http:\/\/www\.wed114\.cn\/[a-z]+\/[a-z]+\/[0-9]+\.html">/', '', $content);
    $content = preg_replace('/<a href=\'http:\/\/www\.wed114\.cn[a-zA-Z0-9\.\/]+\' target=\'_blank\'>/', '', $content);
    $content = preg_replace('/<a target="_blank" href="http:\/\/www\.wed114\.cn\/[a-zA-Z0-9\.\/\_\%]+">/', '', $content);
    $content = preg_replace('/<a href="http:\/\/[0-9a-zA-Z\.\/\-\_]+">/', '', $content);
    $content = preg_replace('/<a href="https:\/\/[0-9a-zA-Z\.\/\-\_]+">/', '', $content);
    $content = preg_replace('/<a href=\'http:\/\/[0-9a-zA-Z\.\/\-\_]+\' target=\'_blank\'>/', '', $content);
    $content = str_replace('wed114结婚网', '', $content);
    $content = str_replace('wed114<u>结婚</u>网', '', $content);
    $content = str_replace('结婚网', '', $content);
    $content = str_replace('<p>&nbsp;</p>', '', $content);
    $content = preg_replace('/<a href=\'[\d]+_[\d]+\.html\'>/', '', $content);
    $content = preg_replace('/<a href=\'\/jiehun\/[a-z]+\/[\d]+\.html\'>/', '', $content);
    $content = str_replace('</a>', '', $content);
    $content = str_replace('  ', ' ', $content);
    $content = str_replace("\r\n", "\n", $content);
    $content = str_replace("\n\n", "\n", $content);
    $content = str_replace("\n", "", $content);
    $content = str_replace(";", "；", $content);
    $content = str_replace('?', '？', $content);
    $content = str_replace('&ldquo；', '“', $content);
    $content = str_replace('&rdquo；', '”', $content);
    $content = str_replace('(', '（', $content);
    $content = str_replace(')', '）', $content);
    $content = str_replace('!', '！', $content);
    $content = str_replace(',', '，', $content);
    $content = str_replace('&nbsp；</p>', '</p>', $content);
    $content = str_replace('<u>', '', $content);
    $content = str_replace('</u>', '', $content);
    $content = str_replace('&mdash；', '—', $content);
    $content = str_replace('&deg；', '℃', $content);
    $content = preg_replace('/<p>小编推荐：.+<\/p>/isU', '', $content);
    $content = preg_replace('/★★★★相关阅读：(.*)<\/p>/isU', '</p>', $content);
    $content = str_replace('<p>&nbsp；', '<p>', $content);
    $content = str_replace('<p></p>', '', $content);
    $content = str_replace('<p>　　', '<p>', $content);

    $pic_array = array();
    $pic = array();
    //if(preg_match_all('/(http:\/\/www\.wed114\.cn\/jiehun\/uploads\/allimg\/[\d]+\/[\d]+_[\d]+_[\d]+\.jpg)/isU', $content, $array)) {
    //    foreach($array[0] as $pic_url) {
    //        array_push($pic_array, $pic_url);
    //    }
    //}
    //if(preg_match_all('/(http:\/\/www\.wed114\.cn\/jiehun\/uploads\/allimg\/[\d]+\/[\d]+\-[\d]+\.jpg)/', $content, $array)) {
    //    array_push($pic_array, $array[0]);
    //}
    if(preg_match_all('/src="(\/jiehun\/uploads\/allimg\/[a-zA-Z0-9]+\/[a-zA-Z0-9\-\_]+\.[a-z]+)/', $content, $array)) {
        foreach($array[1] as $pic_url) {
            array_push($pic_array, $pic_url);
        }
    }
    if(preg_match_all('/(http:\/\/www\.wed114\.cn\/jiehun\/uploads\/allimg\/[a-z0-9]+\/[a-zA-Z0-9\-\_]+\.(jpg|png|jpeg|gif))/', $content, $array)) {
        foreach($array[0] as $pic_url) {
            array_push($pic_array, $pic_url);
        }
    }
        print_r($pic_array);
//exit();
        foreach($pic_array as $pic_url) {
            //sleep(1);
            $http_pic_url = $pic_url;
            if(substr($http_pic_url, 0, 1) == '/') {
                $http_pic_url = 'http://www.wed114.cn'.$pic_url;
            }
            $ret = http_get($http_pic_url, $header_array);

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
        }

    $sql = "INSERT INTO online_article_tmp(category_id, title, pub_time, url, pic, content, create_time, source, recommend, status) VALUES(1, '".
              $mysqli->real_escape_string($row['title'])."', '".
              $mysqli->real_escape_string($row['pub_time'])."', '".
              $mysqli->real_escape_string($row['url'])."', '".
              $mysqli->real_escape_string(json_encode($pic))."', '".
              $mysqli->real_escape_string($content)."', NOW(), 'web114结婚网', 0, 0)";
//echo $sql, "\n";
    $mysqli->query($sql);
//echo $content, "\n";
//exit();
}

$mysqli->close();
