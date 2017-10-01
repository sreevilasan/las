<!DOCTYPE html>
<?php
	// File: EntitySearch.php 
	// 
	//error_log("test", 3, "C:\myfolder\php_errors.log");
	
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';
	require 'include/Header.php';
	
	// get data from GET variables
	$entityid = $_GET['entityid']; 
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//echo "Loaded via Posting method</br>";
		$entityid = $_POST['_entityid'];
	}
	
	require 'include/GetEntityFields.php';
	
	// make entity sql query
	$db = new Database();	// open database
	
	$entitysql = "";
	foreach ($entityfields as $entityfield)
	{
			$entitysql = $entitysql . $entityfield['fieldid'] . " , ";
	}
	$entitysql = substr($entitysql, 0, (strlen($entitysql) - 2));
	
	$filtermap;
	$entityfilterclause = "where 1=1 ";
	
	foreach ($entityfields as $entityfield)
	{
		//$entitysql = $entitysql . $entityfield['fieldid'] . " , ";
		if($entityfield['search'] == "Y") {
			$filtervalue = "";
			$filtervalue = $_GET['filter_' . $entityfield['fieldid']]; 
			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$filtervalue = $_POST['filter_' . $entityfield['fieldid']];
			}
			
			$tempfilter['fieldid'] = $entityfield['fieldid'];
			$tempfilter['filteroperator'] = $entityfield['filteroperator'];
			$tempfilter['value'] = $filtervalue;
			$filtermap[$entityfield['fieldid']] = $tempfilter; 
			
			$tempfilteroperator = "=";
			if ($tempfilter['filteroperator'] != ""){
				$tempfilteroperator = $tempfilter['filteroperator'];
			}
		
			if ($filtervalue != "") {
				if ($entityfield['displaytype'] == 'entity') {
					$tempfiltervalue = $filtervalue;
				} else if ($entityfield['displaytype'] == 'dropdown') {
					$tempfiltervalue = $filtervalue;
				} else if ($entityfield['displaytype'] == 'function') {
					$tempfiltervalue = $filtervalue;
				} else if ($entityfield['displaytype'] == 'date') {
					$tempfiltervalue = $filtervalue;
				} else {
					if ($filtervalue[0] == '^') {
						$tempfiltervalue = strtolower(str_replace("^","",$filtervalue) . '%');
					} else {
						$tempfiltervalue = strtolower('%'.$filtervalue . '%');
					}
					$tempfilteroperator = "like";
				}
				$entityfilterclause = $entityfilterclause . " and lower("  . $tempfilter['fieldid'] . ") " . $tempfilteroperator . " '" . $tempfiltervalue . "'";
			}
		}
	}

	
	$entitysql = "SELECT " . $entitysql . "FROM " . $entityprimtable . " " . $entityfilterclause . ";";
echo $entitysql;
	$rows = $db->select($entitysql);
	if ($db->getError() != "") {
		echo $db->getError();
		exit();
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
		function quit() {
			document.location = "DbMain.php"; // go to entity main page
		}
		
		function edit() {
			document.location = "EntityAddUpd.php?entityid=" + document.getElementById('_entityid').value + "&primarykey=" + document.getElementById('primarykey').value;
		}
		function addnew() {
			document.location = "EntityAddUpd.php?entityid=" + document.getElementById('_entityid').value;
		}
		
		function deleteEntity(entityid, primarykey, entitydescription, objectdescription) {
			if (confirm("Do you want to delete " + entitydescription + " : " + objectdescription) == true) {
				var xmlhttp = new XMLHttpRequest();
				
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						//activityId.innerHTML = this.responseText;
						document.getElementById(primarykey).style.display = "none";
					}
				};

				xmlhttp.open("GET", "EntityDelete.php?entityid=" + entityid + "&primarykey=" + primarykey, true);
				xmlhttp.send();
			} 

		}
		
		function popitup(refentityid, fieldid) {
			newwindow=window.open("EntitySearchHandler.php?entityid=" + refentityid + "&fieldid=" + fieldid,'_blank','left=400,top=50,height=500,width=600,titlebar=no,toolbar=no,location=no');
			if (window.focus) {newwindow.focus()}
			return false;
		}
		
		function HandlePopupResult(entityid, fieldid, result, description) {
			document.getElementById('filter_' + fieldid).value = result;
			document.getElementById(entityid + "_" + fieldid + "_desc").innerHTML = description;
		}
	</script>
</head>
	
<body onload=";">

	<table class="tabinput" border="0">
		<tr>
			<td colspan="2">
				<h1><?php echo $entitydescription; ?></h1>
			</td>
			<td><button class="button button1" type="button" value="add" onclick="addnew();">Add New <?php echo $entitydescription; ?></button></td>
			<td><button class="button button1" type="button" value="Quit" onclick="quit();">Quit</button></td>
		</tr><td>
	
