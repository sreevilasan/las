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
<?php
			$entityaccess = new EntityAccess("project", $UserRole);
			if ($entityaccess->hasReadAccess()) {
?>
				<a href="EntityAddUpd.php?entityid=project">Add New Project</a>
				<a href="EntitySearch.php?entityid=project">View Project</a>
				<a href="EntitySearch.php?entityid=prjcat">View Project Category</a>
				<a href="EntitySearch.php?entityid=prjscope">View Project Scope</a>
				<a href="EntitySearch.php?entityid=prjtype">View Project Type</a>
				<a href="EntityAddUpd.php?entityid=prjcontact">Add Project Contacts</a>
<?php
			} else {
?>
				<a href="projectdetails.php">View Project</a>
<?php
			}
?>			
				<a href="#helpcom">Help</a>
			</div>
		</div> 
		
		<div class="dropdown">
			<button class="dropbtn" onclick="meraFunction('companyDropdown')">Company</button>
			<div class="dropdown-content" id="companyDropdown">
<?php
			$entityaccess = new EntityAccess("company", $UserRole);
			if ($entityaccess->hasReadAccess()) {
?>
				<a href="EntityAddUpd.php?entityid=company">Add New Company</a>
				<a href="EntitySearch.php?entityid=company">View Company</a>
				<a href="EntitySearch.php?entityid=comcat">View Company Category</a>
				<a href="EntityAddUpd.php?entityid=contact">Add Company Contact</a>
<?php
			} else {
?>
				<a href="companydetails.php">View Company</a>
<?php
			}
?>				
				<a href="#helpcom">Help</a>
			</div>
		</div> 
		
		<div class="dropdown">
			<button class="dropbtn" onclick="meraFunction('contactDropdown')">Contact</button>
			<div class="dropdown-content" id="contactDropdown">
<?php
			$entityaccess = new EntityAccess("contact", $UserRole);
			if ($entityaccess->hasReadAccess()) {
?>
				<a href="EntityAddUpd.php?entityid=contact">Add New Contact</a>
				<a href="EntitySearch.php?entityid=contact">View Contact</a>
				<a href="EntitySearch.php?entityid=prjcontact">Link Contact to Project</a>
<?php
			} else {
?>
				<a href="contactdetails.php">View Contact</a>
<?php
			}
?>	
				<a href="#helpcon">Help</a>
			</div>
		</div> 
		
		<div class="dropdown">
			<button class="dropbtn" onclick="meraFunction('documentDropdown')">Document</button>
			<div class="dropdown-content" id="documentDropdown">		
<?php
			$entityaccess = new EntityAccess("document", $UserRole);
			if ($entityaccess->hasReadAccess()) {
?>
				<a href="EntityAddUpd.php?entityid=document">Add New Document</a>	
				<a href="EntitySearch.php?entityid=document">View Document</a>	
				<a href="DocSent.php">Send Document</a>	
				<a href="DocReceive.php">Receive Document</a>	
				<a href="DocUpdate.php">Update Document</a>					
<?php
			} else {
?>
				<!--<a href="contactdetails.php">View Contact</a>-->
<?php
			}
?>
			</div>
		</div> 	
		
		<div class="dropdown">
			<button class="dropbtn" onclick="meraFunction('serviceDropdown')">Service</button>
			<div class="dropdown-content" id="serviceDropdown">
			
<?php
			$entityaccess = new EntityAccess("service", $UserRole);
			if ($entityaccess->hasReadAccess()) {
?>
				<a href="EntityAddUpd.php?entityid=service">Add New Service</a>
				<a href="EntitySearch.php?entityid=service">View Service</a>
				<a href="EntitySearch.php?entityid=servicedept">View Service Department</a>
				<a href="EntitySearch.php?entityid=servicelink">Link Service to Company</a>
			
<?php
			} else {
?>
				<!--<a href="contactdetails.php">View Contact</a>-->
<?php
			}
?>	
				<a href="#helpcon">Help</a>
			</div>
		</div> 
		

		<div class="dropdown">
			<button class="dropbtn" onclick="meraFunction('employeeDropdown')">Employee</button>
			<div class="dropdown-content" id="employeeDropdown">
