<?php
function getTimeDiff($diffValue) {
    $minute =  60;
    $hour   = $minute * 60;
    $day    = $hour * 24;
    $halfamonth = $day * 15;
    $month = $day * 30;
    if($diffValue <= 0) {
        return '刚刚';
    }
    $monthC = $diffValue / $month;
    $weekC  = $diffValue / (7*$day);
    $dayC   = $diffValue / $day;
    $hourC  = $diffValue / $hour;
    $minC   = $diffValue / $minute;
    if($monthC >= 1) {
        $result = intval($monthC) + "个月前";
    } else if($weekC >= 1) {
        $result = intval($weekC) + "周前";
    } else if($dayC >= 1) {
        $result = intval($dayC) + "天前";
    } else if($hourC >= 1) {
        $result = intval($hourC) + "小时前";
    } else if($minC >= 1) {
        $result = intval($minC) + "分钟前";
    } else {
        $result = intval($diffValue) + "秒前";
    };

    return $result;
}

