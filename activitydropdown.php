<?php
	// File: ActivityDropdown.php
	// 

	require 'include/commonclass.php';
	
	$table1 = 'deptact';
	$table2 = 'activity';
	
	// get deptid from request get variables
	$deptId=$_GET['deptId']; 
	
	$db = new Database();	// open database

	$sql = "select " . $table1 . ".actId as actNo, activity from " . $table1 . ", " . $table2 . " where " . $table1 . ".deptId=" . $deptId . " and " . $table1 . ".actId=" . $table2 . ".actId order by activity;"; 

	$rows = $db->select($sql);
	
	echo '<option disabled selected></option>';		
	if ($db->getError() != "") {
		// echo $db->getError();		
		// exit();
	}
	foreach ($rows as $row) {
		// key="p-d-a"; if (key in keyArray) skip next statement
		echo '<option value="' . $row['actNo'] . '">' . $row['activity'] . '</option>';
	}
			
	$db->close(); 	// close database connection

?>