<?php
include('/www/weimiaozhao/spider/base.php');

$base_url = 'http://health.66wz.com';

// 抓取某个指定的分类，全量就将这里设为空
$crawler_category = '';

$category_conf = array(array('url' => 'http://health.66wz.com/ysbj/', 'name' => '养生保健', 'max' => 163, 'min' => 100, 'path' => 'http://health.66wz.com/system/count/0005036/000000000000/000/000/c0005036000000000000_000000'),
                       //array('url' => 'http://health.66wz.com/ys/', 'name' => '饮食', 'max' => 38, 'min' => 35, 'path' => 'http://health.66wz.com/system/count/0005029/000000000000/000/000/c0005029000000000000_0000000'),
                       //array('url' => 'http://health.66wz.com/aml/pfmr/', 'name' => '皮肤美容', 'max' => 20, 'min' => 17, 'path' => 'http://health.66wz.com/system/count/0005009/002000000000/000/000/c0005009002000000000_0000000'), 
                       //array('url' => 'http://health.66wz.com/aml/mbmr/', 'name' => '面部美容', 'max' => 31, 'min' => 28, 'path' => 'http://health.66wz.com/system/count/0005009/001000000000/000/000/c0005009001000000000_0000000'), 
                       //array('url' => 'http://health.66wz.com/aml/xtmr/', 'name' => '型体美容', 'max' => 19, 'min' => 16, 'path' => 'http://health.66wz.com/system/count/0005009/004000000000/000/000/c0005009004000000000_0000000'),
                       array('url' => 'http://health.66wz.com/jfss/yqdjfssf/', 'name' => '减肥塑身知识', 'max' => 16, 'min' => 13, 'path' => 'http://health.66wz.com/system/count/0005004/003000000000/000/000/c0005004003000000000_0000000'),
                       //array('url' => 'http://health.66wz.com/jfss/jfssffqjc/', 'name' => '减肥塑身法', 'max' => 31, 'min' => 28, 'path' => 'http://health.66wz.com/system/count/0005004/004000000000/000/000/c0005004004000000000_0000000'),
                       //array('url' => 'http://health.66wz.com/jfss/wyjfssty/', 'name' => '减肥塑身全体验', 'max' => 4, 'min' => 1, 'path' => 'http://health.66wz.com/system/count//0005004/002000000000/000/000/c0005004002000000000_00000000'),
                       );

$header = 'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
Accept-Encoding:gzip, deflate, sdch
Accept-Language:zh-CN,zh;q=0.8
Cache-Control:no-cache
Connection:keep-alive
Host:health.66wz.com
Pragma:no-cache
Upgrade-Insecure-Requests:1
User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.82 Safari/537.36';

// list
$header_array = explode("\n", $header);

