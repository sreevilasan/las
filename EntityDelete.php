<!DOCTYPE html>
<?php
	// File: EntityDelete.php 
	// 
	//error_log("test", 3, "C:\myfolder\php_errors.log");
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	// get data from GET variables
	$entityid = $_GET['entityid']; 
	$primarykey = $_GET['primarykey']; 
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//echo "Loaded via Posting method</br>";
		$entityid = $_POST['entityid'];
		$primarykey = $_POST['primarykey']; 
	}
	
	require 'include/GetEntityFields.php';

	$db = new Database();	// open database
	
	if ($primarykey != "") {
	
		$entitysql = "DELETE FROM " . $entityprimtable . " WHERE " . $entityprimcol . " = '" . $primarykey . "';";
		$row = $db->query($entitysql);
		
		if ($db->getError() != "") {
			echo $db->getError();
			exit();
		}		

	}

	$db->close();	// Close database  
?>

