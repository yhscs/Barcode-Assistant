<?php

function autoLogout() {
	//TODO: AutoLogout.
}
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
	const IS_CHECKIN="IS_CHECKIN";
	const PERIOD="PERIOD";
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
			case Request::SETDATA:
				if(!(array_key_exists(Index::ROOM,$_POST)
				  && array_key_exists(Index::ROOM_PASSWORD,$_POST)
				  && array_key_exists(StudentData::ID,$_POST)
				  && array_key_exists(StudentData::CHECK_TIME,$_POST)
				  && array_key_exists(StudentData::PERIOD,$_POST))) {
					echo "...\n";
					die();
				}
				$stmt = $conn->prepare("SELECT USERNAME, PASSWORD, ISADMIN FROM USERS WHERE USERNAME = :name LIMIT 1"); #Select usernames, passwords, and account
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
				
				//If we are "in" try and run the auto logout feature.
				autoLogout();
				
				$stmt = $conn->prepare("SELECT ID, ROOM, STUDENT_ID, TIME FROM LOG_INSIDE WHERE STUDENT_ID = :id AND ROOM = :name LIMIT 1"); #Select the index, room, student id, and last logged time from the people who are currently in that room.
				$stmt->execute(array('id' => $_POST[StudentData::ID],
									 'name' => $_POST[Index::ROOM])); #based on the id and the current selected room.
				$row = $stmt->fetch();
				
				$checkin = 0;
				if($stmt->rowCount() == 0) {
					$checkin = 1;
					$stmt = $conn->prepare("INSERT INTO LOG_INSIDE (ID, ROOM, STUDENT_ID, TIME, PERIOD) VALUES (NULL, :username, :stud_id, :stud_time, :period)");
					$stmt->execute(array('username' => $_POST[Index::ROOM],
										'stud_id' => $_POST[StudentData::ID],
										'stud_time' => $_POST[StudentData::CHECK_TIME],
										'period' => $_POST[StudentData::PERIOD]));
				} else {
					if(($thatMuchTime = strtotime($_POST[StudentData::CHECK_TIME]) - strtotime($row["TIME"])) < 60) {
						$thisMuchTime = 60 - $thatMuchTime;
						echo "Sorry, but to prevent spam you need to wait at least $thisMuchTime seconds before you can sign out again!";
						die();
					}
					$index = $row["ID"];
					$stmt = $conn->prepare("DELETE FROM LOG_INSIDE WHERE ID = :index"); #Select the id of the students that is already signed in and delete it.
					$stmt->execute(array('index' => $index)); #based on the index.
				}
				
				$stmt = $conn->prepare("SELECT STUDENT_GRADE, STUDENT_NAME, STUDENT_ID FROM STUDENT$ WHERE STUDENT_ID = :id LIMIT 1"); #Select the student name, grade, and id from the students table.
				$stmt->execute(array('id' => $_POST[StudentData::ID])); #based on the id (that's all the info we have, we need the other pieces).
				$row = $stmt->fetch();
				$student_grade = "N/A";
				$student_name = "N/A";
				if($stmt->rowCount() == 0) { #if the row does not exist
					echo "The Student ID you provided could not be found." . "\n"; #tell an error.
					die();
				} else {
					$student_grade = $row["STUDENT_GRADE"]; #we need to set these variables for later use
					$student_name = $row["STUDENT_NAME"]; #because we will insert them into the logs.
				}
				
				$stmt = $conn->prepare("INSERT INTO LOG (ID, ROOM, CHECKIN, STUDENT_ID, STUDENT_NAME, STUDENT_GRADE, TIME, PERIOD, AUTO) VALUES (NULL, :username, :checkin, :stud_id, :stud_name, :stud_grade, :stud_time, :period, :auto)");
				$stmt->execute(array('username' => $_POST[Index::ROOM],
									 'checkin' => $checkin,
									 'stud_id' => $_POST[StudentData::ID],
									 'stud_name' => $student_name,
									 'stud_grade' => $student_grade,
									 'stud_time' => $_POST[StudentData::CHECK_TIME],
									 'period' => $_POST[StudentData::PERIOD],
									 'auto' => "0")); #The calls here should never be automatic.
				if ($checkin === 0) {
					echo "CHECKOUT" . "\n";
				} else {
					echo "CHECKIN" . "\n";
				}
				break;
			
			case Request::LOGIN:
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
			
			case Request::CREATE:
				if(!(array_key_exists(Index::ROOM,$_POST)
				  && array_key_exists(Index::ROOM_PASSWORD,$_POST)
				  && array_key_exists(Index::ROOM_SALT,$_POST)
				  && array_key_exists(Index::ADMIN,$_POST)
				  && array_key_exists(Index::ADMIN_PASSWORD,$_POST))) {
					echo "...";
					die();
				}
				$userLowercaseCheck = strtolower($_POST[Index::ROOM]);
				$stmt = $conn->prepare("SELECT USERNAME FROM USERS WHERE USERNAME = :name LIMIT 1"); #Select usernames
				$stmt->execute(array('name' => $_POST[Index::ROOM])); #based on the room
				$row = $stmt->fetch();
				if(strtolower($row["USERNAME"]) === $userLowercaseCheck) { #if the room is in the database (both check as a lowercase string)
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
			
			case Request::SALT:
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
