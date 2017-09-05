<!DOCTYPE html>
<?php
	// File: Manhours.php
	// 
	//error_log("test", 3, "C:\myfolder\php_errors.log");
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	$table = 'empmh';
		
	// get date from request get variables
	$actDate=$_GET['actDate']; 
	$month = "";
	$year = "";
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		echo "Loaded via Posting method</br>";
		$actDate=$_POST['actDate']; 
		//echo "Date :" . $actDate;
	}

	// check if month has been selected by user, else default month to current month
	if ($actDate != null) {
		$daysInMonth = cal_days_in_month(CAL_GREGORIAN,substr($actDate, 5, 2),substr($actDate,0, 4));
		$month = substr($actDate, 5, 2);
		$year = substr($actDate,0, 4);
	} else {
		$daysInMonth = date('t');
		$month = date('m');
		$year = date('Y');
		$actDate = date('Y') . "-" . date('m');
	}
	
	// get holidays in the current month 
	$holidayList = null;
		
	$db = new Database();	// open database
	$sql = "select DATE_FORMAT(hdate, '%d') as hday from holiday where hdate >= STR_TO_DATE('" . $year . "-" . $month . "-01','%Y-%m-%d') and hdate < STR_TO_DATE('" . $year . "-" . $month . "-01','%Y-%m-%d') + " . $daysInMonth; 
	$rows = $db->select($sql);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}

	foreach ($rows as $row) {
		$iday = (int)$row['hday'];
		$holidayList[$iday] = $iday;
	}
		
	$db->close(); 	// close database connection
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//echo "Loaded via Posting method</br>";
		//echo "No of rows" . $_POST['noofrows'];

		//print_r($_POST);

		$isqlId = 0;
		$dsqlId = 0;
		for( $i = 0; $i < $_POST['noofrows']; $i++ ) {
			$prjId = $_POST['prjId_'.$i.''];
			$deptId = $_POST['deptId_'.$i.''];
			$activityId = $_POST['activityId_'.$i.''];
		
			for( $j = 0; $j < $daysInMonth; $j++ ) {				
				if ($_POST['modifiedHourFlg_'.$i.'_'.$j.''] == "true" && $_POST['hour_'.$i.'_'.$j.''] != "") {  
					$hour = $_POST['hour_'.$i.'_'.$j.''];
					//echo "Hour :" . $hour;
					
					$deletesql[$dsqlId] = "delete from empmh where empId = " . $EmpId . " and prjId = " . $prjId . " and deptId = " . $deptId . " and actId = " . $activityId . " and mdate = STR_TO_DATE('". $year . "-" . $month . "-" . ($j + 1) . "','%Y-%m-%d');";
					echo $deletesql[$dsqlId] . "</br>";
					$dsqlId++;
					
					$insertsql[$isqlId] = "INSERT INTO empmh (empId, prjId, deptId, actId, mdate,mhours,status) VALUES (" . $EmpId . ", " . $prjId . ", " . $deptId . ", " . $activityId . ", STR_TO_DATE('". $year . "-" . $month . "-" . ($j + 1) . "','%Y-%m-%d'), " . $hour . ", 'S');";
					echo $insertsql[$isqlId] . "</br>";
					$isqlId++;
					

				}	else if ($_POST['modifiedHourFlg_'.$i.'_'.$j.''] == "true" && $_POST['hour_'.$i.'_'.$j.''] == "") {
					$hour = $_POST['hour_'.$i.'_'.$j.''];
					//echo "Hour :" . $hour;
					
					$deletesql[$dsqlId] = "delete from empmh where empId = " . $EmpId . " and prjId = " . $prjId . " and deptId = " . $deptId . " and actId = " . $activityId . " and mdate = STR_TO_DATE('". $year . "-" . $month . "-" . ($j + 1) . "','%Y-%m-%d');";
					echo $deletesql[$dsqlId] . "</br>";
					$dsqlId++;
				}
			}
		}
		
		$db2 = new Database();
		
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
		echo "posted:".$submitted;
		if($submitted == "true") {
			$submitInsertQuery = "INSERT INTO tssubmit(EmpId, Manager, TDate, Status, SDate) VALUES(" . $EmpId . ",(select manager from employee where empid = " . $EmpId . "),STR_TO_DATE('". $year . "-" . $month . "-01','%Y-%m-%d'),'S',STR_TO_DATE('". date('Y-m-d') . "','%Y-%m-%d'));";
			
			$db2->query($submitInsertQuery);
			if ($db2->getError() != "") {
				echo $db2->getError();
				exit();
			}
		}
		
		$db2->close();
	}

	$db = new Database();	// open database
	
	// querying tssubmit table and check if timesheet submitted for that month
	$sql = "select count(1) as rowCount, Status from tssubmit where empid =" . $EmpId . " and tdate = STR_TO_DATE('" . $year . "-" . $month . "-01','%Y-%m-%d') ;";
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
	if ($status == "S" || $status == "A") {
		$disabled = "disabled";
	}
	$buttonClass = 'class="button cmdbutton"';
	if ($status == "S" || $status == "A") {
		$buttonClass = 'class="button cmdbutton1"';
	}
		
	// querying empmh table and getting all records for particular month
	$keyArray = null;
	
	$sql = "SELECT mid, DATE_FORMAT(mdate, '%d') as mday, concat(Prjid ,'-' ,DeptId, '-', ActId) as key1 , mhours FROM " . $table;
	$sql .= " where EmpId=" . $EmpId . " and mdate >= STR_TO_DATE('" . $year . "-" . $month . "-01','%Y-%m-%d')";
	$sql .= " and mdate < STR_TO_DATE('" . $year . "-" . $month . "-01','%Y-%m-%d') + " . $daysInMonth; 
		
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
	
	function isHoliday($dd,$mm,$yyyy) {
		$idate = $yyyy."-".$mm."-".$dd;
		$day = date("D",strtotime($idate));
		if ($day == "Sun" || ($dd == $GLOBALS['holidayList'][$dd])) { // if the day is weekend or holiday
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
	</head>
	
	<body onload="loadDate();calculateTotal();">
	
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="mhourform" onsubmit="enableDropdowns();"> 
		<br>
		<table border="1" align="center">
			<tr>
				<th class="total">Month</th>
				<td><input type="month" name="actDate" id="actDate" onchange="doReload(this.value);" value="<?php echo $actDate;?>"></td>
			</tr>
		</table>
		
		<table border="0" align="center">
			<tr><td><font color="red"> 
				<?php echo "Status: ".$tsStatus; ?> 
			</font></td></tr>
		</table>
		
		<br><br>
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
						for( $i = 1; $i <= $daysInMonth; $i++ ) {
							$idate = $year."-".$month."-".$i;
							$day = date("D",strtotime($idate));
							echo '					<th>';
							if (isHoliday($i,$month,$year)) {
								echo '<font color="red">'.$day .'</font>'. "</th>";
							} else {
								echo $day . "</th>";
							}
							echo "\n";
						}
					?>
					<th align="centre" rowspan="2">Total</th>
				</tr>
				<tr class="table table1">
					<?php
						for( $i = 1; $i <= $daysInMonth; $i++ ) {
							echo "\n";
							echo "					<th>" . $i . "</th>";
						}			
					?>
					
				</tr>
				
				<?php
					// Populating all rows fetched from database
					$ir = 0;
					$ic = 0;
					
					echo "\n";
					// for each key add a manhour row
					foreach ($keyArray as $key1=>$datearray) {
						$mykeys = explode('-', $key1);
						echo "\n";
						echo "					<tr>";
						echo "\n";
						echo '						<td class="total">';
						echo "\n";
						echo '							<input type="checkbox" ' . $disabled. ' onchange="toggleDelete(this);" name="deleteChkBx"_' . $ir . '" id="deleteChkBx_' . $ir . '">';
						echo "\n						</td>\n";
						
						echo "						<td>\n";
						echo '							<select disabled name="prjId_' . $ir . '" id="prjId_' . $ir . '">' . createDropDownString("Project", "prjid", "name", $mykeys[0]). "\n" . '							</select>';
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
						for( $i = 1; $i <= $daysInMonth; $i++ ) {
							$tempHour = "";
							foreach ($datearray as $date=>$hour) {
								// if day equal to date fetched from database set the hours fetched from database 
								if($i == $date) {
									$tempHour = $hour;
								}
							}
							//echo '						<td><input style="width: 57px; padding: 2px" type="number" step="any" min="0" max="24" name="hours" id="' . $key1 . '-' . $i . '" value="' . $tempHour . '"></td>';		 // to increment data	
							//echo '						<td><input onchange="checkHours(this);updateModifiedFlag(this);calculateTotal();checkTotal(this);" style="width: 30px; padding: 2px" type="text" name="hour_' . $ir . '_' . $ic . '" id="hour_' . $ir . '_' . $ic . '" value="' . $tempHour . '"></td>';
							echo '						<td>';
							echo "\n";
							echo '							<input  ' . $disabled. ' class="' . getHourClass($i,$month,$year). '" onchange="checkHours(this);updateModifiedFlag(this);calculateTotal();checkTotal(this);"' . "\n								" . ' style="width: 30px; padding: 2px" type="text" name="hour_' . $ir . '_' . $ic . '" id="hour_' . $ir . '_' . $ic . '" value="' . $tempHour . '">';
							echo "\n						</td>\n";
							echo "\n";
							echo '							<input type="hidden" name="modifiedHourFlg_' . $ir . '_' . $ic . '" id="modifiedHourFlg_' . $ir . '_' . $ic . '" value="false">';
							echo "\n";
							$ic = $ic + 1;
						}

						echo '						<td align="right"><input class="total" disabled style="width: 60px; padding: 2px" type="text" name="rowTotal_' . $ir . '" id="rowTotal_' . $ir . '" ></td>';
						echo "\n";
						echo "					</tr>";
						echo "\n";

						$ir = $ir + 1;
						$ic = 0;
						
					}

					do {
						// Adding default blank row at end
						echo "\n";
						echo '					<tr id="clonerow">';
						echo "\n";
						echo '						<td class="total">';
						echo "\n";
						echo '							<input type="checkbox"  ' . $disabled. ' onchange="toggleDelete(this);" name="deleteChkBx"_' . $ir . '" id="deleteChkBx_' . $ir . '">';
						echo "\n						</td>\n";
						echo "						<td>\n";
						echo '							<select  ' . $disabled. ' onchange="enableHoursOnRow(this);" name="prjId_' . $ir . '" id="prjId_' . $ir . '">' . createDropDownString("Project", "prjid", "name", ""). "\n" . '							</select>';
						echo "\n						</td>";
						echo "\n";
						echo "						<td>\n";
						echo '							<select  ' . $disabled. ' onchange="populateActDrpDwn(this);enableHoursOnRow(this);"  style="width: 95px" name="deptId_' . $ir . '" id="deptId_' . $ir . '">' . createDropDownString("Department", "deptid", "dept", ""). "\n" . '							</select>';
						echo "\n						</td>";
						echo "\n";
						echo "						<td>\n";
						echo '							<select  ' . $disabled. ' onchange="enableHoursOnRow(this);" style="width:100px" name="activityId_' . $ir . '" id="activityId_' . $ir . '">' .  createDropDownString("Activity", "actid", "Activity", ""). "\n" . '							</select>';
						echo "\n						</td>";
						echo "\n";
					
						// for each day in month add hour column
						for( $i = 1; $i <= $daysInMonth; $i++ ) {
							$tempHour = "";

							//echo '				<td><input style="width: 57px; padding: 2px" type="number" step="any" min="0" max="24" name="hours" id="' . "key" . '-' . $i . '" value="' . $tempHour . '"></td>';
							//echo '						<td><input disabled onchange="checkHours(this);updateModifiedFlag(this);calculateTotal();checkTotal(this);" style="width: 30px; padding: 2px" type="text" name="hour_' . $ir . '_' . $ic . '" id="hour_' . $ir . '_' . $ic . '" value="' . $tempHour . '"></td>';
							
							echo '						<td>';
							echo "\n";
							echo '							<input disabled class="' . getHourClass($i,$month,$year). '" onchange="checkHours(this);updateModifiedFlag(this);calculateTotal();checkTotal(this);"' . "\n								" . ' style="width: 30px; padding: 2px" type="text" name="hour_' . $ir . '_' . $ic . '" id="hour_' . $ir . '_' . $ic . '" value="' . $tempHour . '">';
							echo "\n						</td>\n";
							echo '							<input type="hidden" name="modifiedHourFlg_' . $ir . '_' . $ic . '" id="modifiedHourFlg_' . $ir . '_' . $ic . '" value="false">';
							echo "\n";
							
							$ic = $ic + 1;								
						}
						echo '						<td align="right">';
						echo "\n";
						echo '							<input class="total" disabled style="width: 60px; padding: 2px" type="text" name="rowTotal_' . $ir . '" id="rowTotal_' . $ir . '" >';
						echo "\n						</td>\n";
						echo "					</tr>";
						echo "\n";
						$ir = $ir + 1;
						$ic = 0;
					} while ($ir < 5);
					
					// for each day in month add total column
					echo '						<th class="total" align="right" colspan="4">Total&nbsp;&nbsp;</th>';
					echo "\n";
					
					for( $i = 1; $i <= $daysInMonth; $i++ ) {
						echo '						<td><input class="holiday" disabled style="width: 30px; padding: 2px" type="text" name="colTotal_' . ($i - 1) . '" id="colTotal_' . ($i - 1) .'"></td>';
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
					echo '				<input type="hidden" name="daysInMonth" id="daysInMonth" value=' . ($daysInMonth) .'>';
					echo "\n";
					echo '				<input type="hidden" name="isSubmit" id="isSubmit" value=false>';
					echo "\n";
					echo '				<input type="hidden" name="dataModified" id="dataModified" value=false>';
					echo "\n";
				?>
			</div>
		</form>

		<table border="0" align="left">
			<tr>
				<td><button <?php echo $disabled." ".$buttonClass; ?> type="button" value="Delete" onclick="deleteSelectedRows();">Delete Selected</button></td>
				<td><button <?php echo $disabled." ".$buttonClass; ?> type="button" value="Add" onclick="cloneRow();">Add a New Row</button></td>
				<td><button <?php echo $disabled." ".$buttonClass; ?> type="submit" form="mhourform" value="Save">Save</button></td>
				<td><button <?php echo $disabled." ".$buttonClass; ?> type="button" value="Submit" onclick="submitHours();">Submit for Approval</button></td>
				<td><button class="button cmdbutton" type="button" value="Quit" onclick="quitWithoutSaving();">Quit</button></td>
			</tr>
		</table>
	</body>
</html>