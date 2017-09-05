<?php
	// File: ActivityDropdown.php
	// 

//	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
//	require 'include/Header.php';
	
	$table1 = 'deptact';
	$table2 = 'activity';
	
	// get deptid from request get variables
	$deptId=$_GET['deptId']; 
	
	$db = new Database();	// open database

	$sql = "select " . $table1 . ".actId as actNo, activity from " . $table1 . ", " . $table2 . " where " . $table1 . ".deptId=" . $deptId . " and " . $table1 . ".actId=" . $table2 . ".actId ;"; 

	$rows = $db->select($sql);
	
	echo '<option disabled selected></option>';		
	if ($db->getError() != "") {
		// echo $db->getError();		
		// exit();
	}
	foreach ($rows as $row) {
		echo '<option value="' . $row['actNo'] . '">' . $row['activity'] . '</option>';
	}
			
	$db->close(); 	// close database connection

/*
echo '<option disabled selected></option>';
echo '<option value="1">Visit</option>';
echo '<option value="2">Report</option>';
echo '<option value="3">Visit</option>';
echo '<option value="4">Calculation</option>';
echo '<option value="5">BOQ</option>';
echo '<option value="6">Drawing</option>';
echo '<option value="7">Isometric</option>';
echo '<option value="8">Layout</option>';
echo '<option value="9">Planning</option>';
echo '<option value="10">Approval</option>';
echo '<option value="11">Review</option>';
echo '<option value="12">Support</option>';
echo '<option value="13">Specification</option>';
echo '<option value="14">RCC</option>';
echo '<option value="15">Structure</option>';
*/

?>