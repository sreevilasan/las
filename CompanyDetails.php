<!DOCTYPE html>
<html>
<body>

<?php
	// File: CompanyDetails.php
	// 
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';

	$table = 'company';

	$db = new Database();	// open database
	
	$sql 	= 'SELECT * FROM ' . $table . ' order by ComId;';
	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}

	$bs = '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<h2>Company Details</h2><br>';
	
	foreach ($rows as $row)
	{
		echo ' <font color = "blue"><strong>' . $row['Catagory'] . "/ ". $row['ComNo']. '&nbsp;&nbsp;&nbsp;&nbsp;' . $row['Name'] . '</strong></font>';
		echo $bs . $row['Address'];
		if ($row['Telephone'] != "") {
			echo $bs . "Tel: " . $row['Telephone'];
		}
		If ($row['Email'] != "") {
			echo $bs . "Email: "  . $row['Email']; 
		}
		echo  "<br>----------------------------------------------------------------------------------------------------------<br>";
	}

	$db->close();	// Close database 
?>
</body>
</html> 
