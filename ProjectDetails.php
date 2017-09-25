<!DOCTYPE html>
<html>
<body>
<?php
	// File: ProjectDetails.php
	// 
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	$table = 'project';

	$db = new Database();	// open database

	$sql = "SELECT * FROM " . $table . " where catagory!='99' order by catagory asc, PrjNo desc;";
	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}

	$bs = '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<h2>Project Details</h2><br>';
	foreach ($rows as $row)
	{
		echo ' <font color = "blue"><strong>' . $row['Catagory'] . "/ ". $row['PrjNo']. '&nbsp;&nbsp;&nbsp;&nbsp;' . $row['Name'] . '</strong></font>';
		echo $bs . $row['Description'];
		echo $bs . $row['Location'] . "," . $row['Country']; 
		echo  "<br>---------------------------------------------------------------------------------------<br>";
	}

	$db->close();	// Close database  
?>
</body>
</html> 
