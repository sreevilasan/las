<!DOCTYPE html>
<?php
//  File DbMenu.php
//
session_start();
$myusername = $_SESSION['username'];
if (!$_SESSION['username']) {
	header("location:Login.php");
}
?>
<html>
<head>
	<link rel="stylesheet" href="css/dbmain.css">
</head>

<body align="center" bgcolor="pink">
	<?php require 'include/header.php'; ?>
	
	<br><br><br>
	<img src="images/LasDatabase.jpg" height="120" width="360">
	<br>
	<h2>Version 2.02</h2>
	<b>A web based software for Engineering Consultants</b>
	<br><br>
	<textarea rows="5" cols="33" align="left">In case any suggestions or difficulties with software, please contact Sreevilasan K, Mob: 9820352258 
	</textarea>
	<br><br><br><br>
	© 2017 LAS Engineers & Consultants Pvt Ltd. All rights reserved.
	<br><br>

</body>
</html>