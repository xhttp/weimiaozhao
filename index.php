<?php
include('/www/weimiaozhao/base.php');

$web_site = 'http://www.wmz.com';
$db_host = '127.0.0.1';
$db_user = 'admin';
$db_pass = '12345678';
$db_name = 'wmz';

// mysql
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
}
$mysqli->query("SET NAMES utf8");

$category_id = !empty($_GET['id']) ? intval($_GET['id']) : 0;
$page        = !empty($_GET['page']) ? intval($_GET['page']) : 1;

$size = 50;
$offset = ($page - 1) * $size;

$sql = "SELECT id, category_id, status, title, url, pic FROM online_article";
if($category_id > 0) {
    $sql .= " WHERE category_id = ".$category_id;
}
$sql .= " WHERE pic != '[]' ORDER BY id DESC LIMIT $offset, $size";

$result = $mysqli->query($sql);

$list = array();
while($row = $result->fetch_assoc()) {
    $row['pic'] = json_decode($row['pic'], TRUE);
    array_push($list, $row);
}

$weather_json = file_get_contents('http://www.wmz.com/weather?action=get');
$weather_data = json_decode($weather_json, TRUE);

include('templates/index.html');