<?php if (isSearchable($entityid) == "true") { ?>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="filterform" onsubmit=";">
		<input type="hidden" name="_entityid" id="_entityid" value="<?php echo $entityid; ?>">
		<table class="tabinput" border="1">
			<tr>
			
<?php

			if ($entityview = 'Y') {
				echo '<th rowspan="2">View</th>';
			}
			
			if ($entityedit = 'Y') {
				echo '<th rowspan="2">Edit</th>';
			}
			if ($entitydelete = 'Y') {
				echo '<th rowspan="2">Delete</th>';
			}
			foreach ($entityfields as $entityfield)
			{
				if($entityfield['search'] == "Y") {
					echo '					<th>' . $entityfield['description'] . '</th>';
				}
			}
?>
			<td rowspan="2"><button class="button button1" type="submit" form="filterform" value="Search" onclick=";">Search</button></td>
<?php			
			echo '</tr><tr>';
			foreach ($entityfields as $entityfield)  // Fliter list
			{
				if($entityfield['search'] == "Y") {
					echo '<th>';
					if ($entityfield['displaytype'] == 'entity') {
						echo '<input size="' . $entityfield['width'] . '" name="filter_' . $entityfield['fieldid'] . '" id="filter_' . $entityfield['fieldid'] . '" value="' . $entityfield['value'] . '" > <button type="button" onclick="popitup(\'' . $entityfield['refentityid'] . '\', \'' . $entityfield['fieldid'] . '\');">...</button> ';
						echo '<span id="' . $entityfield['refentityid'] . '_' . $entityfield['fieldid'] . '_desc" >' . getEntityDescription($entityfield['refentityid'], $entityfield['value']) . '</span>';
					} else if ($entityfield['displaytype'] == 'dropdown') {
						echo '<select name="filter_' . $entityfield['fieldid'] . '" id="filter_' . $entityfield['fieldid'] . '" >' . createDropDownString($entityfield['reftable'], $entityfield['refvalcol'], $entityfield['refdescol'], $filtermap[$entityfield['fieldid']]['value'], ""). '</select>';
					} else {
						echo '<input type="text" size="' . $entityfield['width'] . '" name="filter_' . $entityfield['fieldid'] . '" id="filter_' . $entityfield['fieldid'] . '" value="' . $filtermap[$entityfield['fieldid']]['value'] . '">';
					}
					echo '</th>';
				}
			}
?>
				
			</tr>
	</form>
<?php } ?>

<?php
		foreach ($rows as $row)		//for all selected rows
		{
			echo '				<tr id="' . $row[$entityprimcol] . '">';
			
			if ($entityview == 'Y') {
				echo '<td><a href="EntityDisplay.php?entityid=' . $entityid . '&primarykey=' . $row[$entityprimcol] . '">View</a></td>';
			}
			
			if ($entityedit == 'Y') {
				echo '<td><a href="EntityAddUpd.php?entityid=' . $entityid . '&primarykey=' . $row[$entityprimcol] . '">Edit</a></td>';
			}

			if ($entitydelete == 'Y') {
				echo '<td><a href="javascript:deleteEntity(\'' . $entityid . '\', \'' . $row[$entityprimcol] . '\', \'' . $entitydescription . '\', \'' . $row[$entitydescol] . '\')">Delete</a></td>';
			}	
			
			foreach ($entityfields as $entityfield) {
				if ($entityfield['fieldid'] == $entityprimcol) {
					//$disabled = "disabled";
				}
				if($entityfield['search'] == "Y") {
					echo '<td>';
					if ($entityfield['displaytype'] == 'entity') {
						echo getEntityDescription($entityfield['refentityid'], $row[$entityfield['fieldid']]);
					} else if ($entityfield['displaytype'] == 'dropdown') {
						echo getDropdownValue($entityfield['reftable'], $entityfield['refvalcol'], $entityfield['refdescol'], $row[$entityfield['fieldid']]);
					} else {
						echo $row[$entityfield['fieldid']];
					}
					echo '</td>';
				}		
			}
			echo "\n";
			echo "				</tr>\n";
		}
?>
	</table>
	</td></tr></table>


	<table border="0" align="left">
		<tr>
			<td><button class="button button1" type="button" value="add" onclick="addnew();">Add New <?php echo $entitydescription; ?></button></td>
			<td><button class="button button1" type="button" value="Quit" onclick="quit();">Quit</button></td>
		</tr>
	</table>
</body>
</html>