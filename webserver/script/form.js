$(function() {
$("#name").attr("disabled", false);
$("#grade").attr("disabled", false);
$("#student_id").attr("disabled", false);
$("#period").attr("disabled", false);
$("#time_start").attr("disabled", false);
$("#time_end").attr("disabled", false);

$('form#filter').submit(function() {
	if (!$("#name").val()) {
		$("#name").attr("disabled", "disabled");
	}
	
	if (!$("#grade").val()) {
		$("#grade").attr("disabled", "disabled");
	}
	
	if (!$("#student_id").val()) {
		$("#student_id").attr("disabled", "disabled");
	}
	
	if (!$("#period").val()) {
		$("#period").attr("disabled", "disabled");
	}
	
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	
	if (start_date == "" || start_date.length == 0 || start_date == null){ start_date = false;} else {start_date = true;}
	if (end_date == "" || end_date.length == 0 || end_date == null){ end_date = false;} else {end_date = true;}
	
	if ((start_date == false) && (end_date == false)) {
		$("#start_date").attr("disabled", "disabled");
		$("#end_date").attr("disabled", "disabled");
	}
	
});
});