<?php

class Constants {
	const INDEX_KEY_REQUEST="REQUEST";
	const INDEX_KEY_ROOM="ROOM";
	const INDEX_KEY_ROOM_PASSWORD="ROOM_PASSWORD";
	const INDEX_KEY_ROOM_SALT="ROOM_SALT";
	const INDEX_KEY_ADMIN="ADMIN";
	const INDEX_KEY_ADMIN_PASSWORD="ADMINISTRATOR_PASSWORD";
	
	const REQUEST_SALT = "SALTY_MC_SALTER";
	const REQUEST_CREATE="CREATE_ROOM";	
}

include '/home/aj4057/config.php'; //Define $servername $username $password $dbname and $configready here.

try {
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	echo "NOT_READY";
	die();
}

if (!empty($_POST)) {
    if(array_key_exists(Constants::INDEX_KEY_REQUEST,$_POST)) {
		switch($_POST[Constants::INDEX_KEY_REQUEST]) {
			case Constants::REQUEST_CREATE;
				echo "CREATE";
				break;
			case Constants::REQUEST_SALT;
				echo "OK" . "\n";
				$stmt = $conn->prepare("SELECT USERNAME, SALT FROM USERS WHERE USERNAME = :name");
				$stmt->execute(array('name' => $_POST[Constants::INDEX_KEY_ADMIN]));
				$row = $stmt->fetch();
				echo $_POST[Constants::INDEX_KEY_ADMIN] . "\n";
				echo $row['SALT'] . "\n";
				break;
			case null;
				echo "...";
				break;
		}
	} else {
		echo "NO_KEY";
	}
} else /*$_POST is empty.*/ {
	echo "READY";
}
?>