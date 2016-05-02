<?php
$wan_ip = '127.0.0.1';
$db_host = '127.0.0.1';
$db_user = 'admin';
$db_pass = '12345678';
$db_name = 'wmz';

function http_get($url, $header, $charset='') {
    $ret = array('header' => '', 'body' => '', 'info' => '');
    // curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    //curl_setopt($ch, CURLOPT_COOKIE, "");
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    curl_setopt($ch, CURLOPT_URL, $url);
    $out_put = curl_exec($ch);
    $info = curl_getinfo($ch);
    if($info['http_code'] == 200) {
        $ret['header'] = substr($out_put, 0, $info['header_size']);
        $ret['body'] = substr($out_put, $info['header_size']);
        if(strstr($ret['header'], 'gzip')) {
            $ret['body'] = gzdecode($ret['body']);
        }
        if(strstr($ret['header'], 'text/html; charset=gb2312') 
             || strstr($ret['body'], 'text/html; charset=gb2312')) {
            $ret['body'] = mb_convert_encoding($ret['body'], 'utf-8', 'gb2312');
        } else if(strstr($ret['header'], 'text/html; charset=gbk') 
             || strstr($ret['body'], 'text/html; charset=gbk')) {
            $ret['body'] = mb_convert_encoding($ret['body'], 'utf-8', 'gbk');
        }

        //if($charset == 'gb2312') {
        //    $ret['body'] = mb_convert_encoding($ret['body'], 'utf-8', 'gb2312');
        //}
    }
    $ret['info'] = $info;

    curl_close($ch);

    return $ret;
}

// ip
$ret = http_get('http://1212.ip138.com/ic.asp', array('Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Encoding:gzip, deflate, sdch',
'Accept-Language:zh-CN,zh;q=0.8',
'Connection:keep-alive',
'Host:1212.ip138.com',
'Referer:http://www.ip138.com/',
'Upgrade-Insecure-Requests:1',
'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.82 Safari/537.36'));

if(preg_match('/\[([0-9\.]+)\]/isU', $ret['body'], $ip_array)) {
    $wan_ip = $ip_array[1];
}

// mysql
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
}
$mysqli->query("SET NAMES utf8");

