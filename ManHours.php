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
	//$actDate=$_GET['actDate']; 
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
			errorExit($EmpId,$db3->getError());
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
		//echo "Loaded via Posting method</br>";
		$startDate=$_POST['startDate'];
		$view = $_POST['view'];	
			
		//echo "Date :" . $actDate;
	}
// update for week
	// check if month has been selected by user, else default month to current month
	/*
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
	*/
	
	if ($startDate != null) {
		$day = substr($startDate, 3, 2);
		$month = substr($startDate, 0, 2);
		$year = substr($startDate,6, 4);
		$weekStartDate = $year . "-" . $month . "-" . $day;
	} else {
		//$month = date('m');
		//$year = date('Y');
		$dayofweek = date('w') - 1;
		$today = date('Y-m-d');
		$weekStart = date('m-d-Y', strtotime('-'.$dayofweek.' days'));
		$day = substr($weekStart, 3, 2);
		$month = substr($weekStart, 0, 2);
		$year = substr($weekStart, 6, 4);
		$startDate = $month . "/" . $day . "/" . $year;
		$weekEnd= date('m-d-Y', strtotime('-'.($dayofweek - 6).' days'));
		$EndDate = substr($weekEnd, 0, 2) . "/" . substr($weekEnd, 3, 2) . "/" . substr($weekEnd, 6, 4);
		$day = substr($startDate, 3, 2);
		$weekStartDate = $year . "-" . $month . "-" . $day;
	}

	$daysInPeriod = 7;
	$dfmt="Y-m-d";
	
	// get holidays in the current month 
	$holidayList = null;
		
	$db = new Database();	// open database
//	$sql = "select DATE_FORMAT(hdate, '%d') as hday from holiday where hdate >= STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') and hdate < STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') + " . $daysInPeriod; 
	$sql = "select hdate from holiday where hdate >= STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') and hdate < STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') + " . $daysInPeriod; 
//echo "sql=".$sql;
	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		errorExit($EmpId,$db->getError());
	}

	foreach ($rows as $row) {
		$holidayList[$row['hdate']] = $row['hdate'];
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
						
						$deletesql[$dsqlId] = "delete from empmh where empId = " . $EmpId . " and prjId = " . $prjId . " and deptId = " . $deptId . " and actId = " . $activityId . " and mdate = STR_TO_DATE('". getNewDate($weekStartDate,$j,$dfmt) . "','%Y-%m-%d');";
						//echo $deletesql[$dsqlId] . "</br>";
						$dsqlId++;
						
						$insertsql[$isqlId] = "INSERT INTO empmh (empId, prjId, deptId, actId, mdate,mhours,status) VALUES (" . $EmpId . ", " . $prjId . ", " . $deptId . ", " . $activityId . ", STR_TO_DATE('". getNewDate($weekStartDate,$j,$dfmt) . "','%Y-%m-%d'), " . $hour . ", 'S');";
						//echo $insertsql[$isqlId] . "</br>";
						$isqlId++;
						
					}	else if ($_POST['modifiedHourFlg_'.$i.'_'.$j.''] == "true" && $_POST['hour_'.$i.'_'.$j.''] == "") {
						$hour = $_POST['hour_'.$i.'_'.$j.''];
						//echo "Hour :" . $hour;
						
						$deletesql[$dsqlId] = "delete from empmh where empId = " . $EmpId . " and prjId = " . $prjId . " and deptId = " . $deptId . " and actId = " . $activityId . " and mdate = STR_TO_DATE('". getNewDate($weekStartDate,$j,$dfmt) . "','%Y-%m-%d');";
						//echo $deletesql[$dsqlId] . "</br>";
						$dsqlId++;
					}
				}
			}
				
			foreach ($deletesql as $dsql) {
				$db2->query($dsql);
				if ($db2->getError() != "") {
					errorExit($EmpId,$db2->getError());
				}
			}			

			foreach ($insertsql as $isql) {
				$db2->query($isql);
				if ($db2->getError() != "") {
					errorExit($EmpId,$db2->getError());
				}
			}
			
			// submit - update status
			$submitted = $_POST['isSubmit'];
