<!DOCTYPE html>
<!--
//  File EditProject.php
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
		<a href="PrjView.php">View Project</a>
		<a href="PrjAdd.php">Add Project</a>
		<a href="PrjAdd.php">Add Project Contacts</a>

		<a href="PrjSearch.php">Search Project</a>
		<a href="DbMain.php">Quit</a>
		
		<!--
		<div class="dropdown">
			<button class="dropbtn" onclick="meraFunction('projectDropdown')">Project</button>
			<div class="dropdown-content" id="projectDropdown">
				<a href="projectdetails.php">View Project</a>
				<a href="#addprj">Add New Project</a>
				<a href="PrjContactDetails.php">Project Contact</a>
			</div>
		</div> 
		
		<div class="dropdown">
			<button class="dropbtn" onclick="meraFunction('companyDropdown')">Company</button>
			<div class="dropdown-content" id="companyDropdown">
				<a href="companydetails.php">View Company</a>
				<a href="#addcom">Add New Company</a>
				<a href="#helpcom">Help</a>
			</div>
		</div> 

		<div class="dropdown" <?php if ($UserRole != "DM") { echo "hidden";} ?>>
			<button class="dropbtn" onclick="meraFunction('reportDropdown')">Reports</button>
			<div class="dropdown-content" id="reportDropdown">
				<a href="MR-ProjectGrade.php">Project Manhour Cost</a> 
				<a href="MR-Summary.php">Manhour Summary</a>
				<a href="MR-EmployeeOvertime.php">Employee Manhours</a>
			</div>
		</div> 

	  <div class="dropdown">
		<button class="dropbtn" onclick="meraFunction('myDropdown')">Help</button>
		<div class="dropdown-content" id="myDropdown">
			<a href="doc/Employee Manhours.htm">Employee Manhours</a>
			<a href="AboutLasDatabase.php">About LAS Database</a>

		</div>
	  </div> 
	 -->

	</div>

<h2>Welcome to Project Database</h2>
<p>Click on the menu to select.</p>

<script>
/* When the user clicks on the button, 
toggle between hiding and showing the dropdown content */

var myfunctionclicked;
function meraFunction(s) { 
	myfunctionclicked = true;
	document.getElementById(s).classList.toggle("show");
	if(s != 'employeeDropdown') {		
		document.getElementById('employeeDropdown').classList.remove('show');
	}
	if(s != 'myDropdown') {
		document.getElementById('myDropdown').classList.remove('show');
	}
}

// Close the dropdown if the user clicks outside of it

window.onclick = function(e) {

	if(!myfunctionclicked) {
		document.getElementById('employeeDropdown').classList.remove('show');
		document.getElementById('myDropdown').classList.remove('show');
	}

	myfunctionclicked = false;
}

</script>

</body>
</html>
