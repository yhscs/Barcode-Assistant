$(function() {
$('.login').on('submit', function () {
	var room = document.getElementById("room").value;
	var password = document.getElementById("password").value;
	response = $.ajax({
		url: "http://attendance.yhscs.us/db.php",
		type:"POST",
		data: {REQUEST: "SALTY_MC_SALTER", USER: room},
		async: false
	}).responseText;
	var shaObj = new jsSHA("SHA-512", "TEXT");
	
	if(!(response.split("\n")[0] == "OK")) {
		document.getElementById("error").innerHTML = response.split("\n")[0];
		return false;
	}
	shaObj.update(response.split("\n")[2]);
	shaObj.update(password);
	
	var hash = shaObj.getHash("HEX");
	
	post("http://attendance.yhscs.us/index.php", {ROOM: room, ROOM_PASSWORD: hash})
    return false;
});

//This was taken from http://stackoverflow.com/a/133997 with care.
function post(path, params, method) {
    method = method || "post";
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);
    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);
            form.appendChild(hiddenField);
         }
    }
    document.body.appendChild(form);
    form.submit();
}
}); //I hate javascript