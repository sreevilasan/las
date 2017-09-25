<!DOCTYPE html>
<?php
	// File: Manhours.php 
	// 
	//error_log("test", 3, "C:\myfolder\php_errors.log");
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	

	$approver = $EmpId;
	
	$table = 'empmh';
		
	// get date from request get variables
	$actDate=$_GET['actDate']; 
	$startDate=$_GET['startDate']; 
	$month = "";
	$year = "";
	$day = "";
	
	$view = $_GET['view']; 
	
	if($view == "approver") {
		
		$sql3 = "select empid, name from employee where manager = " . $approver . " and empid != " . $approver . ";";
		
		$db3 = new Database();
		$emprows = $db3->select($sql3);
		if ($db3->getError() != "") {
			echo $db3->getError();
			exit();
		}

		$empcount = 0;
		$EmpId = "";
		foreach ($emprows as $emprow) {
			//echo '<option value="' . $emprow['empid'] . '">' . $emprow['name'] . '</option>';
			$emparray[$empcount] = $emprow['empid'];
			$empcount = $empcount + 1;
			if($emprow['empid'] == $_GET['EmpId']) {
				$EmpId=$_GET['EmpId']; 
			}
		}
		$db3->close();
		
		if($_GET['EmpId'] == "" || $EmpId == "") {
			$EmpId = $emparray[0]; 
		}
		
		if ($EmpId == "") {
			echo '<br><b><font color="red">No timesheets to approve</font></b>';
			exit();
		}
		
	}

	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		echo "Loaded via Posting method</br>";
		$actDate=$_POST['actDate'];
		$view = $_POST['view'];	
			
		//echo "Date :" . $actDate;
	}
// update for week
echo "startdate=". startDate.":";
	// check if month has been selected by user, else default month to current month
	if ($actDate != null) {
		$daysInPeriod = cal_days_in_month(CAL_GREGORIAN,substr($actDate, 5, 2),substr($actDate,0, 4));
		$month = substr($actDate, 5, 2);
		$year = substr($actDate,0, 4);
		$day = substr($startDate, 3, 2);
	} else {
		$daysInPeriod = date('t');
		$month = date('m');
		$year = date('Y');
		$actDate = date('Y') . "-" . date('m');
		$dayofweek = date('w') - 1;
		$weekStart = date('m-d-Y', strtotime('-'.$dayofweek.' days'));
		$startDate = substr($weekStart, 0, 2) . "/" . substr($weekStart, 3, 2) . "/" . substr($weekStart, 6, 4);
		$weekEnd= date('m-d-Y', strtotime('-'.($dayofweek - 6).' days'));
		$EndDate = substr($weekEnd, 0, 2) . "/" . substr($weekEnd, 3, 2) . "/" . substr($weekEnd, 6, 4);
		$day = substr($startDate, 3, 2);
		$weekStartDate = $year . "-" . $month . "-" . $day;
	}

	$daysInPeriod = 7;
	// get holidays in the current month 
	$holidayList = null;
	
// update for week		
	$db = new Database();	// open database