//echo "posted:".$submitted;
			if($submitted == "true") {
				$sql = "select count(1) as rowCount, Status from tssubmit where empid =" . $EmpId . " and tdate = STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') ;";
				$row = $db2->select($sql, [], true);
				if ($db2->getError() != "") {
					errorExit($EmpId,$db2->getError());
				}
						
				if ($row['rowCount'] == "0") {
					$submitSql = "INSERT INTO tssubmit(EmpId, Manager, TDate, Status, SDate) VALUES(" . $EmpId . ",(select manager from employee where empid = " . $EmpId . "),STR_TO_DATE('". $year . "-" . $month . "-" . $day . "','%Y-%m-%d'),'S',STR_TO_DATE('". date('Y-m-d') . "','%Y-%m-%d'));";
				} else {
					$submitSql = "UPDATE tssubmit SET Status='S', Sdate = STR_TO_DATE('" . date('Y-m-d') . "','%Y-%m-%d') WHERE empid=" . $EmpId . " and Tdate=STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') ;";
				}
							
				$db2->query($submitSql);
				if ($db2->getError() != "") {
					errorExit($EmpId,$db2->getError());
				}
			}
		
		} else {	//viewed by approver
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
			
				$approveUpdateQuery = "update tssubmit set status = '" . $aStatus . "', adate = STR_TO_DATE('". date('Y-m-d') . "','%Y-%m-%d') where empid = " . $EmpId . " and tdate = STR_TO_DATE('". $year . "-" . $month . "-" . $day . "','%Y-%m-%d');";

				$db2->query($approveUpdateQuery);
				if ($db2->getError() != "") {
					errorExit($EmpId,$db2->getError());
				}
				
				//	update manhour table to approve time 			
				if($approved == "true") {
					$manhourApproveQuery = "update empmh set status = '" . $aStatus . "' where empid = " . $EmpId . " and mdate >= STR_TO_DATE('". $year . "-" . $month . "-" . $day . "','%Y-%m-%d') and mdate < STR_TO_DATE('". $year . "-" . $month . "-" . $day . "','%Y-%m-%d') + " . $daysInPeriod . ";";
				
					$db2->query($manhourApproveQuery);
					if ($db2->getError() != "") {
						errorExit($EmpId,$db2->getError());
					}
				}
			}
		
		}
		
		$db2->close();
	}

	$db = new Database();	// open database
	
