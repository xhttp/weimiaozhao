<?php
include('/www/weimiaozhao/spider/base.php');

$base_url = 'http://www.wed114.cn';
//$list_base_url = 'http://www.wed114.cn/jiehun/shenghuomiaozhao/';
//$category = '生活妙招';

// 抓取某个指定的分类，全量就将这里设为空
$crawler_category = '';

$category_conf = array(array('url' => 'http://www.wed114.cn/jiehun/shenghuomiaozhao/', 'name' => '生活妙招'),
                       array('url' => 'http://www.wed114.cn/jiehun/qiche/', 'name' => '汽车'),
                       array('url' => 'http://www.wed114.cn/jiehun/zhuangxiufengge/', 'name' => '装修风格'), 
                       array('url' => 'http://www.wed114.cn/jiehun/diban/', 'name' => '地板'),
                       array('url' => 'http://www.wed114.cn/jiehun/zhiwu/', 'name' => '植物'),
                       array('url' => 'http://www.wed114.cn/jiehun/weishengjian/', 'name' => '卫生间'), 
                       array('url' => 'http://www.wed114.cn/jiehun/chufang/', 'name' => '厨房'), 
                       array('url' => 'http://www.wed114.cn/jiehun/woshi/', 'name' => '卧室'), 
                       array('url' => 'http://www.wed114.cn/jiehun/keting/', 'name' => '客厅'), 
                       array('url' => 'http://www.wed114.cn/jiehun/canting/', 'name' => '餐厅'), 
                       array('url' => 'http://www.wed114.cn/jiehun/huayudaquan/', 'name' => '花语大全'),
                       array('url' => 'http://www.wed114.cn/jiehun/beiyunriji/', 'name' => '备孕日记'),
                       array('url' => 'http://www.wed114.cn/jiehun/yunqianjiancha/', 'name' => '孕前检查'), 
                       array('url' => 'http://www.wed114.cn/jiehun/zhuyunfangfa/', 'name' => '助孕方法'), 
                       array('url' => 'http://www.wed114.cn/jiehun/fukejibing/', 'name' => '妇科疾病'), 
                       array('url' => 'http://www.wed114.cn/jiehun/yunqishipu/', 'name' => '孕期食谱'), 
                       array('url' => 'http://www.wed114.cn/jiehun/taitingliuchan/', 'name' => '胎停流产'), 
                       array('url' => 'http://www.wed114.cn/jiehun/nanxingbeiyun/', 'name' => '男性备孕'), 
                       array('url' => 'http://www.wed114.cn/jiehun/yunfutaijiao/', 'name' => '孕妇胎教'), 
                       array('url' => 'http://www.wed114.cn/jiehun/shengnanshengnv/', 'name' => '生男生女'), 
                       array('url' => 'http://www.wed114.cn/jiehun/yunjingfenxiang/', 'name' => '孕经分享'), 
                       array('url' => 'http://www.wed114.cn/jiehun/zaojiaojiaoliu/', 'name' => '早教交流'), 
                       array('url' => 'http://www.wed114.cn/jiehun/baobeijiankang/', 'name' => '宝贝健康'), 
                       array('url' => 'http://www.wed114.cn/jiehun/baobeishipu/', 'name' => '宝贝食谱'), 
                       array('url' => 'http://www.wed114.cn/jiehun/baobaoyongpin/', 'name' => '宝宝用品'), 
                       array('url' => 'http://www.wed114.cn/jiehun/chanhouhuifu/', 'name' => '产后恢复'),
                       array('url' => 'http://www.wed114.cn/jiehun/baobeihuli/', 'name' => '宝贝护理'), 
                       array('url' => 'http://www.wed114.cn/jiehun/taierfayu/', 'name' => '胎儿发育'), 
                       array('url' => 'http://www.wed114.cn/jiehun/yunfujiemengdaquan/', 'name' => '孕妇解梦大全'),
                       array('url' => 'http://www.wed114.cn/jiehun/qunzi/', 'name' => '裙子'), 
            array('url' => 'http://www.wed114.cn/jiehun/shishangnvzhuangwaitao/', 'name' => '时尚女装外套 '),
            array('url' => 'http://www.wed114.cn/jiehun/kuzi/', 'name' => '裤子'),
            array('url' => 'http://www.wed114.cn/jiehun/shishangnvzhuangshangyi/', 'name' => '时尚女装上衣'),
            array('url' => 'http://www.wed114.cn/jiehun/shishangxiezi/', 'name' => '时尚鞋子'), 
            array('url' => 'http://www.wed114.cn/jiehun/maozidapei/', 'name' => '帽子搭配'), 
            array('url' => 'http://www.wed114.cn/jiehun/lifu/', 'name' => '礼服'), 
            array('url' => 'http://www.wed114.cn/jiehun/shishangbaobao/', 'name' => '时尚包包'));

$header = 'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
Accept-Encoding:gzip, deflate, sdch
Accept-Language:zh-CN,zh;q=0.8
Cache-Control:no-cache
Connection:keep-alive
Host:www.wed114.cn
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
            $list_url = $list_base_url;
        }
    echo $list_url, "\n";    
        sleep(1);
        $ret = http_get($list_url, $header_array);
        $list_header = $ret['header'];
        $list_body   = $ret['body'];
        $list_info   = $ret['info'];

        if(preg_match_all('/<h3><a target="_blank" href="(.*)">(.*)<\/a><\/h3>/isU', $list_body, $list_row_array)) {
            $row_num = count($list_row_array[1]);
        }

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
            $detail_index_url = $detail_url = $base_url.$detail_url;

            // 该详情页是否抓取过
            $sql = "SELECT * FROM crawler_article WHERE url = '".$detail_index_url."'";
            $result = $mysqli->query($sql);
            $article_res = $result->fetch_assoc();
            if(!empty($article_res)) {
                continue;
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
                $detail_header = $ret['header'];
                $detail_body   = $ret['body'];
                $detail_info   = $ret['info'];

                //
                if($page_no == 1) {
                    if(preg_match('/<h1>(.*)<\/h1>/isU', $detail_body, $detail_title_array)) {
                        $title = $detail_title_array[1];
                    }
                    if(preg_match('/<span class="writor">发布时间：([0-9\-]+)<\/span>/', $detail_body, $detail_time_array)) {
                        $pub_time = $detail_time_array[1];
                    }
                    if(preg_match('/<p class="jjxq">(.*)<\/p>/isU', $detail_body, $detail_body_array)) {
                        $content .= '<p>'.$detail_body_array[1].'</p>';
                    }
                }
                if(preg_match('/<div class="substance">(.*)<\/div>/isU', $detail_body, $detail_body_array)) {
                    $content .= $detail_body_array[1];
                }

                if(preg_match('/<a href=\'([\d]+\_[\d]+\.html)\'>下一页<\/a>/isU', $detail_body, $detail_url_array)) {
                    $detail_url = $list_base_url.$detail_url_array[1];
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
        if(preg_match('/<li><a href=\'(list\_[\d]+\_[\d]+\.html)\'>下一页<\/a><\/li>/isU', $list_body, $list_next_array))    {
            $list_url = $list_base_url.$list_next_array[1];        
            $list_page_no++;
        } else {
            break;
        }

        //exit();
    }

}

$mysqli->close();
