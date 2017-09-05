<!DOCTYPE html>
<?php
	// File: TimesheetStatus.php
	// 
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	$table = 'tssubmit';
	
	$db = new Database();	// open database

	$sql = 'SELECT tdate, status FROM ' . $table . ' where empid=' . $EmpId . ' ;';

	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	$noofdays = 30;
	$bs = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<h2>Timesheet Status for '. $myName . '</h2><br>';
	$i=0;
	foreach ($rows as $row)
	{
		if ($i == 0) {
			echo '<font color = "blue"><strong>';
			echo $bs."No." .$bs."From".$bs.$bs.$bs."To".$bs.$bs.$bs."Status";
			echo '</strong></font><br>';
			echo $bs."------------------------------------------------------------------------------------------<br>";
		}

		$fromDate = date("d-M-Y",strtotime($row['tdate']));
		
		$idate=$row['tdate'];
		$daysInMonth = cal_days_in_month(CAL_GREGORIAN,substr($idate, 5, 2),substr($idate,0, 4)) - 1;
		$idate = new DateTime($row['tdate']);
		$idate->add(new DateInterval('P'. $daysInMonth . 'D'));
		$toDate = $idate->format('d-M-Y');

		if ($row['status'] == "S") {
			$tsStatus = "Submitted for Approval";
		} else If ($row['status'] == "A") {
			$tsStatus = "Approved";
		} else If ($row['status'] == "R") {
			$tsStatus = "Send back for Updation";
		} else {
			$tsStatus = "Draft";
		}
		$i = $i +1;
		echo  $bs. $i . "." . $bs. $fromDate . $bs. $toDate. $bs. $tsStatus;
		echo '<br>';
		echo $bs."------------------------------------------------------------------------------------------<br>";
	}
	
	if ($i == 0) {
		echo '<br><font color="red" >No timesheet status available.</font>';
	}

	$db->close();	// Close database  
?>
</body>
</html> 
