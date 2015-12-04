<?php

class Constants {
	const INDEX_KEY_REQUEST="REQUEST";
	const INDEX_KEY_ROOM="ROOM";
	const INDEX_KEY_ROOM_PASSWORD="ROOM_PASSWORD";
	const INDEX_KEY_ADMIN="ADMIN";
	const INDEX_KEY_ADMIN_PASSWORD="ADMINISTRATOR_PASSWORD";
	const INDEX_KEY_SALT = "SALTY_MC_SALTER";
		
	const REQUEST_CREATE="CREATE_ROOM";	
}

include '/home/aj4057/config.php'; //Define $servername $username $password $dbname and $configready here.

try {
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
} catch(PDOException $e) {
	echo "NOT_READY";
}

if (!empty($_POST)) {
    if(array_key_exists(Constants::INDEX_KEY_REQUEST,$_POST)) {
		if($_POST[Constants::INDEX_KEY_REQUEST] === Constants::REQUEST_CREATE) {
			echo "OK";
		} else {
			echo "...OK?";
		}
	} else {
		echo "NO_KEY";
	}
} else /*$_POST is empty.*/ {
}
?>