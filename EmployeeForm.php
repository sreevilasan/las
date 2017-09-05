<!DOCTYPE HTML>  
<html>
<head>
</head>
<body>  

<?php
	require 'include/sreesql.php';
	$table = 'employee';
	
// Defining function CreateDropDown
function CreateDropDown($table, $column){
	
	$pdo = OpenDatabase();  // open database
	
	$sql = "SELECT " . $column . " FROM " . $table . ";";
	$stmt = SqlExecute($pdo, $sql);
	
	while ($row = $stmt->fetch())
	{	
		echo "<option value=\"" . $row['' . $column. ''] . "\">" . $row['' . $column. ''] . "</option>";
	}

	CloseDatabase($pdo);	// Close database 
}   
?>

<?php
// define variables and set to empty values
	$EmpId = 0;
	$Name = "";
	$Designation = "";
	$DeptId = "";
	$GradeId  = "";
	$JoinDate = "";
	$Manager = "";
	$Mobile = "";
	$Telephone = "";
	$OfficialEmail = "";
	$PersonalEmail = "";
	$LocalAddress = "";
	$PermanentAddress = "";
	$DOB = "";
	$CardNo = "";
	$Gender = "";
	$ICENo = "";
	$ICEPerson = "";
	$Qualification = "";
	$YOP = "";
	$Experience = "";
	$MaritalStatus = "";
	$SpouseName = "";
	$NoOfChidren = "";
	$BloodGroup = "";
	$FoodLabel = "";
	$SeatNo = "";
	$ICNo = "";
	$PhotoFile = "";
	$Remarks = "";
	$Status = "";
	$LeftDate = "";
	$EmpFile  = "";
	$BranchId = "";
	$UID = "";
	$UTS = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$EmpId = test_input($_POST["EmpId"]);
	$Name = test_input($_POST["Name"]);
	$Designation = test_input($_POST["Designation"]);
	$DeptId = test_input($_POST["DeptId"]);
	$GradeId  = test_input($_POST["GradeId"]);
	$JoinDate = test_input($_POST["JoinDate"]);
	$Manager = test_input($_POST["Manager"]);
	$Mobile = test_input($_POST["Mobile"]);
	$Telephone = test_input($_POST["Telephone"]);
	$OfficialEmail = test_input($_POST["OfficialEmail"]);
	$PersonalEmail = test_input($_POST["PersonalEmail"]);
	$LocalAddress = test_input($_POST["LocalAddress"]);
	$PermanentAddress = test_input($_POST["PermanentAddress"]);
	$DOB = test_input($_POST["DOB"]);
	$CardNo = test_input($_POST["Cender"]);
	$ICENo = test_input($_POST["ICENo"]);
	$ICEPerson = test_input($_POST["ICEPerson"]);
	$Qualification = test_input($_POST["Qualification"]);
	$YOP = test_input($_POST["YOP"]);
	$Experience = test_input($_POST["Experience"]);
	$MaritalStatus = test_input($_POST["MaritalStatus"]);
	$SpouseName = test_input($_POST["SpouseName"]);
	$NoOfChidren = test_input($_POST["NoOfChidren"]);
	$BloodGroup = test_input($_POST["BloodGroup"]);
	$FoodLabel = test_input($_POST["FoodLabel"]);
	$SeatNo = test_input($_POST["SeatNo"]);
	$ICNo = test_input($_POST["ICNo"]);
	$PhotoFile = test_input($_POST["PhotoFile"]);
	$Remarks = test_input($_POST["Remarks"]);
	$Status = test_input($_POST["Status"]);
	$LeftDate = test_input($_POST["LeftDate"]);
	$EmpFile  = test_input($_POST["EmpFile "]);
	$BranchId = test_input($_POST["BranchId"]);
	$UID = test_input($_POST["UID"]);
	$UTS = test_input($_POST["UTS"]);
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>

