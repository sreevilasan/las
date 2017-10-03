<!DOCTYPE html>
<?php
	// File: EntityAddUpd.php 
	// 
	//error_log("test", 3, "C:\myfolder\php_errors.log");
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	// get data from GET variables
	$entityid = $_GET['entityid']; 
	$primarykey = $_GET['primarykey'];
	$primarykey2 = $_GET['primarykey2']; 
	$primarykey3 = $_GET['primarykey3']; 
	$primarykey4 = $_GET['primarykey4']; 
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//echo "Loaded via Posting method</br>";
		$entityid = $_POST['_entityid'];
		$primarykey = $_POST['primarykey'];
		$primarykey2 = $_POST['primarykey2'];
		$primarykey3 = $_POST['primarykey3'];
		$primarykey4 = $_POST['primarykey4'];
	}

	require 'include/GetEntityFields.php';

	$db = new Database();	// open database
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//echo "Loaded via Posting method</br>";
		
		if($_POST['dataModified'] == "true") {
			if ($primarykey != "") {	// update existing record
				$updatesql = "";	
				foreach ($entityfields as $entityfield)
				{
					if ($entityfield['fieldid'] == $entityprimcol) {
						continue;
					}
					if ($_POST[$entityfield['fieldid']] == "") {
						$updatesql = $updatesql . $entityfield['fieldid'] . " = null , ";
					} else {
						$updatesql = $updatesql . $entityfield['fieldid'] . " = '" . $_POST[$entityfield['fieldid']] . "' , ";
					}
				}
				$updatesql = substr($updatesql, 0, (strlen($updatesql) - 2));
				
				$updatesql = "UPDATE " . $entityprimtable . " SET " . $updatesql . " WHERE " . $entityprimcol . " = '" . $primarykey . "' ";
				
				if($primarykey2 != "") {
					$updatesql = $updatesql . " AND " . $entityprimcol2 . " = '" . $primarykey2 . "' "; 
				}
				
				if($primarykey3 != "") {
					$updatesql = $updatesql . " AND " . $entityprimcol3 . " = '" . $primarykey3 . "' "; 
				}
				
				if($primarykey4 != "") {
					$updatesql = $updatesql . " AND " . $entityprimcol4 . " = '" . $primarykey4 . "' "; 
				}
				
				$updatesql = $updatesql . " ;";
				//echo $updatesql;
				
				$db->query($updatesql);
				if ($db->getError() != "") {
					echo $db->getError();
					exit();
				}
					
			}	else {		// insert a new record
					$insertsql = "";	
					$values = "";
					foreach ($entityfields as $entityfield)
					{
						if ($entityfield['fieldid'] == $entityprimcol) {
							if($selfgenprimkey == "Y") {
								continue;
							} else {
								$primarykey = $_POST[$entityfield['fieldid']];
							}
						}
						
						if (($entityprimcol2 != "") and ($entityfield['fieldid'] == $entityprimcol2)) {
							$primarykey2 = $_POST[$entityfield['fieldid']];
						}
						
						if (($entityprimcol3 != "") and ($entityfield['fieldid'] == $entityprimcol3)) {
							$primarykey3 = $_POST[$entityfield['fieldid']];
						}
						
						if (($entityprimcol4 != "") and ($entityfield['fieldid'] == $entityprimcol4)) {
							$primarykey4 = $_POST[$entityfield['fieldid']];
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

					if($selfgenprimkey == "Y") {
						$row = $db->select("SELECT LAST_INSERT_ID() as id;", [], true);
						if ($db->getError() != "") {
							echo $db->getError();
							exit();
						}
					
						$primarykey = $row['id'];
					}
			}
		}
		//header("location:EntitySearch.php?entityid=" . $entityid);
		//exit();
	}

	
	if ($primarykey != "") {
		
		$entitysql = "";		
		foreach ($entityfields as $entityfield)
		{
			$entitysql = $entitysql . $entityfield['fieldid'] . " , ";
		}
		$entitysql = substr($entitysql, 0, (strlen($entitysql) - 2));	
		
		$entitysql = "SELECT " . $entitysql . "FROM " . $entityprimtable . " WHERE " . $entityprimcol . " = '" . $primarykey . "' ";
		
		if($primarykey2 != "") {
			$entitysql = $entitysql . " AND " . $entityprimcol2 . " = '" . $primarykey2 . "' "; 
		}
		
		if($primarykey3 != "") {
			$entitysql = $entitysql . " AND " . $entityprimcol3 . " = '" . $primarykey3 . "' "; 
		}
		
		if($primarykey4 != "") {
			$entitysql = $entitysql . " AND " . $entityprimcol4 . " = '" . $primarykey4 . "' "; 
		}
		
		$entitysql = $entitysql . " ;";
		//echo $entitysql;

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
		var entityfield = {};
<?php			
		foreach ($entityfields as $entityfield)
		{
				echo '		entityfield["' . $entityfield['fieldid'] . '"] = {"fieldid":"' . $entityfield['fieldid'] . '", "description":"' . $entityfield['description'] . '", "displayseq":"' . $entityfield['displayseq'] . '", "displaytype":"' . $entityfield['displaytype'] . '", "hidden":"' . $entityfield['hidden'] . '", "search":"' . $entityfield['search'] . '", "required":"' . $entityfield['required'] . '"};';
				echo "\n";
		}	

		echo "\n";		
?>
		
		function addFunctionValues() {			
			for(var index in entityfield) {
				var mapKey = index;//This is the map's key.
				var mapKeyVal = entityfield[mapKey];//This is the value part for the map's key.
				if (mapKeyVal["displaytype"] == "function"){
					if (mapKeyVal["fieldid"] == "uid") {
						document.getElementById(mapKeyVal["fieldid"]).value = document.getElementById("sessionid").value;
					} else if (mapKeyVal["fieldid"] == "uts") {
						var d = new Date();
						var nd = d.getFullYear()+"-"+d.getMonth()+"-"+d.getDate()+" "+d.getHours()+":"+d.getMinutes()+":"+d.getSeconds();
						document.getElementById(mapKeyVal["fieldid"]).value = nd;
					//} else if (mapKeyVal["fieldid"] == "empfile") {
					//	document.getElementById(mapKeyVal["fieldid"]).value = "EMP-"+employeeNumber(document.getElementById("primarykey").value)+".docx";
					//} else if (mapKeyVal["fieldid"] == "comfile") {
					//	document.getElementById(mapKeyVal["fieldid"]).value = "COM-"+companyNumber(document.getElementById("primarykey").value)+".docx";
					}
				}
			}
		}
		//function employeeNumber(a) {
		//	return a;
		//}
		//function companyNumber(a) {
		//	return a;
		//}
		
		function enableFields(){
			addFunctionValues();
			for(var index in entityfield) {
				var mapKey = index;//This is the map's key.
				var mapKeyVal = entityfield[mapKey];//This is the value part for the map's key.
				if (mapKeyVal["displaytype"] == "radio") {
					continue;
				}
				document.getElementById(mapKeyVal["fieldid"]).disabled = false;
			}
		}
	
		function validDate(d) {
			if (d == "") return true;
			if (d.length > 5) return true;
			return false;
		}
	
		function validEmail(em) {
			if (em == "") {
				return true;
			}
			i = em.indexOf("@");
			ems = em.substr(i, em.length);
			if ((i > 0) && (ems.indexOf(".") > 0) && em.length > 5) {
				return true;
			} else {
				return false;
			}
		}

		function validateFields(){
			
			for(var index in entityfield) {
				var mapKey = index;//This is the map's key.
				var mapKeyVal = entityfield[mapKey];//This is the value part for the map's key.

				if(mapKeyVal['required'] == "Y") {
					if(document.getElementById(mapKeyVal["fieldid"]).value == "") {
						alert(mapKeyVal['description'] + " is required");
						document.getElementById(mapKeyVal["fieldid"]).style.borderColor = "red";
						document.getElementById(mapKeyVal["fieldid"]).focus();
						return false;
					}
				}

				if(mapKeyVal['displaytype'] == "date") {
					if(validDate(document.getElementById(mapKeyVal["fieldid"]).value) == false ){
						alert("Invalid "+ mapKeyVal['description'] + ". Please enter correct date.");
						document.getElementById(mapKeyVal["fieldid"]).style.borderColor = "red";
						document.getElementById(mapKeyVal["fieldid"]).focus();
						return false;
					}
				}

				if(mapKeyVal['displaytype'] == "email") {
					if(validEmail(document.getElementById(mapKeyVal["fieldid"]).value) == false ){
						alert("Invalid "+mapKeyVal['description'] + ". Please enter correct email.");
						document.getElementById(mapKeyVal["fieldid"]).style.borderColor = "red";
						document.getElementById(mapKeyVal["fieldid"]).focus();
						return false;
					}
				}
			}
			enableFields();
		}

		function quitWithoutSaving() {
			if (document.getElementById('dataModified').value == "true") {
				if (confirm("Data not saved. Do you really want to quit without saving modified values?") == true) {
					document.location = "EntitySearch.php?entityid=" + document.getElementById('_entityid').value
				} 	
			} else {
				document.location = "EntitySearch.php?entityid=" + document.getElementById('_entityid').value
			}
		}
		
		function updateModifiedFlag(){
			// alert("inUpdatemodify");
			document.getElementById('dataModified').value = true;
		}
		
		function popitup(refentityid, fieldid) {
			newwindow=window.open("EntitySearchHandler.php?entityid=" + refentityid + "&fieldid=" + fieldid,'_blank','left=400,top=50,height=500,width=600,titlebar=no,toolbar=no,location=no');
			if (window.focus) {newwindow.focus()}
			return false;
		}
		
		function HandlePopupResult(entityid, fieldid, result, description) {
			document.getElementById(fieldid).value = result;
			document.getElementById(entityid + "_" + fieldid + "_desc").innerHTML = description;
			updateModifiedFlag();
		}
		
		function uploadImage(img) {
			newwindow = window.open('uploadFile.php','_blank','left=400,top=50,height=500,width=600,titlebar=no,toolbar=no,location=no');
			//if (window.focus) {newwindow.focus();}
		}
	
	</script>
</head>
	
<body onload=";">
	
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="entityform" onsubmit="return validateFields();"> 
<?php
		echo '<table class="tabinput" border="0">';      // main title
		echo '<tr><td colspan="2">';
			echo '<h1>' . $entitydescription. ' </h1></td>';
?>
			<td><button class="button button1" type="submit" form="entityform" value="Save" onclick="">Save</button></td>
			<td><button class="button button1" type="button" value="Quit" onclick="quitWithoutSaving();">Quit</button></td>
<?php
		echo '</tr><tr>';
		if ($displayphoto == "Y") {
			echo '<td valign="top"><a href="javascript:uploadImage(\'' . $imagefile . '\');"><img src="' . $imagefile . '" height="120" width="100"><br>Update Photo</a></td>';
		}
		echo '<td>';
		
		echo '<table class="tabinput" border="0">';		// field inputs
		
		foreach ($entityfields as $entityfield)
		{
			$disabled = "";
			if ($entityfield['fieldid'] == $entityprimcol) {
				if($selfgenprimkey == "Y") {
					$disabled = "disabled";
				}
			}
			
			if ($entityfield['disable'] == "Y") {
				$disabled = "disabled";
			}
			
			$width = "";
			if ($entityfield['width'] != "") {
				$width = 'size="' . $entityfield['width'] . '"';
			}
			$hidden = "";
			if ($entityfield['hidden'] == "Y") {
				$hidden = "hidden";
			}
			
			if (($entityfield['displaytype'] == "entity")  || ($entityfield['displaytype'] == "function")) {
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
			
			echo "				<tr $hidden>\n";
			echo '					<th title="' . $entityfield['comment'] . '" align="right">' . $entityfield['description'] .': ' . '</th>';
			echo "\n";
			
			if($entityfield['displaytype'] == "lookup") {
				echo "						<td>\n";
				echo '							<select onchange="updateModifiedFlag();" name="' . $entityfield['fieldid'] . '" id="' . $entityfield['fieldid'] . '">' . getLookupDropdown($entityfield['lookupid'], $entityfield['value'] ). "\n" . '							</select>';
				echo "\n						</td>";
			} elseif($entityfield['displaytype'] == "radio") {
				echo "<td>\n";
				echo getLookupRadio($entityfield['lookupid'], $entityfield['value'], $entityfield['fieldid']);
				echo "</td>";								
			} elseif($entityfield['displaytype'] == "dropdown") {
				echo "						<td>\n";
				echo '							<select onchange="updateModifiedFlag();" name="' . $entityfield['fieldid'] . '" id="' . $entityfield['fieldid'] . '">' . createDropDownString($entityfield['reftable'], $entityfield['refvalcol'], $entityfield['refdescol'], $entityfield['value'] ). "\n" . '							</select>';
				echo "\n						</td>";
			} elseif($entityfield['displaytype'] == "entity") {
				echo '					<td><input onchange="updateModifiedFlag();" ' . $disabled . ' ' . $width . ' name="' . $entityfield['fieldid'] . '" id="' . $entityfield['fieldid'] . '" type="' . $inputtype . '" value="' . $entityfield['value'] . '" onchange="updateModifiedFlag();"> <button type="button" onclick="popitup(\'' . $entityfield['refentityid'] . '\', \'' . $entityfield['fieldid'] . '\');">...</button> ';
				//echo '<input value="' . getEntityDescription($entityfield['refentityid'], $entityfield['value']) . '">';						
				echo '					<span id="' . $entityfield['refentityid'] . '_' . $entityfield['fieldid'] . '_desc" >' . getEntityDescription($entityfield['refentityid'], $entityfield['value'], $entityfields[$entityfield['refentityprim1']]['value']) . '</span></td>';				
			} else {
				echo '					<td><input title="' . $entityfield['comment'] . '" onchange="updateModifiedFlag();" ' . $disabled . ' ' . $width . ' name="' . $entityfield['fieldid'] . '" id="' . $entityfield['fieldid'] . '" type="' . $inputtype . '" value="' . $entityfield['value'] . '" onchange="updateModifiedFlag();"></td>';
			}
			echo "\n";
			
			echo "				</tr>\n";
		}
?>
		</table>
		</td></tr></table>
		
		<input type="hidden" name="_entityid" id="_entityid" value="<?php echo $entityid; ?>">
		<input type="hidden" name="primarykey" id="primarykey" value="<?php echo $primarykey; ?>">
		<input type="hidden" name="primarykey2" id="primarykey2" value="<?php echo $primarykey2; ?>">
		<input type="hidden" name="primarykey3" id="primarykey3" value="<?php echo $primarykey3; ?>">
		<input type="hidden" name="primarykey3" id="primarykey3" value="<?php echo $primarykey3; ?>">
		<input type="hidden" name="dataModified" id="dataModified" value=false>
		<input type="hidden" name="sessionid" id="sessionid" value="<?php echo $EmpId; ?>">
		
	</form>
	

	
	<table border="0" align="left">
		<tr>
			<td><button class="button button1" type="submit" form="entityform" value="Save" onclick="">Save</button></td>
			<td><button class="button button1" type="button" value="Quit" onclick="quitWithoutSaving();">Quit</button></td>
		</tr>
	</table>
</body>
</html>