<?php
include('/www/weimiaozhao/spider/base.php');

$base_url = 'http://life.yxlady.com/';


$header = 'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
Accept-Encoding:gzip, deflate, sdch
Accept-Language:zh-CN,zh;q=0.8
Cache-Control:no-cache
Connection:keep-alive
Host:life.yxlady.com
Pragma:no-cache
Upgrade-Insecure-Requests:1
User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.82 Safari/537.36';

// list
$header_array = explode("\n", $header);

$fp = fopen('/www/weimiaozhao/spider/yxlady.txt', 'r');

$list_id = 0;

while(!feof($fp)) {
    $detail_url = fgets($fp);
    $detail_url = trim($detail_url, "\n");

    if(empty($detail_url)) continue;

    /*
    $sql = "SELECT * FROM crawler_article WHERE url = '".$detail_url."'";
    $result = $mysqli->query($sql);
    $crawler_article_res = $result->fetch_assoc();
    if(!empty($crawler_article_res)) {
        continue;
    }
    */

            // 详情页第一页 url
            $detail_index_url = $detail_url;

            $title = $content = '';
            $page_no = 1;
            $article_url_list = array();
            while(TRUE) {
    echo $detail_url, "\n";
                sleep(1);
                array_push($article_url_list, $detail_url);
                $ret = http_get($detail_url, $header_array);
                $detail_header = $ret['header'];
                $detail_body   = $ret['body'];
                $detail_body = str_replace("\r", "", $detail_body);
                $detail_body = str_replace("\r", "", $detail_body);
                $detail_info   = $ret['info'];

                if(empty($detail_body)) {
                    sleep(1);
                    $ret = http_get($detail_url, $header_array);
                    $detail_header = $ret['header'];
                    $detail_body   = $ret['body'];
                    $detail_body = str_replace("\r", "", $detail_body);
                    $detail_body = str_replace("\r", "", $detail_body);
                    $detail_info   = $ret['info'];
                }

                //
                if($page_no == 1) {
                    if(preg_match('/<h1>(.*)<\/h1>/isU', $detail_body, $detail_title_array)) {
                        $title = trim($detail_title_array[1]);
                        $title = str_replace("\r", "", $title);
                        $title = str_replace("\n", "", $title);
                    }
                }
                if(preg_match('/<div class="ArtCon">(.*)<\/div><div id="f_gg_L2">/isU', $detail_body, $detail_body_array)) {
                    $content .= $detail_body_array[1];
                }
//echo $content, "\n";
//exit();
                if(preg_match('/<a class=\'nextpage\' href=\'(.*)\'>下一页<\/a>/isU', $detail_body, $detail_url_array)) {
                    $detail_url = $detail_url_array[1];
                    $page_no++;
//print_r($detail_url_array);
//exit();
                } else {
                    // 该详情页是否抓取过
                    $sql = "SELECT * FROM crawler_article WHERE url = '".$detail_index_url."'";
                    $result = $mysqli->query($sql);
                    $article_res = $result->fetch_assoc();
                    if(empty($article_res)) {
                        $sql = "INSERT INTO crawler_article(list_id, title, url, url_list, page_no, request_headers, 
                                  response_headers, http_info, status_code,  
                                  pic, content, crawler_time, crawler_ip, last_time, last_ip) VALUES('".
                                  $mysqli->real_escape_string($list_id)."', '".
                                  $mysqli->real_escape_string($title)."', '".
                                  $mysqli->real_escape_string($detail_index_url)."', '".
                                  $mysqli->real_escape_string(json_encode($article_url_list))."', '".
                                  $mysqli->real_escape_string($page_no)."', '".
                                  $mysqli->real_escape_string($header)."', '".
                                  $mysqli->real_escape_string($detail_header)."', '".
                                  $mysqli->real_escape_string(json_encode($detail_info))."', '".
                                  $mysqli->real_escape_string($detail_info['http_code'])."', '', '".
                                  $mysqli->real_escape_string($content)."', ".
                                  "NOW(), '".$wan_ip."', NOW(), '".$wan_ip."')";
                        $res = $mysqli->query($sql);
                    } else {
                        $sql = "UPDATE crawler_article SET title = '".$mysqli->real_escape_string($title)."', http_info = '".$mysqli->real_escape_string(json_encode($detail_info))."', status_code = '".$mysqli->real_escape_string($detail_info['http_code'])."', content = '".$mysqli->real_escape_string($content)."', last_time = NOW(), last_ip = '".$wan_ip."' 
                                  WHERE id = ".$article_res['id'];
                        $res = $mysqli->query($sql);
                    }
     
                    // 退出，没有当前详情页的下一页了，抓取下一个详情页
                    break;
                }
            }
            //exit();
        

//print_r($list_next_array);
//echo $list_url, "\n";
//exit();

}

$mysqli->close();
