<!DOCTYPE HTML> 
<!--	
	File: logOut.php
	Written on: 25-Aug-2017 by Sreevilasan K
	ModificationDate	Description 				ChangedBy
	26-Aug-2017			PDO inserted				Sreevilasan K
-->
<html>
<title></title>
<head></head>
<body>  
<?php 
	require 'include/header.php'; 
	session_start();
	session_destroy();
	header("location:Index.php");
	
	require 'include/footer.php'; 
?>
</body>
</html>