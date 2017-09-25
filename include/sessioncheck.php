<?php
	session_start();
	
	if (!$_SESSION['username']) {
		header("location:Login.php");
	}
	
	$myUsername = $_SESSION['username'];
	$myName = $_SESSION['Name'];
	$EmpId = $_SESSION['EmpId'];
	$UserRole = $_SESSION['UserRole'];
?>