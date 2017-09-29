<!DOCTYPE html>
<!--
//  File DbMenu.php
//	Version 2.02
//	Author: Sreevilasan K.
//	Written on 14-Aug-2017
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
		require 'include/commonclass.php';
		require 'include/header.php'; 
		require 'include/footer.php';
	?>

	<div class="container">
		<a href="index.php">Home</a>
		
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
		
		<div class="dropdown">
			<button class="dropbtn" onclick="meraFunction('contactDropdown')">Contact</button>
			<div class="dropdown-content" id="contactDropdown">
				<a href="contactdetails.php">View Contact</a>
				<a href="#addcon">Add New Contact</a>
				<a href="#helpcon">Help</a>
			</div>
		</div> 
		
		<a href="#document">Document</a>
		<a href="#service">Service</a>

		<div class="dropdown">
			<button class="dropbtn" onclick="meraFunction('employeeDropdown')">Employee</button>
			<div class="dropdown-content" id="employeeDropdown">
				<a href="employeedetails.php">View Employee</a>
				<a href="AddEmployee.php">Add New Employee</a>
				<a href="SeatingArrangement.php">Seating Arrangement</a>
			</div>
		</div> 
		
		<div class="dropdown">
			<button class="dropbtn" onclick="meraFunction('manhourDropdown')">Manhour</button>
			<div class="dropdown-content" id="manhourDropdown">
				<a href="ManHours.php">Employee Manhours (Timesheet Entry)</a> 
				<a href="ManHours.php?view=approver">Approve Timesheet</a>
				<a href="TimesheetStatus.php">Timesheet Status</a>
				<a href="HolidayList.php">Holiday List</a>
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
		
		<div class="dropdown" <?php if ($UserRole != "DM") { echo "hidden";} ?>>
			<button class="dropbtn" onclick="meraFunction('DatabaseDropdown')">Database</button>
			<div class="dropdown-content" id="DatabaseDropdown">	
				<?php echo generateDatabaseMenu();	//generate phpcode for menu items ?>
			</div>
		</div>

	  <div class="dropdown">
		<button class="dropbtn" onclick="meraFunction('myDropdown')">Help</button>
		<div class="dropdown-content" id="myDropdown">
			<a href="doc/Employee Manhours.htm">Employee Manhours</a>
			<a href="AboutLasDatabase.php">About LAS Database</a>

		</div>
	  </div> 
		<a href="LogOut.php">Logout</a>
	</div>

<h2>Welcome to LAS Database</h2>
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
		
	if(s != 'manhourDropdown') {		
		document.getElementById('manhourDropdown').classList.remove('show');
	}
	
	if(s != 'projectDropdown') {		
		document.getElementById('projectDropdown').classList.remove('show');
	}
	
	if(s != 'companyDropdown') {
		document.getElementById('companyDropdown').classList.remove('show');
	}
	
	if(s != 'contactDropdown') {
		document.getElementById('contactDropdown').classList.remove('show');
	}
	
	if(s != 'reportDropdown') {
		document.getElementById('reportDropdown').classList.remove('show');
	}
	
	if(s != 'DatabaseDropdown') {
		document.getElementById('DatabaseDropdown').classList.remove('show');
	}
	if(s != 'myDropdown') {
		document.getElementById('myDropdown').classList.remove('show');
	}
}

// Close the dropdown if the user clicks outside of it

window.onclick = function(e) {

	if(!myfunctionclicked) {
		document.getElementById('employeeDropdown').classList.remove('show');
		document.getElementById('projectDropdown').classList.remove('show');
		document.getElementById('companyDropdown').classList.remove('show');
		document.getElementById('contactDropdown').classList.remove('show');
		document.getElementById('myDropdown').classList.remove('show');
	}

	myfunctionclicked = false;
}

</script>

</body>
</html>
