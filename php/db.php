<?php

class Constants {
	const INDEX_KEY_REQUEST="REQUEST";
	const INDEX_KEY_ROOM="ROOM";
	const INDEX_KEY_ROOM_PASSWORD="ROOM_PASSWORD";
	const INDEX_KEY_ROOM_SALT="ROOM_SALT";
	const INDEX_KEY_ADMIN="ADMIN";
	const INDEX_KEY_ADMIN_PASSWORD="ADMINISTRATOR_PASSWORD";
	const INDEX_KEY_USER="USER";
	
	const REQUEST_SALT = "SALTY_MC_SALTER";
	const REQUEST_CREATE = "CREATE_ROOM";
	const REQUEST_LOGIN = "LOGIN";
	const REQUEST_SETDATA = "PLS_CREATE_DATA";
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
			case Constants::REQUEST_LOGIN:
				if(!(array_key_exists(Constants::INDEX_KEY_ROOM,$_POST)
				&& array_key_exists(Constants::INDEX_KEY_ROOM_PASSWORD,$_POST))) {
					echo "...";
					die();
					break;
				}
				$stmt = $conn->prepare("SELECT USERNAME, PASSWORD, ISADMIN FROM USERS WHERE USERNAME = :name LIMIT 1"); #Select usernames, passwords, and account type
				$stmt->execute(array('name' => $_POST[Constants::INDEX_KEY_ROOM])); #based on the room
				$row = $stmt->fetch();
				if($row["ISADMIN"] === "1") { #If the account is an admin
					echo "This account is not tied to a room!"; #Deny it.
					die();
					break; #just in case.
				}
				if($row["PASSWORD"] !== $_POST[Constants::INDEX_KEY_ROOM_PASSWORD]) { #if the password hash is not the stored hash
					echo "The username or password is incorrect!"; #Deny it.
					die();
					break; #just in case.
				}
				#Account is not an admin and the passwords match!
				echo "OK";
				break;
			case Constants::REQUEST_CREATE:
				if(!(array_key_exists(Constants::INDEX_KEY_ROOM,$_POST)
				&& array_key_exists(Constants::INDEX_KEY_ROOM_PASSWORD,$_POST)
				&& array_key_exists(Constants::INDEX_KEY_ROOM_SALT,$_POST)
				&& array_key_exists(Constants::INDEX_KEY_ADMIN,$_POST)
				&& array_key_exists(Constants::INDEX_KEY_ADMIN_PASSWORD,$_POST))) {
					echo "...";
					die();
					break;
				}
				$stmt = $conn->prepare("SELECT USERNAME FROM USERS WHERE USERNAME = :name LIMIT 1"); #Select usernames
				$stmt->execute(array('name' => $_POST[Constants::INDEX_KEY_ROOM])); #based on the room
				$row = $stmt->fetch();
				if($row["USERNAME"] === $_POST[Constants::INDEX_KEY_ROOM]) { #if the room is in the database
					echo "This room name already exists!"; #Deny it.
					die();
					break; #just in case.
				}
				$stmt = $conn->prepare("SELECT USERNAME, PASSWORD, ISADMIN FROM USERS WHERE USERNAME = :name LIMIT 1"); #Select usernames and passwords and account type
				$stmt->execute(array('name' => $_POST[Constants::INDEX_KEY_ADMIN])); #based on the admin
				$row = $stmt->fetch();
				if($row["ISADMIN"] === "0") { #if the account is not an admin
					echo "This account does not have permission to do that."; #Deny it.
					die();
					break; #just in case.
				}
				if($row["PASSWORD"] !== $_POST[Constants::INDEX_KEY_ADMIN_PASSWORD]) { #if the password hash is not the stored hash
					echo "The username or password is incorrect!"; #Deny it.
					die();
					break; #just in case.
				}
				#Account does not exist, account is an admin, and passwords match!
				$stmt = $conn->prepare("INSERT INTO USERS (USERNAME,PASSWORD,SALT,ISADMIN) VALUES (:username, :password, :salt, '0')");
				$stmt->execute(array('username' => $_POST[Constants::INDEX_KEY_ROOM],
									 'password' => $_POST[Constants::INDEX_KEY_ROOM_PASSWORD],
									 'salt' => $_POST[Constants::INDEX_KEY_ROOM_SALT]));
				echo "OK" . "\n";
				echo "User account " . $_POST[Constants::INDEX_KEY_ROOM] . " created successfully";
				break;
			case Constants::REQUEST_SALT:
				if(!(array_key_exists(Constants::INDEX_KEY_USER,$_POST))) {
					echo "...";
					die();
					break;
				}
				$stmt = $conn->prepare("SELECT USERNAME, SALT FROM USERS WHERE USERNAME = :name LIMIT 1"); #Select usernames and salts
				$stmt->execute(array('name' => $_POST[Constants::INDEX_KEY_USER])); #based on the user
				$row = $stmt->fetch();
				if(!($row)) { #if the row does not exist
					echo "The username or password is incorrect!"; #Deny it.
					die();
					break; #just in case.
				}
				#Account exists!
				echo "OK" . "\n";
				echo $_POST[Constants::INDEX_KEY_USER] . "\n";
				echo $row['SALT'] . "\n";
				break;
			default:
				echo "...";
				die();
				break;
		}
	} else {
		echo "NO_KEY";
	}
} else { #Post is empty
	echo "READY";
}
?>
