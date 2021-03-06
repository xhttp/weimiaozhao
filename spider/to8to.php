<?php
include('/www/weimiaozhao/spider/base.php');

$base_url = 'http://www.to8to.com';

// 抓取某个指定的分类，全量就将这里设为空
$crawler_category = '';

$category_conf = array(
                       //array('url' => 'http://www.to8to.com/yezhu/xc_index.html', 'name' => '选材手册'), 
                       array('url' => 'http://www.to8to.com/yezhu/zxlc.html', 'name' => '装修流程'),
                       array('url' => 'http://www.to8to.com/yezhu/list-h9s4', 'name' => '装修风水'),
                       array('url' => 'http://www.to8to.com/life/hunjia/fagui/', 'name' => '婚姻法规'),
                       array('url' => 'http://www.to8to.com/life/hunjia/xisu/', 'name' => '婚嫁习俗'),
                       array('url' => 'http://www.to8to.com/life/hunjia/cehua/', 'name' => '婚礼策划'),
                       array('url' => 'http://www.to8to.com/life/hunjia/yongpin/', 'name' => '婚庆用品'),
                       );

$header = 'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
Accept-Encoding:gzip, deflate, sdch
Accept-Language:zh-CN,zh;q=0.8
Cache-Control:no-cache
Connection:keep-alive
Host:www.to8to.com
Referer:http://auto.cnfol.com/chezhuketang/20160316/22413444.shtml
Pragma:no-cache
Upgrade-Insecure-Requests:1
User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.82 Safari/537.36';

// list
$header_array = explode("\n", $header);

