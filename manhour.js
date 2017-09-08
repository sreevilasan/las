/*
File: manhour.js
Description: Contains all javascript functions for manhours page
*/

function loadDate() {
	if (document.getElementById("actDate").value == "") {
		document.getElementById("actDate").valueAsDate = new Date();
	}
	
}

function checkHours(a) {
	if (isNaN(a.value)) {
		alert("Only numeric values allowed");
		a.value = "";
		a.focus();
	} else if (a.value < 0 || a.value > 24){
		alert("Hours should be between 0 to 24");
		a.value = "";	
		a.focus();					
	} else if (a.value ==  ""){
		;
	} else {
		a.value = Math.trunc(a.value * 10) /10;
	}
	a.style.backgroundColor = "yellow";
}

function updateModifiedFlag(a){
	var hourId = a.id;
	document.getElementById('modifiedHourFlg' + hourId.substr(4)).value = true;
	document.getElementById('dataModified').value = true;
}

function calculateTotal(){				
	for (j = 0; j < document.getElementById("noofrows").value; j++) {
		rowId = 'rowTotal_' + j;

		var rowSum = 0;
		for (i = 0; i < document.getElementById("daysInMonth").value; i++) {

			hourId = 'hour_' + j + '_' + i; 

			if (document.getElementById(hourId).value != "") {
				rowSum += parseInt(document.getElementById(hourId).value);
			}
		}	
		document.getElementById(rowId).value = rowSum;
	}	
	
	for (i = 0; i < document.getElementById("daysInMonth").value; i++) {
		colId = 'colTotal_'+ i;
		var colSum = 0;
		for (j = 0; j < document.getElementById("noofrows").value; j++) {
			hourId = 'hour_' + j + '_' + i; 
			if (document.getElementById(hourId).value != "") {
				colSum += parseInt(document.getElementById(hourId).value);
			}
		}
		document.getElementById(colId).value = colSum;
	}
	
	var grandSum = 0;
	for (j = 0; j < document.getElementById("daysInMonth").value; j++) {
		colId = 'colTotal_' + j;
		grandSum += parseInt(document.getElementById(colId).value);
	}
	document.getElementById("grandTotal").value = grandSum;
}

function checkTotal(a) {
	var hourId = a.id;
	var temp = hourId.split("_");
	var colTotalId = 'colTotal_' + temp[2];

	if (document.getElementById(colTotalId).value > 24 ) {
		alert("Total hours for a day should be between 0 to 24");
		document.getElementById(colTotalId).style.backgroundColor = "red";
		a.focus();
	} else {
		document.getElementById(colTotalId).style.backgroundColor = "lightgray";
	}
}

/*
function daysInMonth(anyDateInMonth) {
	return new Date(anyDateInMonth.getYear(), anyDateInMonth.getMonth()+1,0).getDate();
}
*/

function doReload(actDate){	
	var view = document.getElementById("view").value;
	if(view == "approver") {
		var subemp = document.getElementById("subemp").value;
		var reloadurl = document.getElementById("mhourform").action;
		document.location = reloadurl + '?view=approver&actDate=' + actDate + '&EmpId=' + subemp;	
	} else {
		var reloadurl = document.getElementById("mhourform").action;
		document.location = reloadurl + '?actDate=' + actDate;
	}
}

function doReloadEmployee(a){
	var actDate = document.getElementById("actDate").value;
	var subemp = a.value;
	var reloadurl = document.getElementById("mhourform").action;
	document.location = reloadurl + '?view=approver&actDate=' + actDate + '&EmpId=' + subemp;
}

function enableDropdowns() {
	for (i = 0; i < document.getElementById("noofrows").value; i++) {
		document.getElementById("prjId_" + i).disabled = false;
		document.getElementById("deptId_" + i).disabled = false;
		document.getElementById("activityId_" + i).disabled = false;
	}
}

function enableHoursOnRow(a) {
	var dropdownId = a.id;
	var temp = dropdownId.split("_");
	var rowid = temp[1];

	var prjId = document.getElementById("prjId_" + rowid).value;
	var deptId = document.getElementById("deptId_" + rowid).value;
	var activityId = document.getElementById("activityId_" + rowid).value;

	if(prjId != "" && deptId != "" && activityId != "") {
		for (i = 0; i < document.getElementById("daysInMonth").value; i++) {
			document.getElementById("hour_" + rowid + "_" + i).disabled = false;
		}
	}
}

