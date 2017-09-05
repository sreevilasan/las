<?php
session_start();
$myusername = $_SESSION['username'];
if (!$_SESSION['username']) {
	header("location:login.php");
}
?>