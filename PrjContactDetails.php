<!DOCTYPE html>
<html>
<body>
<?php
	// File: PrjContactDetails.php
	// 
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	$table1 = 'prjcontact';
	$table2 = 'project';
	$table3 = 'contact';

	$db = new Database();	// open database
	
	$sql = 'SELECT * FROM ' . $table1 . ', ' . $table2 . ', ' . $table3 ;
	$sql .= ' where ' . $table1 . '.PrjId=' . $table2 . '.PrjId' . ' and ' . $table1 . '.ContactId=' . $table3 . '.ContactId;';
	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	echo '<h2>Project Contact Details</h2><br>';
	$bs = '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	foreach ($rows as $row)
	{
		echo ' <font color = "blue"><strong>' . $row['ContactId'] . ". " . '&nbsp;&nbsp;&nbsp;&nbsp;' . $row['Name'] . '</strong></font>';
		if ($row['OfficeAddress'] != "") {
			echo $bs . $row['OfficeAddress'];
		}
		if ($row['Mobile'] != "") {
			echo $bs . "Mob: " . $row['Mobile'];
		}
		if ($row['Telephone'] != "") {
			echo $bs . "Tel: " . $row['Telephone'];
		}
		If ($row['Email'] != "") {
			echo $bs . "Email: "  . $row['Email']; 
		}
		echo $bs . "Project: " . $row[8] . '&nbsp;&nbsp;&nbsp;&nbsp;' . "(" . $row['Division'] . ")" ;
		echo  "<br>----------------------------------------------------------------------------------------------------------<br>";
	}

	$db->close();	// Close database  
?>
</body>
</html> 