function checkDuplicateRow(a) {
	var dropdownId = a.id;
	var temp = dropdownId.split("_");
	var rowid = temp[1];

	var prjId = document.getElementById("prjId_" + rowid).value;
	var deptId = document.getElementById("deptId_" + rowid).value;
	var activityId = document.getElementById("activityId_" + rowid).value;

	
	for (i = 0; i < document.getElementById("noofrows").value; i++) {
		if(i != rowid) {
			var tempprj = document.getElementById("prjId_" + i).value;
			var tempdept = document.getElementById("deptId_" + i).value;
			var tempact = document.getElementById("activityId_" + i).value;
			if((tempprj == prjId) && (tempdept == deptId) && (tempact == activityId)) {
				alert("Project, Department, Activity combination already present");
				a.value = a.oldvalue;
			}
		}
	}
	a.oldvalue = a.value;
}


function cloneRow() {
	var table = document.getElementById("hourtable");
	var rowcount = parseInt(document.getElementById("noofrows").value);
	var row = table.insertRow(rowcount + 2);
	var row2 = document.getElementById("clonerow");
	row.id = "mhrow_"+rowcount;
	row.name = "mhrow_"+rowcount;
	for (i = 0; i < row2.cells.length; i++) {
		var cell1 = row.insertCell(i);
		cell1.innerHTML = row2.cells[i].innerHTML;
		var cloneid = row2.cells[i].children[0].id;
		
		var temp = cloneid.split("_");
		var newId = temp[0] + "_" + rowcount;
		if(temp.length > 2) {
			newId = newId + "_" + temp[2];
			var input = document.createElement("input");
			input.setAttribute("type", "hidden");
			input.setAttribute("name", "modifiedHourFlg_" + rowcount + "_" + temp[2]);
			input.setAttribute("id", "modifiedHourFlg_" + rowcount + "_" + temp[2]);
			input.setAttribute("value", "false");
			document.getElementById("mhourform").appendChild(input);
		}
		cell1.children[0].id = newId;
		cell1.children[0].name = newId;					
	}
	
	document.getElementById("noofrows").value = rowcount + 1;
}

function toggleDelete(a) {
	var deleteChkBx = a.id;
	var temp = deleteChkBx.split("_");
	var rowid = temp[1];
	
	if(a.checked) {
		for (i = 0; i < document.getElementById("daysInMonth").value; i++) {
			document.getElementById("hour_" + rowid + "_" + i).disabled = true;
		}
	} else {
		for (i = 0; i < document.getElementById("daysInMonth").value; i++) {
			document.getElementById("hour_" + rowid + "_" + i).disabled = false;
		}
	}
}

function deleteSelectedRows() {
	var rowcount = parseInt(document.getElementById("noofrows").value);
	for (i = 0; i < rowcount; i++) {
		if(document.getElementById("deleteChkBx_" + i).checked) {	
			//document.getElementById("mhrow_" + i).hidden = true; // remove the row by hidden=true for the tr of row
			document.getElementById("mhrow_" + i).style.display = "none"; // remove the row by style.display="none" for the tr of row
			document.getElementById("deleteChkBx_" + i).disabled = true;
			for (j = 0; j < document.getElementById("daysInMonth").value; j++) {
				document.getElementById("hour_" + i + "_" + j).disabled = true;
				if(document.getElementById("hour_" + i + "_" + j).value != "") {
					document.getElementById("modifiedHourFlg_" + i + "_" + j).value = true;
				}
			}	
		}
	}
}

function submitHours() {
	
	var txt;
	if (confirm("Do you really want to submit man hours for approval?") == true) {
		document.getElementById("isSubmit").value = true;
		enableDropdowns();
		document.getElementById("mhourform").submit();
		txt = "Manhours submitted for approval";
		// send mail to Manager
	} else {
		txt = "Manhours not submitted.";
	}
	//alert(txt);
}

function approveHours() {
	var txt;
//alert(document.getElementById("mhourform").action);
	document.getElementById("isApproved").value = true;
	//enableDropdowns(); //check if needed
	document.getElementById("mhourform").submit();
	txt = "Manhours approved";
	// send mail to Employee

	//alert(txt);
}

function rejectHours() {
	var txt;

	document.getElementById("isRejected").value = true;
	//enableDropdowns();
	document.getElementById("mhourform").submit();
	txt = "Manhours approved";
	// send mail to Employee

	//alert(txt);
}

function quitWithoutSaving() {
	if (document.getElementById('dataModified').value == "true") {
		if (confirm("Data not saved. Do you really want to quit without saving modified values?") == true) {
			document.location = "DbMain.php"; // go to lasdb link
		} 	
	} else {
		document.location = "DbMain.php"; // go to lasdb link
	}
}

function populateActDrpDwn(a) {
	//alert(a.innerHTML);
	var dropdownId = a.id;
	var temp = dropdownId.split("_");
	var rowid = temp[1];

	var deptId = document.getElementById("deptId_" + rowid).value;
	var activityId = document.getElementById("activityId_" + rowid);
	//alert(activityId.innerHTML);
	

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			activityId.innerHTML = this.responseText;
		}
	};
	xmlhttp.open("GET", "activitydropdown.php?deptId=" + deptId, true);
	xmlhttp.send();

}