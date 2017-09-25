<!DOCTYPE html>
<!--
	// File: TimesheetStatus.php
	// 
-->
<?php
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	$fromDate=date("Y-m").'-01';
	$toDate = date("Y-m-d");

	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		$fromDate=$_POST['fromDate'];
		$toDate = $_POST['toDate'];	
	}	
?>

<html>
<head>
<link rel="stylesheet" href="css/LasStyle.css">

<script>
function loadFromToDate() {
	today = new Date();
	if (document.getElementById("fromDate").value == "") {
		document.getElementById("fromDate").valueAsDate = new Date(today.getFullYear(),today.getMonth(),2);
	} 

	if (document.getElementById("toDate").value == "") {
		document.getElementById("toDate").valueAsDate = today;
	}
}
</script>
</head>

<body onload="loadFromToDate();">
<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="TimesheetStatusForm">
	<table class="table10" border="0"><tr><th>
		From Date:<input type="date" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>">
	</th><th style="width:10px"></th><th>
		To Date:<input type="date" name="toDate" id="toDate" value="<?php echo $toDate; ?>">
	</th><th style="width:20px"></th><th>
		<button class="button button2" type="submit" form="TimesheetStatusForm" value="Run">CreateReport</button>
	</th></tr></table>
</form>

<body>
<?php
//echo "from=". $fromDate."  To=". $toDate . " empid=".$EmpId;
	$table = 'tssubmit';

	$db = new Database();	// open database

	$sql = 'SELECT tdate, status FROM ' . $table . ' where empid=' . $EmpId ;
	$sql .= ' AND tdate >= STR_TO_DATE("' . $fromDate . '","%Y-%m-%d") ';
	$sql .= 'AND tdate <= STR_TO_DATE("' . $toDate . '","%Y-%m-%d")';
//echo "sql=".$sql;

	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}

	$i=0;
	echo '<br>';
	foreach ($rows as $row)
	{
		if ($i == 0) {
			echo '<table class="table10" border="0"><tr><th height="30px">';
			echo '<h3>Timesheet Status for <font color="blue">'. $myName . '</font></h3>';
			echo '</th></tr></table>';
			echo '<table class="table10" border="1">';
			echo '<tr class="table10 table11">';
			echo '<th>No</th><th>From Date</th><th>To Date</th><th>Status</th></th></tr>';
		}

		$fromDate = date("d-M-Y",strtotime($row['tdate']));
		$toDate = getNewDate($row['tdate'],7,"d-M-Y");

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
		echo '<tr class="table10 table12"><td height="30px">'. $i .'</td><td>' . $fromDate . '</td><td>' . $toDate . '</td><td>'. $tsStatus .'</td></tr>';
	}
	
	if ($i == 0) {
		echo '<br><font color="red" ><b>No timesheet status available.</b></font>';
	}

	$db->close();	// Close database  
?>
</body>
</html> 