foreach($category_conf as $category_item) {

    $list_base_url = $category_item['url'];
    $category = $category_item['name'];

    $list_page_max = $category_item['max'];
    $list_page_min = $category_item['min'];
    $list_base_path = $category_item['path'];

    if(!empty($crawler_category) && $category != $crawler_category) {
        continue;
    }

    // 先查表
    $list_page_no = 1;
    $list_url = '';
    //while(TRUE) {
    for($list_page_no=$list_page_max; $list_page_no > $list_page_min; $list_page_no--) {

        $row_num = 0;
        $list_id = 0;
        $list_row_array = array();

        if($list_page_no == $list_page_max) {
            $list_url = $list_base_url;
        } else {
            $list_url = $list_base_path.$list_page_no.'.shtml';
        }
    echo $list_url, "\n";    
        sleep(1);
        $parse_list_url = parse_url($list_url);
        $header_array[5] = 'host:'.$parse_list_url['host'];
        $ret = http_get($list_url, $header_array, 'gh2312');
        if(empty($ret['body'])) {
            sleep(1);
            $ret = http_get($list_url, $header_array, 'gb2312');
        }
        $list_header = $ret['header'];
        $list_body   = $ret['body'];
        $list_info   = $ret['info'];

        $list_body = str_replace("\r", "", $list_body);
        $list_body = str_replace("\n", "", $list_body);
        if(preg_match_all('/<img src="http:\/\/health\.66wz\.com\/images\/di2\.jpg" width="11" height="15" align="middle"><\/td><td ><a href="(http:\/\/[a-z0-9\/\.]+\.shtml)"([\s]{1,4})target="_blank" >/isU', $list_body, $list_row_array)) {
            $row_num = count($list_row_array[1]);
        } else if(preg_match_all('/<img src="http:\/\/health\.66wz\.com\/images\/di2\.jpg" width="11" height="15" align="middle"><\/td><td  ><a  href="(http:\/\/[a-z0-9\/\.]+\.shtml)"   target="_blank" >/isU', $list_body, $list_row_array)) {
            $row_num = count($list_row_array[1]);
        }
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
            $detail_index_url = $detail_url;

            $title = $content = '';
            $page_no = 1;
            $pub_time = date('0000-00-00 00:00:00');
            $article_url_list = array();
            while(TRUE) {
    echo $detail_url, "\n";
                sleep(1);
                array_push($article_url_list, $detail_url);
                $parse_detail_url = parse_url($detail_url);
                $header_array[5] = $parse_detail_url['host'];
                $ret = http_get($detail_url, $header_array, 'gb2312');
                if(empty($ret['body'])) {
                    sleep(1);
                    $ret = http_get($detail_url, $header_array, 'gb2312');
                }
                $detail_header = $ret['header'];
                $detail_body   = $ret['body'];
                $detail_info   = $ret['info'];
                //
                if($page_no == 1) {
                    if(preg_match('/<h1 id="artibodytitle">(.*)<\/h1>/isU', $detail_body, $detail_title_array)) {
                        $title = $detail_title_array[1];
                    } else if(preg_match('/<h1  class="biaoti" id="artibodytitle">(.*)<\/h1>/isU', $detail_body, $detail_title_array)) {
                        $title = $detail_title_array[1];
                    } else if(preg_match('/<td height="40" align="center" bgcolor="#EEEEEE" class="biaoti">(.*)<\/td>/isU', $detail_body, $detail_title_array)) {
                         $title = $detail_title_array[1];
                    } else if(preg_match('/<div id="title_1">(.*)<\/div>/isU', $detail_body, $detail_title_array)) {
                         $title = $detail_title_array[1];
                    }

                    if(preg_match('/<span id="pub_date">([\d]{4}年[\d]{2}月[\d]{2}日 [\d]{2}:[\d]{2}:[\d]{2})<\/span>/isU', $detail_body, $detail_time_array)) {
                        $pub_time = $detail_time_array[1];
                        $pub_time = str_replace('年', '-', $pub_time);
                        $pub_time = str_replace('月', '-', $pub_time);
                        $pub_time = str_replace('日', '', $pub_time);
                    } else if(preg_match('/<span id="pub_date">([\d]{4}-[\d]{2}-[\d]{2} [\d]{2}:[\d]{2})<\/span>/', $detail_body, $detail_time_array)) {
                        $pub_time = $detail_time_array[1];
                    } else if(preg_match('/<div id="Content_7">([\d]{4}-[\d]{2}-[\d]{2} [\d]{2}:[\d]{2})/', $detail_body, $detail_time_array)) {
                        $pub_time = $detail_time_array[1];
                    }
                }

                if(preg_match('/<div class="artibody" id="ftcg">(.*)<\/div>/isU', $detail_body, $detail_body_array)) {
                    $content .= $detail_body_array[1];
                } else if(preg_match('/<div id="ftcg" class="content">(.*)<\/div>/isU', $detail_body, $detail_body_array)) {
                    $content .= $detail_body_array[1];
                } else if(preg_match('/<td class="artibody">(.*)<td><table width="95%" border="0"/isU', $detail_body, $detail_body_array)) {
                    $content .= $detail_body_array[1];
                } else if(preg_match('/<div id="Content_8">(.*)<\/div>/isU', $detail_body, $detail_body_array)) {
                    $content .= $detail_body_array[1];
                }

                if(preg_match('/<a href= (http:\/\/[a-z0-9\/\_]+\.shtml)>下一页/isU', $detail_body, $detail_url_array)) {
                    $detail_url = $detail_url_array[1];
                    $page_no++;
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
                        $sql = "UPDATE crawler_article SET title = '".$mysqli->real_escape_string($title)."', pub_time = '".$mysqli->real_escape_string($pub_time)."', response_headers = '".$mysqli->real_escape_string(json_encode($detail_header))."', http_info = '".$mysqli->real_escape_string(json_encode($detail_info))."', status_code = '".$mysqli->real_escape_string($detail_info['http_code'])."', content = '".$mysqli->real_escape_string($content)."', last_time = NOW(), last_ip = '".$wan_ip."' 
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
        /*
        if(preg_match('//isU', $list_body, $list_next_array))    {
            $list_url = $base_url.$list_next_array[1];        
            $list_page_no++;
        } else {
            break;
        }
        */
        //exit();
    }

}

$mysqli->close();
