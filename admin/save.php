<?php
session_start();
if(empty($_SESSION['ADMIN']) || $_SESSION['ADMIN'] == '') {
    exit();
}

include('config.php');

$id          = intval($_POST['id']);
$category_id = intval($_POST['category_id']);
$title       = strval($_POST['title']);
$pub_time    = strval($_POST['pub_time']);
$url         = strval($_POST['url']);
$pic         = strval($_POST['pic']); 
$content     = strval($_POST['content']);
$create_time = strval($_POST['create_time']);
$source      = intval($_POST['source']);
$recommend   = intval($_POST['recommend']);
$status      = intval($_POST['status']);

$content = str_replace("\r", "", $content);
$content = str_replace("\n", "", $content);

$mysqli = new MySQLi(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqli->query('set names utf8');
/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$sql = "UPDATE online_article SET category_id = '".$mysqli->real_escape_string($category_id)."', 
               title = '".$mysqli->real_escape_string($title)."', 
               pub_time = '".$mysqli->real_escape_string($pub_time)."', 
               url = '".$mysqli->real_escape_string($url)."', 
               pic = '".$mysqli->real_escape_string($pic)."', 
               content = '".$mysqli->real_escape_string($content)."', 
               create_time = '".$mysqli->real_escape_string($create_time)."', 
               source = '".$mysqli->real_escape_string($source)."', 
               recommend = '".$mysqli->real_escape_string($recommend)."', 
               status = '".$mysqli->real_escape_string($status)."' WHERE id = ".$id;
$mysqli->query($sql);

$mysqli->close();

header('Location: detail.php?id='.$id);

