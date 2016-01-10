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
		$error = "Administrators have not been programmed to log into this database yet."; #Deny it.
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
	<title>Login</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha512.js"></script>
	<script src="/script/jquery.min.js"></script> <!-- Why do I have to use theseeee. -->
	<script src="/script/login.js"></script>
	<link rel="stylesheet" type="text/css" href="/css/style.css">
</header>
<body>
	<div id="main">
		<div id="login">
			<h1>Attendance Viewer</h1>
			<form class="login">
				<h3>Room name: </h3><div class="padding"><input type="text" id="room" name="room"></div><br>
				<h3>Password: </h3><div class="padding"><input type="password" id="password" name="password"></div><br>
				<div class="padding"><input type="submit" value=" Login "></div>
				<span id="error"><?php echo $error?></span>
			</form>
		</div>
	</div>
</body>
</html>