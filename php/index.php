<?php
include '/home/aj4057/config.php'; #Define $servername $username $password $dbname and $configready here.
include '/home/aj4057/indexkeys.php'; #Index keys that are used. For example, Index::REQUEST is defined here.

do {
session_start(); #Starting Session
$error=''; // Variable To Store Error Message

try {
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	$error = "Could not connect to the database. This should never happen.";
	break;
}

if (!empty($_POST)) {
	if(!(array_key_exists(Index::ROOM,$_POST)
	  && array_key_exists(Index::ROOM_PASSWORD,$_POST))) {
		$error = "The username or password was not set.";
		break;
	}
	if($_POST[Index::ROOM] === '' || $_POST[Index::ROOM_PASSWORD] === '' ) {
		$error = "The username or password was not set.";
		break;
	}
	$stmt = $conn->prepare("SELECT USERNAME, PASSWORD, ISADMIN FROM USERS WHERE USERNAME = :room");
	$stmt->execute(array('room' => $_POST[Index::ROOM])); #based on the room
	$row = $stmt->fetch();
	if($row["ISADMIN"] === "1") { #If the account is an admin
		$error = "Administrators have not been programed to log into this database yet."; #Deny it.
		break;
	}
	if($row["PASSWORD"] !== $_POST[Index::ROOM_PASSWORD]) { #if the password hash is not the stored hash
		$error = "The username or password is incorrect!"; #Deny it.
		break;
	}
	$_SESSION['login_user'] = $_POST[Index::ROOM]; #Initializing Session
	header("location: view.php"); // Redirecting To Other Page
	$conn = null;
}

if(isset($_SESSION['login_user'])){
	header("location: view.php");
}
} while (0); #but it works!
?>
<!DOCTYPE html>
<html>
<header>
	<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha512.js"></script>
	<script src="http://attendance.yhscs.us/script/jquery.min.js"></script> <!-- Why do I have to use theseeee. -->
</header>
<body>
	<form class="login">
		Room name: <input type="text" id="room" size="20"><br>
		Password: <input type="text" id="password" size="20"><br>
		<input type="submit" value="Submit"> 
	</form>
	<span><?php echo $error?></span>
	<script>
$('.login').on('submit', function () {
	var room = document.getElementById("room").value;
	var password = document.getElementById("password").value;
	response = $.ajax({
		url: "http://attendance.yhscs.us/db.php",
		type:"POST",
		data: {REQUEST: "SALTY_MC_SALTER", ROOM: room},
		async: false
	}).responseText;
	var sha512 = CryptoJS.algo.SHA512.create();
	
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
</script>
</body>
</html>