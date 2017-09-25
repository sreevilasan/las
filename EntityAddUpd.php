<!DOCTYPE html>
<?php
	// File: EntityAddUpd.php 
	// 
	//error_log("test", 3, "C:\myfolder\php_errors.log");
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';

	function getEntityDescription($v_entityid, $v_primarykey) {
		$db = new Database();	// open database

		$sql = "SELECT * FROM entity where entityid='" . $v_entityid . "'";

		$row = $db->select($sql, [], true);
		
		if ($db->getError() != "") {
			echo $db->getError();
			exit();
		}

		$v_entityprimtable = $row['primtable'];
		$v_entityprimcol = $row['primcol'];
		$v_entitydescol = $row['descol'];
		
		$entitydescsql = "SELECT " . $v_entitydescol . " FROM " . $v_entityprimtable . " WHERE " . $v_entityprimcol . " = '" . $v_primarykey . "';";

		$row = $db->select($entitydescsql, [], true);
		
		if ($db->getError() != "") {
			echo $db->getError();
			exit();
		}
		
		$v_desc = $row[$v_entitydescol];
		$db->close();

		return $v_desc;
	}
	
	function getLookupDropdown($lookupid, $lookupval = ""){
		$db = new Database();	// open database
		
		$sql = "SELECT value, description FROM lookupval where lookupid = '" . $lookupid . "';";
		$rows = $db->select($sql);
		if ($db->getError() != "") {
			echo $db->getError();
			exit();
		}
		
		// putting blank option at start of dropdown
		$dropdownString = "\n								<option disabled selected></option> ";
		
		// Looping through query output and creating dropdown
		foreach ($rows as $row)
		{
			if ($lookupval == $row['value']) {
				$dropdownString = $dropdownString . "\n								<option selected value=\"" . $row['value'] . "\">" . $row['description'] . "</option> ";
			} else {
				$dropdownString = $dropdownString . "\n								<option value=\"" . $row['value'] . "\">" . $row['description'] . "</option> ";
			}
		}
		
		$db->close();
		
		return $dropdownString;
	} 
		
	// get data from GET variables
	$entityid = $_GET['entityid']; 
	$primarykey = $_GET['primarykey']; 
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//echo "Loaded via Posting method</br>";
		$entityid = $_POST['entityid'];
		$primarykey = $_POST['primarykey']; 
	}
	
	$db = new Database();	// open database

	$sql = "SELECT * FROM entity where entityid='" . $entityid . "';";
	$row = $db->select($sql, [], true);
		
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	$entitydescription = $row['description'];
	$entityprimtable = $row['primtable'];
	$entityprimcol = $row['primcol'];
	$selfgenprimkey = $row['selfgenprimkey'];

	echo '<h2>' . $entitydescription. ' </h2><br>';
	
	$sql = "SELECT * FROM entityfields where entityid='" . $entityid . "' order by displayseq";
	$rows = $db->select($sql);
	
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
	}
	
	$entityfields;
	
	foreach ($rows as $row)
	{
		$entityfield['fieldid'] = $row['fieldid'];
		$entityfield['description'] = $row['description'];
		$entityfield['displayseq'] = $row['displayseq'];
		$entityfield['displaytype'] = $row['displaytype'];
		$entityfield['refentityid'] = $row['refentityid'];
		$entityfield['lookupid'] = $row['lookupid'];
		$entityfield['reftable'] = $row['reftable'];
		$entityfield['refvalcol'] = $row['refvalcol'];
		$entityfield['refdescol'] = $row['refdescol'];
		$entityfield['display'] = $row['display'];
		$entityfield['search'] = $row['search'];
		$entityfields[$row['fieldid']] = $entityfield;
	}
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//echo "Loaded via Posting method</br>";
		//print_r($_POST);
		
		if($_POST['dataModified'] == "true") {
			if ($primarykey != "") {	
				$updatesql = "";	
				foreach ($entityfields as $entityfield)
				{
					if ($entityfield['fieldid'] == $entityprimcol) {
						continue;
					}
					$updatesql = $updatesql . $entityfield['fieldid'] . " = '" . $_POST[$entityfield['fieldid']] . "' , ";
				}
				$updatesql = substr($updatesql, 0, (strlen($updatesql) - 2));
				
				$updatesql = "UPDATE " . $entityprimtable . " SET " . $updatesql . " WHERE " . $entityprimcol . " = '" . $primarykey . "';";
				echo $updatesql;
				
				$db->query($updatesql);
				if ($db->getError() != "") {
					echo $db->getError();
					exit();
				}
					
			}	else {
					$insertsql = "";	
					$values = "";
					foreach ($entityfields as $entityfield)
					{
						if ($entityfield['fieldid'] == $entityprimcol) {
							continue;
						}
						$insertsql = $insertsql . $entityfield['fieldid'] . " , ";
						if ($_POST[$entityfield['fieldid']] == "") {
							$values = $values . " null , ";
						} else {
							$values = $values . "'" . $_POST[$entityfield['fieldid']] . "' , ";
						}						
					}
					$insertsql = substr($insertsql, 0, (strlen($insertsql) - 2));
					$values = substr($values, 0, (strlen($values) - 2));
					
					$insertsql = "INSERT INTO " . $entityprimtable . " (" . $insertsql . ") VALUES (" . $values . ");";
					echo $insertsql;
					$db->query($insertsql);
					if ($db->getError() != "") {
						echo $db->getError();
						exit();
					}

					$row = $db->select("SELECT LAST_INSERT_ID() as id;", [], true);
					if ($db->getError() != "") {
						echo $db->getError();
						exit();
					}
					
					echo $row['id'];						
			}
		}
		header("location:EntityDisplay.php?entityid=" . $entityid . "&primarykey=" . $primarykey);
		exit();
	}
	
	if ($primarykey != "") {
		
		$entitysql = "";		
		foreach ($entityfields as $entityfield)
		{
			$entitysql = $entitysql . $entityfield['fieldid'] . " , ";
		}
		$entitysql = substr($entitysql, 0, (strlen($entitysql) - 2));
				
		$entitysql = "SELECT " . $entitysql . "FROM " . $entityprimtable . " WHERE " . $entityprimcol . " = '" . $primarykey . "';";

		$row = $db->select($entitysql, [], true);
		if ($db->getError() != "") {
			echo $db->getError();
			exit();
		}
		
		foreach ($entityfields as $entityfield)
		{
			$entityfield['value'] = $row[$entityfield['fieldid']];
			$entityfields[$entityfield['fieldid']] = $entityfield;
		}		
	}

	$db->close();	// Close database  
