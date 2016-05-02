<?php
$action = isset($_POST['action']) ? strval($_POST['action']) : '';

if(empty($action)) {
    include('templates/login.html');
} else {
    $account  = isset($_POST['account']) ? strval($_POST['account']) : '';
	$password = isset($_POST['password']) ? strval($_POST['password']) : '';

    if(empty($account) || empty($password)) {
	    echo 'account password not empty.';
		exit();
	}

    include('config.php');
    if(ADMIN_ACCOUNT == $account && ADMIN_PASSWORD == $password) {
		session_start();
		$_SESSION['ADMIN'] = 1;
		header('Location: index.php');
	} else {
		echo 'account password error.';
		exit();
	}
}
