function reLoadForm(a) {
alert(a);
	var reloadCat = document.getElementById("MR-SummaryForm").action;
	document.location = reloadCat + a;
}