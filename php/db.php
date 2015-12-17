<?php
class Index {
	const REQUEST="REQUEST";
	const ROOM="ROOM";
	const ROOM_PASSWORD="ROOM_PASSWORD";
	const ROOM_SALT="ROOM_SALT";
	const ADMIN="ADMIN";
	const ADMIN_PASSWORD="ADMINISTRATOR_PASSWORD";
	const USER="USER";
}

class StudentData {
	const ID="STUDID";
	const CHECK_TIME="STUDTIME";
	const AUTOMATIC="STUDAUTO_LOGOUT";
}

class Request {
	const SALT = "SALTY_MC_SALTER";
	const CREATE = "CREATE_ROOM";
	const LOGIN = "LOGIN";
	const SETDATA = "PLS_CREATE_DATA";
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
    if(array_key_exists(Index::REQUEST,$_POST)) {
		switch($_POST[Index::REQUEST]) {
			case Constants::REQUEST_SETDATA:
				if(!(array_key_exists(Index::ROOM,$_POST)
				  && array_key_exists(Index::ROOM_PASSWORD,$_POST)
				  && array_key_exists(StudentData::STUDENT_ID,$_POST)
				  && array_key_exists(StudentData::CHECK_TIME,$_POST)
				  && array_key_exists(StudentData::AUTOMATIC,$_POST))) {
					echo "...";
					die();
				}
				echo "NOT_READY_SRS";
				break;
			
			case Constants::REQUEST_LOGIN:
				if(!(array_key_exists(Index::ROOM,$_POST)
				&& array_key_exists(Index::ROOM_PASSWORD,$_POST))) {
					echo "...";
					die();
				}
				$stmt = $conn->prepare("SELECT USERNAME, PASSWORD, ISADMIN FROM USERS WHERE USERNAME = :name LIMIT 1"); #Select usernames, passwords, and account type
				$stmt->execute(array('name' => $_POST[Index::ROOM])); #based on the room
				$row = $stmt->fetch();
				if($row["ISADMIN"] === "1") { #If the account is an admin
					echo "This account is not tied to a room!"; #Deny it.
					die();
				}
				if($row["PASSWORD"] !== $_POST[Index::ROOM_PASSWORD]) { #if the password hash is not the stored hash
					echo "The username or password is incorrect!"; #Deny it.
					die();
				}
				#Account is not an admin and the passwords match!
				echo "OK";
				break;
			
			case Constants::REQUEST_CREATE:
				if(!(array_key_exists(Index::ROOM,$_POST)
				  && array_key_exists(Index::ROOM_PASSWORD,$_POST)
				  && array_key_exists(Index::ROOM_SALT,$_POST)
				  && array_key_exists(Index::ADMIN,$_POST)
				  && array_key_exists(Index::ADMIN_PASSWORD,$_POST))) {
					echo "...";
					die();
				}
				$stmt = $conn->prepare("SELECT USERNAME FROM USERS WHERE USERNAME = :name LIMIT 1"); #Select usernames
				$stmt->execute(array('name' => $_POST[Index::ROOM])); #based on the room
				$row = $stmt->fetch();
				if($row["USERNAME"] === $_POST[Index::ROOM]) { #if the room is in the database
					echo "This room name already exists!"; #Deny it.
					die();
				}
				$stmt = $conn->prepare("SELECT USERNAME, PASSWORD, ISADMIN FROM USERS WHERE USERNAME = :name LIMIT 1"); #Select usernames and passwords and account type
				$stmt->execute(array('name' => $_POST[Index::ADMIN])); #based on the admin
				$row = $stmt->fetch();
				if($row["ISADMIN"] === "0") { #if the account is not an admin
					echo "This account does not have permission to do that."; #Deny it.
					die();
				}
				if($row["PASSWORD"] !== $_POST[Index::ADMIN_PASSWORD]) { #if the password hash is not the stored hash
					echo "The username or password is incorrect!"; #Deny it.
					die();
				}
				#Account does not exist, account is an admin, and passwords match!
				$stmt = $conn->prepare("INSERT INTO USERS (USERNAME,PASSWORD,SALT,ISADMIN) VALUES (:username, :password, :salt, '0')");
				$stmt->execute(array('username' => $_POST[Index::ROOM],
									 'password' => $_POST[Index::ROOM_PASSWORD],
									 'salt' => $_POST[Index::ROOM_SALT]));
				echo "OK" . "\n";
				echo "User account " . $_POST[Index::ROOM] . " created successfully";
				break;
			
			case Constants::REQUEST_SALT:
				if(!(array_key_exists(Index::USER,$_POST))) {
					echo "...";
					die();
				}
				$stmt = $conn->prepare("SELECT USERNAME, SALT FROM USERS WHERE USERNAME = :name LIMIT 1"); #Select usernames and salts
				$stmt->execute(array('name' => $_POST[Index::USER])); #based on the user
				$row = $stmt->fetch();
				if(!($row)) { #if the row does not exist
					echo "The username or password is incorrect!"; #Deny it.
					die();
				}
				#Account exists!
				echo "OK" . "\n";
				echo $_POST[Index::USER] . "\n";
				echo $row['SALT'] . "\n";
				break;
			default:
				echo "...";
				die();
		}
	} else {
		echo "NO_KEY";
	}
} else { #Post is empty
	echo "READY";
}
?>
