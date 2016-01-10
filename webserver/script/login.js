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
	var sha512 = CryptoJS.algo.SHA512.create();
	if(!(response.split("\n")[0] == "OK")) {
		document.getElementById("error").innerHTML = response.split("\n")[0];
		return false;
	}
	sha512.update(response.split("\n")[2]);
	sha512.update(password);
	
	var hash = sha512.finalize();
	
	post("http://attendance.yhscs.us/index.php", {ROOM: room, ROOM_PASSWORD: hash})
    return false;  //idk how this works.
});

//This was taken from http://stackoverflow.com/a/133997 with care.
function post(path, params, method) {
    method = method || "post"; // Set method to post by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
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