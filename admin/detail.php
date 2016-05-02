<?php
session_start();
if(empty($_SESSION['ADMIN']) || $_SESSION['ADMIN'] == '') {
    exit();
}

include('config.php');

$id = !empty($_GET['id']) ? intval($_GET['id']) : 1;

$mysqli = new MySQLi(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqli->query("SET NAMES utf8");

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$sql = "SELECT * FROM online_article WHERE id = $id";
$result = $mysqli->query($sql);
$article = $result->fetch_assoc();
$result->free();
//$mysqli->close();

// pre next
$sql = "SELECT id FROM online_article WHERE id < $id ORDER BY id DESC LIMIT 1";
$result = $mysqli->query($sql);
$pre_array = $result->fetch_assoc();
$pre_article_id = $pre_array['id'];

$sql = "SELECT id FROM online_article WHERE id > $id ORDER BY id ASC LIMIT 1";
$result = $mysqli->query($sql);
$next_array = $result->fetch_assoc();
$next_article_id = $next_array['id'];

$mysqli->close();

// 映射到模板
include('templates/detail.html');