//	$sql = "select DATE_FORMAT(hdate, '%d') as hday from holiday where hdate >= STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') and hdate < STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') + " . $daysInPeriod; 
	$sql = "select hdate from holiday where hdate >= STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') and hdate < STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') + " . $daysInPeriod; 

	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}

	foreach ($rows as $row) {
		$holidayList[$row['hdate']] = $row['hdate'];;
	}
		
	$db->close(); 	// close database connection
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//echo "Loaded via Posting method</br>";
		//echo "No of rows" . $_POST['noofrows'];

		//print_r($_POST);
		$db2 = new Database();

		if($view != "approver") {
		
			$isqlId = 0;
			$dsqlId = 0;
			for( $i = 0; $i < $_POST['noofrows']; $i++ ) {
				$prjId = $_POST['prjId_'.$i.''];
				$deptId = $_POST['deptId_'.$i.''];
				$activityId = $_POST['activityId_'.$i.''];
			
				for( $j = 0; $j < $daysInPeriod; $j++ ) {				
					if ($_POST['modifiedHourFlg_'.$i.'_'.$j.''] == "true" && $_POST['hour_'.$i.'_'.$j.''] != "") {  
						$hour = $_POST['hour_'.$i.'_'.$j.''];
						//echo "Hour :" . $hour;
						
						//$deletesql[$dsqlId] = "delete from empmh where empId = " . $EmpId . " and prjId = " . $prjId . " and deptId = " . $deptId . " and actId = " . $activityId . " and mdate = STR_TO_DATE('". $year . "-" . $month . "-" . ($j + 1) . "','%Y-%m-%d');";
						$deletesql[$dsqlId] = "delete from empmh where empId = " . $EmpId . " and prjId = " . $prjId . " and deptId = " . $deptId . " and actId = " . $activityId . " and mdate = " . getNewDate($weekStartDate,$j) . ";";
						echo $deletesql[$dsqlId] . "</br>";
						$dsqlId++;
						
						//$insertsql[$isqlId] = "INSERT INTO empmh (empId, prjId, deptId, actId, mdate,mhours,status) VALUES (" . $EmpId . ", " . $prjId . ", " . $deptId . ", " . $activityId . ", STR_TO_DATE('". $year . "-" . $month . "-" . ($j + 1) . "','%Y-%m-%d'), " . $hour . ", 'S');";
						$insertsql[$isqlId] = "INSERT INTO empmh (empId, prjId, deptId, actId, mdate,mhours,status) VALUES (" . $EmpId . ", " . $prjId . ", " . $deptId . ", " . $activityId . ", " . getNewDate($weekStartDate,$j) . ", " . $hour . ", 'S');";
						echo $insertsql[$isqlId] . "</br>";
						$isqlId++;
						

					}	else if ($_POST['modifiedHourFlg_'.$i.'_'.$j.''] == "true" && $_POST['hour_'.$i.'_'.$j.''] == "") {
						$hour = $_POST['hour_'.$i.'_'.$j.''];
						//echo "Hour :" . $hour;
						
						//$deletesql[$dsqlId] = "delete from empmh where empId = " . $EmpId . " and prjId = " . $prjId . " and deptId = " . $deptId . " and actId = " . $activityId . " and mdate = STR_TO_DATE('". $year . "-" . $month . "-" . ($j + 1) . "','%Y-%m-%d');";
						$deletesql[$dsqlId] = "delete from empmh where empId = " . $EmpId . " and prjId = " . $prjId . " and deptId = " . $deptId . " and actId = " . $activityId . " and mdate = " . getNewDate($weekStartDate,$j) . ";";
						echo $deletesql[$dsqlId] . "</br>";
						$dsqlId++;
					}
				}
			}
				
			foreach ($deletesql as $dsql) {
				$db2->query($dsql);
				if ($db2->getError() != "") {
					echo $db2->getError();
					exit();
				}
			}			

			foreach ($insertsql as $isql) {
				$db2->query($isql);
				if ($db2->getError() != "") {
					echo $db2->getError();
					exit();
				}
			}
			
			// submit - update status
			$submitted = $_POST['isSubmit'];
			//echo "posted:".$submitted;
			if($submitted == "true") {
// update for week
				$sql = "select count(1) as rowCount, Status from tssubmit where empid =" . $EmpId . " and tdate = STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') ;";
				$row = $db2->select($sql, [], true);
				if ($db2->getError() != "") {
					echo $db->getError();
					exit();
				}
						
				if ($row['rowCount'] == "0") {
					$submitSql = "INSERT INTO tssubmit(EmpId, Manager, TDate, Status, SDate) VALUES(" . $EmpId . ",(select manager from employee where empid = " . $EmpId . "),STR_TO_DATE('". $year . "-" . $month . "-" . $day . "','%Y-%m-%d'),'S',STR_TO_DATE('". date('Y-m-d') . "','%Y-%m-%d'));";
				} else {
					$submitSql = "UPDATE tssubmit SET Status='S', Sdate = STR_TO_DATE('" . date('Y-m-d') . "','%Y-%m-%d') WHERE empid=" . $EmpId . " and Tdate=STR_TO_DATE('" . $year . "-" . $month . "-01','%Y-%m-%d') ;";
				}
							
				$db2->query($submitSql);
				if ($db2->getError() != "") {
					echo $db2->getError();
					exit();
				}
			}
		
		} else {
			$EmpId = $_POST['subemp'];
			// submit - update status
			$approved = $_POST['isApproved'];
			$rejected = $_POST['isRejected'];
			//echo "posted:".$approved.$rejected;
			if($approved == "true" || $rejected == "true") {
				if($approved == "true") {
					$aStatus = "A";
				} else {
					$aStatus = "R";
				}

// update for week				
				$approveUpdateQuery = "update tssubmit set status = '" . $aStatus . "', adate = STR_TO_DATE('". date('Y-m-d') . "','%Y-%m-%d') where empid = " . $EmpId . " and tdate = STR_TO_DATE('". $year . "-" . $month . "-" . $day . "','%Y-%m-%d');";

				$db2->query($approveUpdateQuery);
				if ($db2->getError() != "") {
					echo $db2->getError();
					exit();
				}
			}
		
		}
		
		$db2->close();
	}

	$db = new Database();	// open database
	
