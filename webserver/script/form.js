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
	
	var date_start = document.getElementById("date_start").value;
	var date_end = document.getElementById("date_end").value;
	
	if (date_start == "" || date_start.length == 0 || date_start == null){ date_start = false;} else {date_start = true;}
	if (date_end == "" || date_end.length == 0 || date_end == null){ date_end = false;} else {date_end = true;}
	
	if ((date_start == false) && (date_end == false)) {
		$("#date_start").attr("disabled", "disabled");
		$("#date_end").attr("disabled", "disabled");
	}
	
});
});