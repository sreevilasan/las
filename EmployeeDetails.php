<!DOCTYPE html>
<html>
<body>

<?php
	// File: EmployeeDetails.php
	// 
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';

	$table = 'employee';

	$db = new Database();	// open database

	$sql = 'SELECT * FROM ' . $table . ' order by EmpID;';
	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	echo '<h2>Employee Details</h2><br>';
	$bs = '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	
	foreach ($rows as $row)
	{
		echo ' <font color = "blue"><strong>' . $row['EmpId'] . ". ". $row['Name']. '</strong></font>';
		echo $bs . $row['Designation'];
		echo $bs . $row['LocalAddress'];
		echo $bs . 'Mob: ' . $row['Mobile']. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$email = $row['OfficialEmail'];
		if ($email == "") {
			$email = $row['PersonalEmail'];
		}
		echo 'Email: ' . $email;
			
		//echo $bs . $row['DOB'];    
		echo  "<br>---------------------------------------------------------------------------------------<br>";
	}

	$db->close();	// Close database  
?>
</body>
</html> 