<h2>Employee Database</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
	EmpId: <input type="text" name="EmpId"><br><br>
	Name: <input type="text" name="Name"><br><br>
	Designation: <input type="text" name="Designation"><br><br>
	DeptId: <input type="text" name="DeptId"><br><br>
	GradeId : <select name="GradeId"> <?php  createDropDown("las.grade", "GradeId");  ?> </select> <br><br>
	JoinDate: <input type="text" name="JoinDate"><br><br>
	Manager: <input type="text" name="Manager"><br><br>
	Mobile: <input type="text" name="Mobile"><br><br>
	Telephone: <input type="text" name="Telephone"><br><br>
	OfficialEmail: <input type="text" name="OfficialEmail"><br><br>
	PersonalEmail: <input type="text" name="PersonalEmail"><br><br>
	LocalAddress: <textarea name="LocalAddress" rows="2" cols="40"></textarea><br><br>
	PermanentAddress: <textarea name="PermanentAddress" rows="2" cols="40"></textarea><br><br>
	DOB: <input type="text" name="DOB"><br><br>
	CardNo: <input type="text" name="CardNo"><br><br>
	Gender:
		<input type="radio" name="Gender" value="female">Female
		<input type="radio" name="Gender" value="male">Male
	<br><br>	
	ICENo: <input type="text" name="ICENo"><br><br>
	ICEPerson: <input type="text" name="ICEPerson"><br><br>
	Qualification: <input type="text" name="Qualification"><br><br>
	YOP: <input type="text" name="YOP"><br><br>
	Experience: <input type="text" name="Experience"><br><br>
	MaritalStatus: 
		<input type="radio" name="MaritalStatus" value="Married">Married
		<input type="radio" name="MaritalStatus" value="Unmarried">Unmarried
		<br><br>
	SpouseName: <input type="text" name="SpouseName"><br><br>
	NoOfChidren: <input type="text" name="NoOfChidren"><br><br>
	BloodGroup: <select name="BloodGroup"> <?php  createDropDown("las.bloodgroup", "BloodGroup");  ?> </select> <br><br>
	FoodLabel: 
		<input type="radio" name="FoodLabel" value="Veg">Veg
		<input type="radio" name="FoodLabel" value="Nonveg">Nonveg
		<br><br>
	SeatNo: <input type="text" name="SeatNo"><br><br>
	ICNo: <input type="text" name="ICNo"><br><br>
	PhotoFile: <input type="text" name="PhotoFile"><br><br>
	Remarks: <input type="text" name="Remarks"><br><br>
	Status: <input type="text" name="Status"><br><br>
	LeftDate: <input type="text" name="LeftDate"><br><br>
	EmpFile : <input type="text" name="EmpFile "><br><br>
	BranchId: <input type="text" name="BranchId"><br><br>
	UID: <input type="text" name="UID"><br><br>
	UTS: <input type="text" name="UTS"><br><br>
	<input type="submit" name="submit" value="Submit">  
 </form>

<?php
echo "<h2>Your Input:</h2>";
	echo $EmpId . "<br>";
	echo $Name . "<br>";
	echo $Designation . "<br>";
	echo $DeptId . "<br>";
	echo $GradeId  . "<br>";
	echo $JoinDate . "<br>";
	echo $Manager . "<br>";
	echo $Mobile . "<br>";
	echo $Telephone . "<br>";
	echo $OfficialEmail . "<br>";
	echo $PersonalEmail . "<br>";
	echo $LocalAddress . "<br>";
	echo $PermanentAddress . "<br>";
	echo $DOB . "<br>";
	echo $CardNo . "<br>";
	echo $Gender . "<br>";
	echo $ICENo . "<br>";
	echo $ICEPerson . "<br>";
	echo $Qualification . "<br>";
	echo $YOP . "<br>";
	echo $Experience . "<br>";
	echo $MaritalStatus . "<br>";
	echo $SpouseName . "<br>";
	echo $NoOfChidren . "<br>";
	echo $BloodGroup . "<br>";
	echo $FoodLabel . "<br>";
	echo $SeatNo . "<br>";
	echo $ICNo . "<br>";
	echo $PhotoFile . "<br>";
	echo $Remarks . "<br>";
	echo $Status . "<br>";
	echo $LeftDate . "<br>";
	echo $EmpFile  . "<br>";
	echo $BranchId . "<br>";
	echo $UID . "<br>";
	echo $UTS . "<br>";

?>

</body>
</html>