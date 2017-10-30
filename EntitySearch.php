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
	
	$entityaccess = new EntityAccess($entityid, $UserRole);
	
	if($entityaccess->hasReadAccess()) {
		$entityview = 'Y';
	} else {
		echo "<b>Access Denied</b> <br><br>";
		echo "You don't have required permission for entity : " . $entityid;
		exit();
	}
	
	if($entityaccess->hasEditAccess()) {
		$entityedit = 'Y';
	} else {
		$entityedit = 'N';
	}
	
	if($entityaccess->hasDeleteAccess()) {
		$entitydelete = 'Y';
	} else {
		$entitydelete = 'N';
	}

	if($entityaccess->hasAddAccess()) {
		$entityadd = 'Y';
	} else {
		$entityadd = 'N';	
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
	$entitysortclause = "";
	
	foreach ($entityfields as $entityfield)
	{
		//$entitysql = $entitysql . $entityfield['fieldid'] . " , ";
		if($entityfield['search'] == "Y") {
			$filtervalue = "";
			$filtervalue = $_GET['filter_' . $entityfield['fieldid']]; 
			$sortvalue = $_GET['sort']; 
			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$filtervalue = $_POST['filter_' . $entityfield['fieldid']];
				$sortvalue = $_POST['_sort'];
				$sortorder = $_POST['_order'];
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

	$entitysortclause = $sortvalue;
	$entitysql = "SELECT " . $entitysql . "FROM " . $entityprimtable . " " . $entityfilterclause ;
	if ($sortvalue != "") {
		$entitysql .= " order by " . $entitysortclause;
		if ($sortorder != "") {
			$entitysql .= $sortorder;
			$sortorder = "";
		} else {
			$sortorder = " desc ";
		}
	}
	$entitysql .= ";";

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
		function displayImage(img) {
			newwindow = window.open(img,'_blank','left=400,top=50,height=500,width=650,titlebar=no,toolbar=no,location=no');
		}
		function sorta(a) {
			document.getElementById("_sort").value = a.substring(5);
			var reloadurl = document.getElementById("filterform").action;
			document.getElementById("filterform").submit();
			//document.location = reloadurl + "?entityid=" + document.getElementById("_entityid").value+"&sort="+a.substring(5);
		}
	</script>
</head>
	
<body onload=";">

	<table class="tabinput" border="0">
		<tr>
			<td colspan="2">
				<h1><?php echo $entitydescription; ?></h1>
			</td>
<?php if ($entityadd == "Y") { ?>
			<td><button class="button button1" type="button" value="add" onclick="addnew();">Add New <?php echo $entitydescription; ?></button></td>
<?php } ?>
<?php		
			if ($entityextrabutton != "") {	// add extra button for the entity
				echo '<td>' . addExtraButton($entityextrabutton) . '</td>';
			}
?>			
			<td><button class="button button1" type="button" value="Quit" onclick="quit();">Quit</button></td>
		</tr><td>
	
<?php if (isSearchable($entityid) == "true") { ?>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="filterform" onsubmit=";">
		<input type="hidden" name="_entityid" id="_entityid" value="<?php echo $entityid; ?>">
		<input type="hidden" name="_sort" id="_sort" value="<?php echo $sortvalue; ?>">
		<input type="hidden" name="_order" id="_order" value="<?php echo $sortorder; ?>">
		<table class="tabinput" border="1">
			<tr>
			
<?php
//echo "values=".$entityview.$entityedit.$entitydelete.$entityadd;
			if ($entityview == 'Y') {
				echo '<th rowspan="2">View</th>';
			}

			if ($entityedit == 'Y') {
				echo '<th rowspan="2">Edit</th>';
			}
			if ($entitydelete == 'Y') {
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
						echo '<input size="' . $entityfield['width'] . '" name="filter_' . $entityfield['fieldid'] . '" id="filter_' . $entityfield['fieldid'] . '" value="' . $filtermap[$entityfield['fieldid']]['value'] . '" > <button type="button" onclick="popitup(\'' . $entityfield['refentityid'] . '\', \'' . $entityfield['fieldid'] . '\');">...</button> ';
						echo '<span id="' . $entityfield['refentityid'] . '_' . $entityfield['fieldid'] . '_desc" >' . getEntityDescription($entityfield['refentityid'], $filtermap[$entityfield['fieldid']]['value']) . '</span>';
					} else if ($entityfield['displaytype'] == 'dropdown') {
						echo '<select name="filter_' . $entityfield['fieldid'] . '" id="filter_' . $entityfield['fieldid'] . '" >' . createDropDownString($entityfield['reftable'], $entityfield['refvalcol'], $entityfield['refdescol'], $filtermap[$entityfield['fieldid']]['value'], ""). '</select>';
					} else if ($entityfield['displaytype'] == 'date') {
						echo '<input type="date" size="' . $entityfield['width'] . '" name="filter_' . $entityfield['fieldid'] . '" id="filter_' . $entityfield['fieldid'] . '" value="' . $filtermap[$entityfield['fieldid']]['value'] . '">';
					} else {
						if ($sortorder != "") {
							$sortsymbol = "&#8681;";
						} else {
							$sortsymbol = "&#8679;";
						}
						echo '<input type="text" size="' . $entityfield['width'] . '" name="filter_' . $entityfield['fieldid'] . '" id="filter_' . $entityfield['fieldid'] . '" value="' . $filtermap[$entityfield['fieldid']]['value'] . '">';
						echo '<button type="button" name="sort_' . $entityfield['fieldid'] . '" id="sort_' . $entityfield['fieldid'] . '" value="' . $entityfield['fieldid'] . '" onclick="sorta(' . 'name' .');">' . $sortsymbol . '</button>';
					}
					echo '</th>';
				}
			}
?>				
			</tr>
	</form>
<?php } ?>

<?php
		foreach ($rows as $row)		//display all selected rows 
		{
			echo '				<tr id="' . $row[$entityprimcol] . '">';
			
			if ($entityview == 'Y') {
				echo '<td><a href="EntityDisplay.php?entityid=' . $entityid . '&primarykey=' . $row[$entityprimcol] . '&primarykey2=' . $row[$entityprimcol2] . '&primarykey3=' . $row[$entityprimcol3] . '&primarykey4=' . $row[$entityprimcol4] . '">View</a></td>';
			}
			
			if ($entityedit == 'Y') {
				echo '<td><a href="EntityAddUpd.php?entityid=' . $entityid . '&primarykey=' . $row[$entityprimcol] . '&primarykey2=' . $row[$entityprimcol2] . '&primarykey3=' . $row[$entityprimcol3] . '&primarykey4=' . $row[$entityprimcol4] . '">Edit</a></td>';
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
					} else if ($entityfield['displaytype'] == 'date') {
						$date=date_create($row[$entityfield['fieldid']]);
						echo date_format($date,"d-M-Y");
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
<?php if ($entityadd == "Y") { ?>
			<td><button class="button button1" type="button" value="add" onclick="addnew();">Add New <?php echo $entitydescription; ?></button></td>
<?php } ?>
<?php		
			if ($entityextrabutton != "") {	// add extra button for the entity
				echo '<td>' . addExtraButton($entityextrabutton) . '</td>';
			}
?>
			<td><button class="button button1" type="button" value="Quit" onclick="quit();">Quit</button></td>
		</tr>
	</table>
</body>
</html>