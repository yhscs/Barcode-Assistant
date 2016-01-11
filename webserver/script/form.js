$(function() {
$("#name").attr("disabled", true);
$("#grade").attr("disabled", true);
$("#student_id").attr("disabled", true);
$("#time_start").attr("disabled", true);
$("#time_end").attr("disabled", true);

$(".none").click(function() {
    $("#name").attr("disabled", true);
	$("#grade").attr("disabled", true);
	$("#student_id").attr("disabled", true);
	$("#time_start").attr("disabled", true);
	$("#time_end").attr("disabled", true);
});
$(".stname").click(function() {
    $("#name").attr("disabled", false);
	$("#grade").attr("disabled", true);
	$("#student_id").attr("disabled", true);
	$("#time_start").attr("disabled", true);
	$("#time_end").attr("disabled", true);
});
$(".stgrade").click(function() {
    $("#name").attr("disabled", true);
	$("#grade").attr("disabled", false);
	$("#student_id").attr("disabled", true);
	$("#time_start").attr("disabled", true);
	$("#time_end").attr("disabled", true);
});
$(".stid").click(function() {
    $("#name").attr("disabled", true);
	$("#grade").attr("disabled", true);
	$("#student_id").attr("disabled", false);
	$("#time_start").attr("disabled", true);
	$("#time_end").attr("disabled", true);
});
$(".time").click(function() {
    $("#name").attr("disabled", true);
	$("#grade").attr("disabled", true);
	$("#student_id").attr("disabled", true);
	$("#time_start").attr("disabled", false);
	$("#time_end").attr("disabled", false);
});
});