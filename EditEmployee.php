<!DOCTYPE html>
<!--
//  File EditEmployee.php
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
		<a href="EntityAddUpd.php?entityid=EMPLOYEE">Add Employee</a>
		<a href="EntitySearch.php?entityid=EMPLOYEE">Search Employee</a>
		<a href="EntityDisplay.php?entityid=EMPLOYEE">View Employee</a>
		<a href="EmpAllocateSeat.php">Allocate Seat</a>
		<a href="EmpRemove.php">Remove Employee</a>
		<a href="DbMain.php">Quit</a>
	</div>

<h2>Welcome to Employee Database</h2>
<p>Click on the menu to select.</p>

<script>
</script>

</body>
</html>