// update for week
	// querying tssubmit table and check if timesheet submitted for that month
	$sql = "select count(1) as rowCount, Status from tssubmit where empid =" . $EmpId . " and tdate = STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') ;";
	$row = $db->select($sql, [], true);
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	if ($row['rowCount'] == "1") {
		$status = $row['Status'];
	} else {
		$status = "";
	}

	// Time Sheet Status
	if ($status == "S") {
		$tsStatus = "Submitted for approval";
	} else if ($status == "A") {
		$tsStatus = "Approved";
	} else if ($status == "R") {
		$tsStatus = "Send back for updation";
	} else {
		$tsStatus = "Draft";
	}
	
	// disable cells 
	$disabled = "";
	if ($status == "S" || $status == "A" || $view == "approver") {
		$disabled = "disabled";
	}
	
	$buttonClass = 'class="button cmdbutton"';
	if ($status == "S" || $status == "A") {
		$buttonClass = 'class="button cmdbutton1"';
	}
	
	if($view == "approver") {
		if ($status == "S") {
			$buttonClass = 'class="button cmdbutton"';
		} else {
			$buttonClass = 'disabled class="button cmdbutton1"';
		}
	}
		
	// querying empmh table and getting all records for particular month
	$keyArray = null;
// update for week	
	//$sql = "SELECT mid, DATE_FORMAT(mdate, '%d') as mday, concat(Prjid ,'-' ,DeptId, '-', ActId) as key1 , mhours FROM " . $table;
	//$sql .= " where EmpId=" . $EmpId . " and mdate >= STR_TO_DATE('" . $year . "-" . $month . "-01','%Y-%m-%d')";
	//$sql .= " and mdate < STR_TO_DATE('" . $year . "-" . $month . "-01','%Y-%m-%d') + " . $daysInPeriod; 
	
	$sql = "SELECT mid, mdate, concat(Prjid ,'-' ,DeptId, '-', ActId) as key1 , mhours FROM " . $table;
	$sql .= " where EmpId=" . $EmpId . " and mdate >= STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d')";
	$sql .= " and mdate < STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') + " . $daysInPeriod; 		
	$rows = $db->select($sql);
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}

	foreach ($rows as $row)
	{	
		if ($keyArray[$row['key1']] != null) {
			$tempArray1 = null;
			$tempArray1 = $keyArray[$row['key1']];
			$tempArray1[$row['mday']] = $row['mhours'];
			$keyArray[$row['key1']] = $tempArray1;	
		} else {
			$tempArray = null;
			$tempArray[$row['mday']] = $row['mhours'];
			$keyArray[$row['key1']] = $tempArray;
		}
	}

	$db->close(); 	// Close connection
/*	
	function isHoliday($dd,$mm,$yyyy) {
		$idate = $yyyy."-".$mm."-".$dd;
		$dayName = date("D",strtotime($idate));
		if ($dayName == "Sun" || ($dd == $GLOBALS['holidayList'][$dd])) { // if the day is weekend or holiday
			return true;	// holiday
		} else {
			return false;	// not holiday
		}
	}
*/	
	function isHoliday($idate) {
		$dayName = date("D",strtotime($idate));
		if ($dayName == "Sun" || ($dd == $GLOBALS['holidayList'][$dd])) { // if the day is weekend or holiday
			return true;	// holiday
		} else {
			return false;	// not holiday
		}
	}
	function getHourClass($dd,$mm,$yyyy) {
		if (isHoliday($dd,$mm,$yyyy)) {
			return "holiday";
		} else {
			return "workday";
		}
	}
	
	function getEmployeeName($eid) {
		
		$db = new Database();	// open database
		
		$sql = "select name from employee where empid =" . $eid . " limit 1 ;";
		$row = $db->select($sql, [], true);
		if ($db->getError() != "") {
			echo $db->getError();
			exit();
		}
		$employeeName = $row['name'];
		
		$db->close();
		
		return $employeeName;
		
	}