// update for week
	// querying tssubmit table and check if timesheet submitted for the requested period
	$sql = "select count(1) as rowCount, Status from tssubmit where empid =" . $EmpId . " and tdate = STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') ;";
	$row = $db->select($sql, [], true);
	if ($db->getError() != "") {
		errorExit($EmpId,$db->getError());
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

	//$sql = "SELECT mid, DATE_FORMAT(mdate, '%d') as mday, concat(Prjid ,'-' ,DeptId, '-', ActId) as key1 , mhours FROM " . $table;
	//$sql .= " where EmpId=" . $EmpId . " and mdate >= STR_TO_DATE('" . $year . "-" . $month . "-01','%Y-%m-%d')";
	//$sql .= " and mdate < STR_TO_DATE('" . $year . "-" . $month . "-01','%Y-%m-%d') + " . $daysInPeriod; 
	
	$sql = "SELECT mid, mdate, concat(Prjid ,'-' ,DeptId, '-', ActId) as key1 , mhours FROM " . $table;
	$sql .= " where EmpId=" . $EmpId . " and mdate >= STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d')";
	$sql .= " and mdate < STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d') + " . $daysInPeriod; 		
	$rows = $db->select($sql);
	if ($db->getError() != "") {
		errorExit($EmpId,$db->getError());
	}

	foreach ($rows as $row)
	{	
		if ($keyArray[$row['key1']] != null) {
			$tempArray1 = null;
			$tempArray1 = $keyArray[$row['key1']];
			$tempArray1[$row['mdate']] = $row['mhours'];
			$keyArray[$row['key1']] = $tempArray1;	
		} else {
			$tempArray = null;
			$tempArray[$row['mdate']] = $row['mhours'];
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
		if ($dayName == "Sun" || ($idate == $GLOBALS['holidayList'][$idate])) { // if the day is weekend or holiday
			return true;	// holiday
		} else {
			return false;	// not holiday
		}
	}
	function getHourClass($idate) {
		if (isHoliday($idate)) {
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
			errorExit($EmpId,$db->getError());
		}
		$employeeName = $row['name'];
		
		$db->close();
		
		return $employeeName;
		
	}
	
	function getNewDate($sdate,$ndays, $dfmt) {
		// $sdate in yyyy-mm-dd in string format
		// $ndays is no of days starting from $sdate in integer format
		// $dfmt is the output date format e.g "d-m-y"
		// returns the new date in yyyy-mm-dd in string format
		
		$date=date_create($sdate);
		date_add($date,date_interval_create_from_date_string($ndays . " days"));
		$newDate = date_format($date,$dfmt);
		return $newDate;
	}
		
	function errorExit($emp,$errmsg) {
		echo $errmsg;
		error_log(date("d-M-Y H:i:s") . ";" . $emp . ";" . $errmsg, 3, "C:\sreek\php_errors.log");
		exit();
	}

?>

<html>
	<head>
<style>
	body {
		background-color: lightblue;
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
				
					var temp = document.getElementById('startDate').value;
					date = new Date(temp.substr(6,4), temp.substr(0,2), temp.substr(3,2));					
					startDate = new Date(date.getFullYear(), date.getMonth()-1, date.getDate());					
					endDate = new Date(date.getFullYear(), date.getMonth() - 1, date.getDate() + 6);
				
				var selectCurrentWeek = function() {
					window.setTimeout(function () {
						$('.ui-datepicker-calendar').find('.ui-datepicker-current-day a').addClass('ui-state-active')
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
						if(date >= startDate && date <= endDate) {
							cssClass = 'ui-datepicker-current-day';
						}
						return [true, cssClass];
					},
					onChangeMonthYear: function(year, month, inst) {
						selectCurrentWeek();
					}
				});
				
				$('.ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
				$('.ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
				
				$(".week-picker").click(function() {
					selectCurrentWeek();
				});
			});

		</script>
	</head>
	
	<body onload="calculateTotal();">
	
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="mhourform" onsubmit="enableDropdowns();"> 
		<br>

			<?php
		if($view == "approver") {
		?>
		
		<table border="1" align="right">
			<tr>
				<th class="total">Select Employee</th>
				<td>
					<select id="subemp" name="subemp" onchange="doReloadEmployee(this);">
					<?php
						$sql3 = "select empid, name,  (select status from tssubmit where empid = a.empid and tdate = STR_TO_DATE('" . $year . "-" . $month . "-" . $day . "','%Y-%m-%d')) as status from employee a where manager = " . $approver . " and empid != " . $approver . ";";

						$db3 = new Database();
						$emprows = $db3->select($sql3);
						if ($db3->getError() != "") {
							errorExit($EmpId,$db3->getError());
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
		
		<table border="1" align="left">
			<tr>
				<!--<th class="total">Month</th>
				<td><input type="month" name="actDate" id="actDate" onchange="doReload(this.value);" value="<?php echo $actDate;?>"></td> -->
				<th class="total">Week Start Date:</th>
				<td><input type="text" class="week-picker" id="startDate" name="startDate" onchange="doReloadStartDate(this.value);" value="<?php echo $startDate;?>"></input></td>
			</tr>
		</table>
		
		<br><br>
		<table border="0" align="left">
			<tr><td><b><font color="red"> 
				<?php echo "Status: ".$tsStatus; ?> 
			</font></b></td></tr>
		</table>
		
		<br>
		<h1>Timesheet of <font color="blue"><?php echo getEmployeeName($EmpId); ?></font></h1>

		<div style="overflow-y: auto;" > <!-- div added for putting scroll bar on table-->
			<table border="3" align="left" id="hourtable">
				<tr class="table table1">
					<th align="centre" rowspan="2"></th>
					<th align="centre" rowspan="2">Project</th>
					<th align="centre" rowspan="2">Department</th>
					<th align="centre" rowspan="2">Activity</th>
					<?php	
						echo "\n";
						// for each day in month display day of the month
						for( $i = 0; $i < $daysInPeriod; $i++ ) {
							$newDate = getNewDate($weekStartDate,$i,$dfmt);
							$dayName = date("D",strtotime($newDate));
							echo '					<th>';
							if (isHoliday($newDate)) {
								echo '<font color="red">'.$dayName .'</font>';
							} else {
								echo $dayName;
							}
							echo "</th>\n";
						}
					?>
					<th align="centre" rowspan="2">Total</th>
				</tr>
				<tr class="table table1">
					<?php
						for( $i = 0; $i < $daysInPeriod; $i++ ) {
							echo "\n";
							echo "					<th>" . getNewDate($weekStartDate,$i,"d-M-y") . "</th>";
						}			
					?>
					
				</tr>
				
				<?php
					// Populating all rows fetched from database
					$ir = 0;	
					
					echo "\n";
					
					// for each key add a manhour row
					foreach ($keyArray as $key1=>$datearray) { 	
						$mykeys = explode('-', $key1);
						echo "\n";
						echo '					<tr name="mhrow_' . $ir . '" id="mhrow_' . $ir . '">';
						echo "\n";
						echo '						<td class="total">';
						echo "\n";
						echo '							<input type="checkbox" ' . $disabled. ' onchange="toggleDelete(this);" name="deleteChkBx"_' . $ir . '" id="deleteChkBx_' . $ir . '">';
						echo "\n						</td>\n";
						
						echo "						<td>\n";
						echo '							<select disabled name="prjId_' . $ir . '" id="prjId_' . $ir . '">' . createDropDownString("project", "prjid", "name", $mykeys[0]). "\n" . '							</select>';
						echo "\n						</td>";
						echo "\n";
						
						echo "						<td>\n";
						echo '							<select disabled  style="width: 95px" name="deptId_' . $ir . '" id="deptId_' . $ir . '">' . createDropDownString("Department", "deptid", "dept", $mykeys[1]). "\n" . '							</select>';
						echo "\n						</td>";
						echo "\n";
						
						echo "						<td>\n";
						echo '							<select disabled style="width:100px" name="activityId_' . $ir . '" id="activityId_' . $ir . '">' .  createDropDownString("Activity", "actid", "Activity", $mykeys[2]). "\n" . '							</select>';
						echo "\n						</td>";
						echo "\n";
						
						// for each day in month add hour column
						for( $i = 0; $i < $daysInPeriod; $i++ ) {
							$tempHour = "";
							
							foreach ($datearray as $date=>$hour) {
								// if day equal to date fetched from database set the hours fetched from database 
								$newDate = getNewDate($weekStartDate,$i,$dfmt);
								if($newDate == $date) {
									$tempHour = $hour;
								}
							}
							
							echo '						<td>';
							echo "\n";
							echo '							<input  ' . $disabled. ' class="' . getHourClass($newDate). '" onchange="checkHours(this);updateModifiedFlag(this);calculateTotal();checkTotal(this);"' . "\n								" . ' style="width: 60px; padding: 2px" type="text" name="hour_' . $ir . '_' . $i . '" id="hour_' . $ir . '_' . $i . '" value="' . $tempHour . '">';
							echo "\n						</td>\n";
							echo "\n";
							echo '							<input type="hidden" name="modifiedHourFlg_' . $ir . '_' . $i . '" id="modifiedHourFlg_' . $ir . '_' . $i . '" value="false">';
							echo "\n";
							//$ic = $ic + 1;
						}

						echo '						<td align="right"><input class="total" disabled style="width: 60px; padding: 2px" type="text" name="rowTotal_' . $ir . '" id="rowTotal_' . $ir . '" ></td>';
						echo "\n";
						echo "					</tr>";
						echo "\n";

						$ir = $ir + 1;				
					}
					
					// add blank rows if needed
					$lastrow = 0;
					if ($ir < 5) {
						$lastrow = 5;
					} else {
						$lastrow = $ir + 1;
					}
					
					while($ir <= $lastrow) {
						// Adding default blank row at end
						echo "\n";
						if($ir == $lastrow) {
							echo '					<tr style="display:none" id="clonerow" name="clonerow" >';
						} else {
							echo '					<tr name="mhrow_' . $ir . '" id="mhrow_' . $ir . '">';
						}
						echo "\n";
						echo '						<td class="total">';
						echo "\n";
						echo '							<input type="checkbox"  ' . $disabled. ' onchange="toggleDelete(this);" name="deleteChkBx"_' . $ir . '" id="deleteChkBx_' . $ir . '">';
						echo "\n						</td>\n";
						echo "						<td>\n";
						echo '							<select  ' . $disabled. ' onFocus="this.oldValue = this.value"; onchange="checkDuplicateRow(this); enableHoursOnRow(this);" name="prjId_' . $ir . '" id="prjId_' . $ir . '">' . createDropDownString("project", "prjid", "name", ""). "\n" . '							</select>';
						echo "\n						</td>";
						echo "\n";
						echo "						<td>\n";
						echo '							<select  ' . $disabled. ' onFocus="this.oldValue = this.value"; onchange="populateActDrpDwn(this);checkDuplicateRow(this);enableHoursOnRow(this);"  style="width: 95px" name="deptId_' . $ir . '" id="deptId_' . $ir . '">' . createDropDownString("Department", "deptid", "dept", ""). "\n" . '							</select>';
						echo "\n						</td>";
						echo "\n";
						echo "						<td>\n";
						echo '							<select  ' . $disabled. ' onFocus="this.oldValue = this.value"; onchange="checkDuplicateRow(this);enableHoursOnRow(this);" style="width:100px" name="activityId_' . $ir . '" id="activityId_' . $ir . '">' .  createDropDownString("Activity", "actid", "Activity", ""). "\n" . '							</select>';
						echo "\n						</td>";
						echo "\n";
					
						// for each day in month add hour column
						for( $i = 0; $i < $daysInPeriod; $i++ ) {
							$tempHour = "";
							$newDate = getNewDate($weekStartDate,$i,$dfmt);					
							echo '						<td>';
							echo "\n";
							echo '							<input disabled class="' . getHourClass($newDate). '" onchange="checkHours(this);updateModifiedFlag(this);calculateTotal();checkTotal(this);"' . "\n								" . ' style="width: 60px; padding: 2px" type="text" name="hour_' . $ir . '_' . $i . '" id="hour_' . $ir . '_' . $i . '" value="' . $tempHour . '">';
							echo "\n						</td>\n";
							echo '							<input type="hidden" name="modifiedHourFlg_' . $ir . '_' . $i . '" id="modifiedHourFlg_' . $ir . '_' . $i . '" value="false">';
							echo "\n";							
						}
						echo '						<td align="right">';
						echo "\n";
						echo '							<input class="total" disabled style="width: 60px; padding: 2px" type="text" name="rowTotal_' . $ir . '" id="rowTotal_' . $ir . '" >';
						echo "\n						</td>\n";
						echo "					</tr>";
						echo "\n";
						$ir = $ir + 1;
					}
					
					// for each day in month add total column
					echo '						<th class="total" align="right" colspan="4">Total&nbsp;&nbsp;</th>';
					echo "\n";
					
					for( $i = 0; $i < $daysInPeriod; $i++ ) {
						echo '						<td><input class="holiday" disabled style="width: 60px; padding: 2px" type="text" name="colTotal_' . $i . '" id="colTotal_' . $i .'"></td>';
						echo "\n";
					}
					
					echo '						<td align="right"><input class="total" disabled style="width: 60px; padding: 2px" type="text" name="grandTotal" id="grandTotal"></td>';
					echo "\n";
					echo "					</tr>";
					echo "\n";
				?>
			</table>
				<?php 
					echo "\n";
					echo '				<input type="hidden" name="noofrows" id="noofrows" value=' . ($ir) .'>';
					echo "\n";
					echo '				<input type="hidden" name="daysInPeriod" id="daysInPeriod" value=' . ($daysInPeriod) .'>';
					echo "\n";
					echo '				<input type="hidden" name="isSubmit" id="isSubmit" value=false>';
					echo "\n";
					echo '				<input type="hidden" name="isApproved" id="isApproved" value=false>';
					echo "\n";
					echo '				<input type="hidden" name="isRejected" id="isRejected" value=false>';
					echo "\n";
					echo '				<input type="hidden" name="dataModified" id="dataModified" value=false>';
					echo "\n";
					echo '				<input type="hidden" name="view" id="view" value=' . $view . '>';
					echo "\n";
				?>
			</div>
		</form>

		<table border="0" align="left">
			<tr>
			<?php
				if ($view == "approver") {
			?>
				<td><button <?php echo $buttonClass; ?> type="button" value="Approve" onclick="approveHours();">Approve</button></td>
				<td><button <?php echo $buttonClass; ?> type="button" value="Reject" onclick="rejectHours();">Reject</button></td>

			<?php
				} else {
			?>	
				<td><button <?php echo $disabled." ".$buttonClass; ?> type="button" value="Delete" onclick="deleteSelectedRows();">Delete Selected</button></td>
				<td><button <?php echo $disabled." ".$buttonClass; ?> type="button" value="Add" onclick="cloneRow();">Add a New Row</button></td>
				<td><button <?php echo $disabled." ".$buttonClass; ?> type="submit" form="mhourform" value="Save">Save</button></td>
				<td><button <?php echo $disabled." ".$buttonClass; ?> type="button" value="Submit" onclick="submitHours();">Submit for Approval</button></td>
			<?php
				}
			?>
				<td><button class="button cmdbutton" type="button" value="Quit" onclick="quitWithoutSaving();">Quit</button></td>

			</tr>
		</table>
	</body>
</html>