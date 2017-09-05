<body>
<h1>
	Manhour Details
	&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
	<img src="images/LasDatabase.jpg" height="15" width="60"> 
</h1>

<?php
// Defining function CreateDropDown
function CreateDropDown($table, $column){
    $conn2 = new mysqli("localhost", "root", "Las1");
	$sql = mysqli_query($conn2, "SELECT " . $column . " FROM " . $table . " order by " . $column . " limit 25");
	while ($row = $sql->fetch_assoc()){
		$v = $row['' . $column. ''];
		echo "<option value=\"" . $v . "\">" . $v . "</option>";
	}
} 
?>

<form action="TimeSheet.php" method="POST">
  <fieldset>
    <legend>:</legend>
	<select name="Emp"> 
	<?php    createDropDown("las.employee", "Name");  ?> 
	</select>
    &nbsp;&nbsp;Date: <input type="text" name="TDate" value="01-08-2017">&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="submit" value="Submit">
  </fieldset>
</form> 
</body>
</html> 