?>

<html>
	<head>
<style>
	body {
		background-color: white;
	}
</style>
		<link rel="stylesheet" href="css/manhour.css">
		<script src="manhour.js"></script>
		<script src="jquery.js"></script>
		<script type="text/javascript" src="jquery-ui.min.js"></script>
		<link rel="stylesheet" type="text/css" media="screen" href="jquery-ui.css">
		<script type="text/javascript">

				
			$(function() {
				var startDate;
				var endDate;
				

				
				var selectCurrentWeek = function() {
					window.setTimeout(function () {
						$('.week-picker').find('.ui-datepicker-current-day a').addClass('ui-state-active')
					}, 1);
				}
				
				$('.week-picker').datepicker( {
					showOtherMonths: true,
					selectOtherMonths: true,
					 firstDay: 1 ,
					onSelect: function(dateText, inst) { 
						var date = $(this).datepicker('getDate');
						startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);
						endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 7);
						var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
						//$('#startDate').text($.datepicker.formatDate( dateFormat, startDate, inst.settings ));
						//$('#endDate').text($.datepicker.formatDate( dateFormat, endDate, inst.settings ));
						$('#startDate').val($.datepicker.formatDate( dateFormat, startDate, inst.settings ));
						selectCurrentWeek();
						$(this).change();
					},
					beforeShowDay: function(date) {
						var cssClass = '';
						if(date >= startDate && date <= endDate)
							cssClass = 'ui-datepicker-current-day';
						return [true, cssClass];
					},
					onChangeMonthYear: function(year, month, inst) {
						selectCurrentWeek();
					}
				});
				
				$('.ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
				$('.ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
			});
			
			
		</script>
	</head>
	
	<body onload="loadDate();calculateTotal();">
	
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="mhourform" onsubmit="enableDropdowns();"> 
		<br>
		<?php
		if($view == "approver") {
		?>
		
		<table border="1" align="left">
			<tr>
				<th class="total">Select Employee</th>
				<td>
					<select id="subemp" name="subemp" onchange="doReloadEmployee(this);">
					<?php
// update for week
						$sql3 = "select empid, name,  (select status from tssubmit where empid = a.empid and tdate = STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d')) as status from employee a where manager = " . $approver . " and empid != " . $approver . ";";

						$db3 = new Database();
						$emprows = $db3->select($sql3);
						if ($db3->getError() != "") {
							echo $db3->getError();
							exit();
						}

						foreach ($emprows as $emprow) {
							if ($emprow['status'] == "A") {
								$statusSymbol = "✔";
							} else if ($emprow['status'] == "R"){
								$statusSymbol = "✘";
							} else if ($emprow['status'] == "S") {
								$statusSymbol = "★";
							} else {
								$statusSymbol = "…";
							}
							
							if ($EmpId == $emprow['empid']) {
								echo '<option selected value="' . $emprow['empid'] . '">' . $statusSymbol . ' ' . $emprow['name'] . '</option>';
							} else {
								echo '<option value="' . $emprow['empid'] . '">' . $statusSymbol . ' ' . $emprow['name'] . '</option>';
							}
						}
						$db3->close();
					?>
					</select>
				</td>
			</tr>
		</table>
		<?php
		}
		?>
		<table border="1" align="center">
			<tr>
				<th class="total">Month</th>
				<td><input type="month" name="actDate" id="actDate" onchange="doReload(this.value);" value="<?php echo $actDate;?>"></td>
				<td><input type="text" class="week-picker" id="startDate" name="startDate" onchange="doReloadStartDate(this.value);" value="<?php echo $startDate;?>"></input></td>
			</tr>
		</table>
		
		<table border="0" align="center">
			<tr><td><font color="red"> 
				<?php echo "Status: ".$tsStatus; ?> 
			</font></td></tr>
		</table>
		
		<h1>Timesheet of <font color="blue"><?php echo getEmployeeName($EmpId); ?></font></h1>
		

	</body>
</html>