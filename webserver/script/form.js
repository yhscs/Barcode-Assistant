$(function() {
$("#name").attr("disabled", true);
$("#grade").attr("disabled", true);
$("#student_id").attr("disabled", true);
$("#period").attr("disabled", true);
$("#time_start").attr("disabled", true);
$("#time_end").attr("disabled", true);

$('form#filter').submit(function() {
    $("#stname").attr("disabled", true);
	$("#stgrade").attr("disabled", true);
	$("#stid").attr("disabled", true);
	$("#period_val").attr("disabled", true);
	$("#time").attr("disabled", true);
});
$("#stname").click(function() {
	if(document.getElementById('stname').checked) {
		$("#name").attr("disabled", false);
	} else {
		$("#name").attr("disabled", true);
	}
});
$("#stgrade").click(function() {
	if(document.getElementById('stgrade').checked) {
		$("#grade").attr("disabled", false);
	} else {
		$("#grade").attr("disabled", true);
	}
});
$("#stid").click(function() {
	if(document.getElementById('stid').checked) {
		$("#student_id").attr("disabled", false);
	} else {
		$("#student_id").attr("disabled", true);
	}
});
$("#period_val").click(function() {
	if(document.getElementById('period_val').checked) {
		$("#period").attr("disabled", false);
	} else {
		$("#period").attr("disabled", true);
	}
});
$("#time").click(function() {
	if(document.getElementById('time').checked) {
		$("#time_start").attr("disabled", false);
		$("#time_end").attr("disabled", false);
	} else {
		$("#time_start").attr("disabled", true);
		$("#time_end").attr("disabled", true);
	}
});
});