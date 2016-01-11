<?php

function autoLogout() {
	//TODO: AutoLogout.
}
function getPeriod($time) {
	if($time < date('H:i:s',strtotime("7:25:00"))) {
		return "Before school";
	} else if ($time < ($localPeriod = date('H:i:s',strtotime("8:17:00")))) {
		if($time < date('H:i:s',strtotime("-5 minutes", strtotime($localPeriod)))) {
			return "1";
		} else {
			return "1 (Passing period)";
		}
	} else if ($time < ($localPeriod = date('H:i:s',strtotime("9:09:00")))) {
		if($time < date('H:i:s',strtotime("-5 minutes", strtotime($localPeriod)))) {
			return "2";
		} else {
			return "2 (Passing period)";
		}
	} else if ($time < ($localPeriod = date('H:i:s',strtotime("10:01:00")))) {
		if($time < date('H:i:s',strtotime("-5 minutes", strtotime($localPeriod)))) {
			return "3";
		} else {
			return "3 (Passing period)";
		}
	} else if ($time < ($localPeriod = date('H:i:s',strtotime("10:35:00")))) {
		if($time < date('H:i:s',strtotime("-5 minutes", strtotime($localPeriod)))) {
			return "4";
		} else {
			return "4 (Passing period)";
		}
	} else if ($time < ($localPeriod = date('H:i:s',strtotime("11:03:00")))) {
		if($time < date('H:i:s',strtotime("-5 minutes", strtotime($localPeriod)))) {
			return "5";
		} else {
			return "5 (Passing period)";
		}
	} else if ($time < ($localPeriod = date('H:i:s',strtotime("11:31:00")))) {
		if($time < date('H:i:s',strtotime("-5 minutes", strtotime($localPeriod)))) {
			return "6";
		} else {
			return "6 (Passing period)";
		}
	} else if ($time < ($localPeriod = date('H:i:s',strtotime("11:59:00")))) {
		if($time < date('H:i:s',strtotime("-5 minutes", strtotime($localPeriod)))) {
			return "7";
		} else {
			return "7 (Passing period)";
		}
	} else if ($time < ($localPeriod = date('H:i:s',strtotime("12:27:00")))) {
		if($time < date('H:i:s',strtotime("-5 minutes", strtotime($localPeriod)))) {
			return "8";
		} else {
			return "8 (Passing period)";
		}
	} else if ($time < ($localPeriod = date('H:i:s',strtotime("12:56:00")))) {
		if($time < date('H:i:s',strtotime("-5 minutes", strtotime($localPeriod)))) {
			return "9";
		} else {
			return "9 (Passing period)";
		}
	} else if ($time < ($localPeriod = date('H:i:s',strtotime("13:48:00")))) {
		if($time < date('H:i:s',strtotime("-5 minutes", strtotime($localPeriod)))) {
			return "10";
		} else {
			return "10 (Passing period)";
		}
	} else if ($time < ($localPeriod = date('H:i:s',strtotime("14:35:00")))) {
		return "11";
	} else {
		return "After school";
	}
}

include '/home/aj4057/indexkeys.php'; //Index keys that are used. For example, Index::REQUEST is defined here.
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
				  && array_key_exists(StudentData::ID,$_POST))) {
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
				
				date_default_timezone_set('America/Chicago');
				$date = date('Y-m-d H:i:s');
				$period = getPeriod(date('H:i:s'));
				
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
										'stud_time' => $date,
										'period' => $period));
				} else {
					if(($thatMuchTime = strtotime($date) - strtotime($row["TIME"])) < 15) {
						$thisMuchTimeIn = 15 - $thatMuchTime;
						echo "Sorry, but to prevent spam you need to wait $thisMuchTimeIn seconds. Let some other students sign out while you wait.";
						die();
					}
					$index = $row["ID"];
					$stmt = $conn->prepare("DELETE FROM LOG_INSIDE WHERE ID = :index"); #Select the id of the students that is already signed in and delete it.
					$stmt->execute(array('index' => $index)); #based on the index.
				}
				
				$stmt = $conn->prepare("INSERT INTO LOG (ID, ROOM, CHECKIN, STUDENT_ID, STUDENT_NAME, STUDENT_GRADE, TIME, PERIOD, AUTO) VALUES (NULL, :username, :checkin, :stud_id, :stud_name, :stud_grade, :stud_time, :period, :auto)");
				$stmt->execute(array('username' => $_POST[Index::ROOM],
									 'checkin' => $checkin,
									 'stud_id' => $_POST[StudentData::ID],
									 'stud_name' => $student_name,
									 'stud_grade' => $student_grade,
									 'stud_time' => $date,
									 'period' => $period,
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