foreach($category_conf as $category_item) {

    $list_base_url = $category_item['url'];
    $category = $category_item['name'];

    if(!empty($crawler_category) && $category != $crawler_category) {
        continue;
    }

    // 先查表
    $list_page_no = 1;
    $list_url = '';
    while(TRUE) {
        $row_num = 0;
        $list_id = 0;
        $list_row_array = array();

        if($list_page_no == 1) {
            $list_url = $category_item['url'];
        }
    echo $list_url, "\n";    
        sleep(1);
        $ret = http_get($list_url, $header_array);
        $list_header = $ret['header'];
        $list_body   = $ret['body'];
        $list_info   = $ret['info'];

        // 重试一次列表页
        if(empty($list_body)) {
            echo $list_url, "\n";
            sleep(1);
            $ret = http_get($list_url, $header_array);
            if(empty($ret['body'])) {
                sleep(1);
                $ret = http_get($list_url, $header_array);
            }
            $list_header = $ret['header'];
            $list_body   = $ret['body'];
            $list_info   = $ret['info'];
        }

        if(preg_match_all('/<div class="list-item-title"><a target="_blank" href="([a-zA-Z0-9\/\.\_\-\:]+\.html)">/isU', $list_body, $list_row_array)) {
            $row_num = count($list_row_array[1]);
        }
//echo $list_body;
//print_r($list_row_array);exit();
        // 先比较列表页是否被抓取过
        $sql = "SELECT * FROM crawler_list WHERE url = '".$list_url."'";
        $result = $mysqli->query($sql);
        $list_row = $result->fetch_assoc();
        if(empty($list_row)) {
            $sql = "INSERT INTO crawler_list(url, category, page_no, row_num, request_headers, response_headers, 
                      http_info, status_code, crawler_time, crawler_ip, last_time, last_ip) VALUES('".
                      $mysqli->real_escape_string($list_url).
                      "', '".$mysqli->real_escape_string($category).
                      "', '".$mysqli->real_escape_string($list_page_no).
                      "', '".$mysqli->real_escape_string($row_num).
                      "', '".$mysqli->real_escape_string($header).
                      "', '".$mysqli->real_escape_string($list_header).
                      "', '".$mysqli->real_escape_string(json_encode($list_info)).
                      "', '".$mysqli->real_escape_string($list_info['http_code']).
                      "', NOW(), '".$mysqli->real_escape_string($wan_ip).
                      "', NOW(), '".$mysqli->real_escape_string($wan_ip)."')";
            $res = $mysqli->query($sql);
            $list_id = $mysqli->insert_id;
        } else {
            // update
            $sql = "UPDATE crawler_list SET last_time = NOW(), last_ip = '".$wan_ip."' WHERE id = ".$list_row['id'];
            $res = $mysqli->query($sql);
            $list_id = $list_row['id'];
        }

        // 检测该列表页下的详情页是否被抓取过
        foreach($list_row_array[1] as $detail_url) {
            // 详情页第一页 url
            if(strstr($detail_url, 'http')) {
                $detail_index_url  = $detail_url;
            } else {
                $detail_index_url  = $detail_url = $base_url.$detail_url;
            }

            $title = $content = '';
            $page_no = 1;
            $pub_time = date('0000-00-00 00:00:00');
            $article_url_list = array();
            while(TRUE) {
    echo $detail_url, "\n";
                sleep(1);
                array_push($article_url_list, $detail_url);
                $ret = http_get($detail_url, $header_array);
                if(empty($ret['body'])) {
                    sleep(1);
                    $ret = http_get($detail_url, $header_array);
                }
                $detail_header = $ret['header'];
                $detail_body   = $ret['body'];
                $detail_body = str_replace("\r", "", $detail_body);
                $detail_body = str_replace("\r", "", $detail_body);
                $detail_info   = $ret['info'];

                //
                if($page_no == 1) {
                    if(preg_match('/<h2 class="yezhu-zxcs-content-title">(.*)<\/h2>/isU', $detail_body, $detail_title_array)) {
                        $title = trim($detail_title_array[1]);
                        $title = str_replace("\r", "", $title);
                        $title = str_replace("\n", "", $title);
                    }
                    if(preg_match('/([\d]{4}-[\d]{2}-[\d]{2}) &nbsp;&nbsp;&nbsp;&nbsp;/isU', $detail_body, $detail_time_array)) {
                        $pub_time = $detail_time_array[1];
                    }
                }
                if(preg_match('/<div class="yezhu-zxcs-content">(.*)<div class="yezhu-zxcs-page-up">/isU', $detail_body, $detail_body_array)) {
                    $content .= $detail_body_array[1];
                }
//echo $content, "\n";
//exit();
                if(preg_match('/<a id="pagenav_next" href="(.*)">下一页<\/a>/isU', $detail_body, $detail_url_array)) {
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
                        $sql = "INSERT INTO crawler_article(list_id, title, pub_time, url, url_list, page_no, request_headers, 
                                  response_headers, http_info, status_code, pic,  
                                  content, crawler_time, crawler_ip, last_time, last_ip) VALUES('".
                                  $mysqli->real_escape_string($list_id)."', '".
                                  $mysqli->real_escape_string($title)."', '".
                                  $mysqli->real_escape_string($pub_time)."', '".
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
                        $sql = "UPDATE crawler_article SET title = '".$mysqli->real_escape_string($title)."', pub_time = '".$mysqli->real_escape_string($pub_time)."', http_info = '".$mysqli->real_escape_string(json_encode($detail_info))."', status_code = '".$mysqli->real_escape_string($detail_info['http_code'])."', content = '".$mysqli->real_escape_string($content)."', last_time = NOW(), last_ip = '".$wan_ip."' 
                                  WHERE id = ".$article_res['id'];
                        $res = $mysqli->query($sql);
                    }
     
                    // 退出，没有当前详情页的下一页了，抓取下一个详情页
                    break;
                }
            }
            //exit();
        }

        // 检测当前列表页是否还有下一页，没有就退出
        if(preg_match('/<a href="(\/[a-z0-9\_\-\/]+\.html)" class="next" id=nextpageid  >下一页/isU', $list_body, $list_next_array))    {
            $list_url = $base_url.$list_next_array[1];        
            $list_page_no++;
        } else {
            break;
        }
//print_r($list_next_array);
//echo $list_url, "\n";
//exit();
    }

}

$mysqli->close();
