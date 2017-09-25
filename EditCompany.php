<!DOCTYPE html>
<!--
//  File EditContact.php
//	Version 2.02
//	Author: Sreevilasan K.
//	Written on 24-Sep-2017
-->
<html>
<head>
	<link rel="stylesheet" href="css/dbmain.css">
	<style>
		.copyright {
			position: absolute;
			bottom: 0;
		}
	</style>
</head>

<body bgcolor="white">
	<?php 
		require 'include/sessioncheck.php';
		require 'include/header.php'; 
		require 'include/footer.php';
	?>

	<div class="container">
		<a href="index.php">Home</a>
		<a href="EmpAdd.php">Add Company</a>
		<a href="EmpView.php">View Company</a>
		<a href="EmpSearch.php">Search Company</a>
		<a href="DbMain.php">Quit</a>
	</div>

<h2>Welcome to Company Database</h2>
<p>Click on the menu to select.</p>



</body>
</html>