?>

<html>
	<head>
	<style>
		body {background-color: white;}
	</style>
	<link rel="stylesheet" href="css/manhour.css">
	<link rel="stylesheet" href="css/LasStyle.css">
	<script type="text/javascript">
		function quitWithoutSaving() {
			if (document.getElementById('dataModified').value == "true") {
				if (confirm("Data not saved. Do you really want to quit without saving modified values?") == true) {
					document.location = "EditEmployee.php"; // go to lasdb link
				} 	
			} else {
				document.location = "EditEmployee.php"; // go to employee main page
			}
		}
		
		function updateModifiedFlag(){
			document.getElementById('dataModified').value = true;
		}
		
		function popitup(refentityid, fieldid) {
			newwindow=window.open("EntitySearchHandler.php?entityid=" + refentityid + "&fieldid=" + fieldid,'name','height=400,width=600');
			if (window.focus) {newwindow.focus()}
			return false;
		}
		
		function HandlePopupResult(entityid, fieldid, result, description) {
			document.getElementById(fieldid).value = result;
			document.getElementById(entityid + "_" + fieldid + "_desc").innerHTML = description;
		}
	</script>
</head>
	
<body onload=";">
	
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="entityform" onsubmit=";"> 
		<table border="0">
<?php
		foreach ($entityfields as $entityfield)
		{
			$disabled = "";
			if ($entityfield['fieldid'] == $entityprimcol) {
				if($selfgenprimkey == "Y") {
					$disabled = "disabled";
				}
			}
			
			if (($entityfield['displaytype'] == "entity") ||($entityfield['displaytype'] == "function")) {
				$disabled = "disabled";
			}
			
			if ($entityfield['displaytype'] == "date") {
				$inputtype = "date";
			} else if ($entityfield['displaytype'] == "password") {
				$inputtype = "password";
			} else if ($entityfield['displaytype'] == "radio") {
				$inputtype = "radio";
			} else if ($entityfield['displaytype'] == "checkbox") {
				$inputtype = "checkbox";
			} else if ($entityfield['displaytype'] == "file") {
				$inputtype = "file";
			} else {
				$inputtype = "text";
			}
			
			echo "				<tr>\n";
			echo '					<th align="right">' . $entityfield['description'] .': ' . '</th>';
			echo "\n";
			
			if($entityfield['displaytype'] == "lookup") {
				echo "						<td>\n";
				echo '							<select name="' . $entityfield['fieldid'] . '" id="' . $entityfield['fieldid'] . '">' . getLookupDropdown($entityfield['lookupid'], $entityfield['value'] ). "\n" . '							</select>';
				echo "\n						</td>";
				
			} elseif($entityfield['displaytype'] == "dropdown") {
				echo "						<td>\n";
				echo '							<select name="' . $entityfield['fieldid'] . '" id="' . $entityfield['fieldid'] . '">' . createDropDownString($entityfield['reftable'], $entityfield['refvalcol'], $entityfield['refdescol'], $entityfield['value'] ). "\n" . '							</select>';
				echo "\n						</td>";
				
			} else {
				echo '					<td><input ' . $disabled . ' name="' . $entityfield['fieldid'] . '" id="' . $entityfield['fieldid'] . '" type="' . $inputtype . '" value="' . $entityfield['value'] . '" onchange="updateModifiedFlag();"></td>';
			}
			echo "\n";
			
			if (($entityfield['displaytype'] == "entity") || ($entityfield['displaytype'] == "function")){
				echo '					<td style="padding-left:0px; padding-right:0px;"><button type="button" onclick="popitup(\'' . $entityfield['refentityid'] . '\', \'' . $entityfield['fieldid'] . '\');">...</button></td>';

				echo '					<td id="' . $entityfield['refentityid'] . '_' . $entityfield['fieldid'] . '_desc" >' . getEntityDescription($entityfield['refentityid'], $entityfield['value']) . '</td>';
			} else {
				echo '					<td></td>';
				echo '					<td></td>';
			}
			
			echo "				</tr>\n";
		}
?>
		</table>
		<input type="hidden" name="entityid" id="entityid" value="<?php echo $entityid; ?>">
		<input type="hidden" name="primarykey" id="primarykey" value="<?php echo $primarykey; ?>">
	</form>
	
	<input type="hidden" name="dataModified" id="dataModified" value=false>
	
	<table border="0" align="left">
		<tr>
			<td><button class="button button1" type="submit" form="entityform" value="Save">Save</button></td>
			<td><button class="button button1" type="button" value="Quit" onclick="quitWithoutSaving();">Cancel</button></td>
		</tr>
	</table>
</body>
</html>