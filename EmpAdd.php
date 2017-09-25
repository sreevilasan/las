<!DOCTYPE html>
<?php
	// File: EmpEdit.php 
	// 
	//error_log("test", 3, "C:\myfolder\php_errors.log");
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	$editor = $EmpId;
	$table = 'employee';
		
	// get data from GET variables
		$view = $_GET['view']; 
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//echo "Loaded via Posting method</br>";
		$view = $_POST['view'];	
	}
	
	if($view == "add") {
		
	}
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//echo "Loaded via Posting method</br>";
		//print_r($_POST);
		
		$db2 = new Database();
		
		// update employee database
		
		$db2->close();
	}
?>

<html>
	<head>
	<style>
		body {background-color: white;}
	</style>
	<link rel="stylesheet" href="css/manhour.css">
	<link rel="stylesheet" href="css/LasStyle.css">
	<script type="text/javascript">
		function quitWithoutSaving() {
			if (document.getElementById('dataModified').value == "true") {
				if (confirm("Data not saved. Do you really want to quit without saving modified values?") == true) {
					document.location = "EditEmployee.php"; // go to lasdb link
				} 	
			} else {
				document.location = "EditEmployee.php"; // go to employee main page
			}
		}
		
		function updateModifiedFlag(a){
			document.getElementById('dataModified').value = true;
		}
	</script>
	</head>
	
	<body onload=";">
	
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="employeeform" onsubmit=";"> 

		</form>
		
		<input type="hidden" name="dataModified" id="dataModified" value=false>';
		
		<table border="0" align="left">
			<tr>
				<td><button class="button button1" type="submit" form="employeeform" value="Save">Save</button></td>
				<td><button class="button button1" type="button" value="Quit" onclick="quitWithoutSaving();">Cancel</button></td>
			</tr>
		</table>
	</body>
</html>