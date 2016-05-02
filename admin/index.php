<?php
//error_reporting(E_ALL);
session_start();
if(empty($_SESSION['ADMIN']) || $_SESSION['ADMIN'] == '') {
    header('Location: login.php');
    exit();
}

include('config.php');

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$size = 20;
$offset = ($page - 1) * $size;
$total = isset($_GET['total']) ? intval($_GET['total']) : 0;
//$pre_id = isset($_GET['pre_id']) ? intval($_GET['pre_id']) : 0;
$pre_id = 0;

$mysqli = new MySQLi(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqli->query("SET NAMES utf8");

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// 总数
if(empty($total)) {
	$sql = "SELECT count(*) AS total FROM online_article";
	$result = $mysqli->query($sql);
	$count = $result->fetch_assoc();
	$total = $count['total'];
	$result->free();
}

if(empty($pre_id)) {
    $sql = "SELECT id, category_id, title, pub_time, url, pic, create_time, source, recommend, status FROM online_article ORDER BY id DESC LIMIT $offset, $size";
} else {
    //$sql = "SELECT id, category_id, title, pub_time, url, pic, create_time, source, recommend, status FROM online_article WHERE id < $pre_id ORDER BY id DESC LIMIT $offset, $size";
}
$result = $mysqli->query($sql);
$list = array();
while($row = $result->fetch_assoc()) {
    array_push($list, $row);

    $pre_id = $row['id'];
}
$result->free();
$mysqli->close();

// 映射到模板
$total_page = ceil($total / $size);
include('templates/list.html');