<?php
			$entityaccess = new EntityAccess("employee", $UserRole);
			if ($entityaccess->hasReadAccess()) {
?>
				<a href="EntityAddUpd.php?entityid=employee">Add New Employee</a>
				<a href="EntitySearch.php?entityid=employee">View Employee</a>
				<a href="EntitySearch.php?entityid=department">View Department</a>
				<a href="SeatingArrangement.php">View Seating Arrangement</a>
<?php
			} else {
?>
				<a href="employeedetails.php">View Employee</a>
				<a href="SeatingArrangement.php">View Seating Arrangement</a>
<?php
			}
?>
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
		
		<div class="dropdown">
			<button class="dropbtn" onclick="meraFunction('assetDropdown')">Asset</button>
			<div class="dropdown-content" id="assetDropdown">
<?php
			$entityaccess = new EntityAccess("asset", $UserRole);
			if ($entityaccess->hasReadAccess()) {
?>
				<a href="assetdetails.php">Asset List</a>
				<a href="EntityAddUpd.php?entityid=asset">Add New Asset</a>
				<a href="EntitySearch.php?entityid=asset">View Asset</a>
				<a href="EntitySearch.php?entityid=assettype">View Asset Type</a>
				<a href="EntitySearch.php?entityid=assetsubtype">View Asset Subtype</a>
				<a href="EntitySearch.php?entityid=seat">View Seat Details</a>
<?php
			} else {
?>
				<a href="assetdetails.php">Asset List</a>
<?php
			}
?>
			</div>
		</div> 
	
		<div class="dropdown" <?php if (!(($UserRole == "DA") || ($UserRole == "DM"))) { echo "hidden";} ?>>
			<button class="dropbtn" onclick="meraFunction('reportDropdown')">Reports</button>
			<div class="dropdown-content" id="reportDropdown">
				<a href="MR-ProjectGrade.php">Project Manhour Cost</a> 
				<a href="MR-Summary.php">Manhour Summary</a>
				<a href="MR-EmployeeOvertime.php">Employee Manhours</a>
			</div>
		</div> 
		
		<div class="dropdown" <?php if (!(($UserRole == "DA") || ($UserRole == "DM"))) { echo "hidden";} ?>>
			<button class="dropbtn" onclick="meraFunction('DatabaseDropdown')">Database</button>
			<div class="dropdown-content" id="DatabaseDropdown">	
				<?php echo generateDatabaseMenu();	//generate phpcode for menu items ?>
			</div>
		</div>
		
		<div class="dropdown" <?php if (!($UserRole == "DA")) { echo "hidden";} ?>>
			<button class="dropbtn" onclick="meraFunction('DbaDropdown')">DBA</button>
			<div class="dropdown-content" id="DbaDropdown">	
				<?php echo generateDatabaseMenu(9);	//generate phpcode for menu items ?>
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
	if(s != 'documentDropdown') {
		document.getElementById('documentDropdown').classList.remove('show');
	}
	if(s != 'serviceDropdown') {
		document.getElementById('serviceDropdown').classList.remove('show');
	}
	
	if(s != 'reportDropdown') {
		document.getElementById('reportDropdown').classList.remove('show');
	}
	
	if(s != 'DatabaseDropdown') {
		document.getElementById('DatabaseDropdown').classList.remove('show');
	}
	if(s != 'DbaDropdown') {
		document.getElementById('DbaDropdown').classList.remove('show');
	}
	if(s != 'assetDropdown') {
		document.getElementById('assetDropdown').classList.remove('show');
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
		document.getElementById('documentDropdown').classList.remove('show');
		document.getElementById('serviceDropdown').classList.remove('show');
		document.getElementById('reportDropdown').classList.remove('show');
		document.getElementById('DatabaseDropdown').classList.remove('show');
		document.getElementById('DbaDropdown').classList.remove('show');
		document.getElementById('assetDropdown').classList.remove('show');
		document.getElementById('myDropdown').classList.remove('show');
	}

	myfunctionclicked = false;
}

</script>

</body>
</html>
