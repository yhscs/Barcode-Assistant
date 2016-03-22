<?php
$yhs = array(
	"passing"=>"-5 minutes",
	"before"=>"7:25:00",
	"1"=>"8:17:00", #Periods should end at the beginning of the next period.
	"2"=>"9:09:00", #The end of the period minus the key "passing" is defined
	"3"=>"10:01:00",#as the passing period.
	"4"=>"10:35:00",
	"5"=>"11:03:00",
	"6"=>"11:31:00",
	"7"=>"11:59:00",
	"8"=>"12:27:00",
	"9"=>"12:56:00",
	"10"=>"13:48:00",
	"11"=>"14:35:00"); #Anything beyond this will be considered after school.
	
$yhsa = array(
	"passing"=>"-4 minutes",
	"before"=>"7:25:00",
	"1"=>"8:12:00",
	"2"=>"9:03:00",
	"3"=>"9:54:00",
	"4"=>"10:28:00",
	"5"=>"10:56:00",
	"6"=>"11:24:00",
	"7"=>"11:52:00",
	"8"=>"12:20:00",
	"9"=>"12:48:00",
	"10"=>"13:39:00",
	"11"=>"24:30:00");
	
$yms = array(
	"passing"=>"-3 minutes",
	"before"=>"7:30:00",
	"1"=>"8:15:00",
	"2"=>"9:00:00",
	"3"=>"9:45:00",
	"4"=>"10:30:00",
	"5/6/7"=>"12:18:00",
	"8"=>"13:03:00",
	"9"=>"13:48:00",
	"10"=>"24:30:00");

function getPeriodReal($school, $time) { #since PHP has such shitty naming conventions, I do too!
	$index = 0;
	if($time < date('H:i:s',strtotime($school["before"]))) {
		return "Before school";
	}
	foreach($school as $x => $x_value) {
		if($index++ < 2) {
			continue;
		}
		if($index === (count($school))) {
			if ($time < date('H:i:s',strtotime($school[$x]))) {
				return $x;
			} else {
				return "After school";
			}
		} else {
			if ($time < ($localPeriod = date('H:i:s',strtotime($x_value)))) {
				if($time < date('H:i:s',strtotime($school["passing"], strtotime($localPeriod)))) {
					return $x;
				} else {
					return "$x (Passing period)";
				}
			}
		}
	}
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
				#Check school hours first!
				date_default_timezone_set('America/Chicago');
				$date = date('Y-m-d H:i:s');
				$period = getPeriod(date('H:i:s'));
				if($period === "Before school" || $period === "After school") {
					echo "There is no need, it isn't during school hours!" . "\n"; #tell an error.
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
				
				$stmt = $conn->prepare("SELECT ID, ROOM, STUDENT_ID, TIME FROM LOG_INSIDE WHERE STUDENT_ID = :id AND ROOM = :name LIMIT 1"); #Select the index, room, student id, and last logged time from the people who are currently in that room.
				$stmt->execute(array('id' => $_POST[StudentData::ID],
									 'name' => $_POST[Index::ROOM])); #based on the id and the current selected room.
				$row = $stmt->fetch();
				$rowCount = $stmt->rowCount();
				
				
				if($rowCount == 1) {
					if(($thatMuchTime = strtotime($date) - strtotime($row["TIME"])) > 4*60*60){ #If the current student has been away for more than 4 hours then we'll consider them gone.
#						The code here was used to show when a student was signed out automatically. Turned out to be quite spammy. Has been removed.

#						$fourHoursFromThen = strtotime($row["TIME"]) + 4*60*60;
#						$realTime = date("Y-m-d H:i:s", $fourHoursFromThen);

#						$stmt = $conn->prepare("INSERT INTO LOG (ID, ROOM, CHECKIN, STUDENT_ID, STUDENT_NAME, STUDENT_GRADE, TIME, PERIOD, ) VALUES (NULL, :username, :checkin, :stud_id, :stud_name, :stud_grade, :stud_time, :period, :auto)");
#						$stmt->execute(array('username' => $_POST[Index::ROOM],
#										'checkin' => "0",
#										'stud_id' => $_POST[StudentData::ID],
#										'stud_name' => $student_name,
#										'stud_grade' => $student_grade,
#										'stud_time' => $realTime,
#										'period' => $period,
#										'auto' => "1")); #The calls here should ALWAYS be automatic.

#						Here would be the place to preform an action when the student has been gone for a while and tries to scan their id again.
						
						$index = $row["ID"];
						$stmt = $conn->prepare("DELETE FROM LOG_INSIDE WHERE ID = :index"); #Select the id of the students that is already signed in and delete it.
						$stmt->execute(array('index' => $index)); #based on the index.

						$rowCount = 0; #Removed, preform check as normal.
					}
				}
				
				$checkin = 0;
				if($rowCount == 0) {
					$checkin = 1;
					$stmt = $conn->prepare("INSERT INTO LOG_INSIDE (ID, ROOM, STUDENT_ID,STUDENT_NAME,STUDENT_GRADE, TIME, PERIOD) VALUES (NULL, :username, :stud_id, :stud_name, :stud_grade, :stud_time, :period)");
					$stmt->execute(array('username' => $_POST[Index::ROOM],
										'stud_id' => $_POST[StudentData::ID],
										'stud_name' => $student_name,
										'stud_grade' => $student_grade,
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
				
				$stmt = $conn->prepare("INSERT INTO LOG (ID, ROOM, CHECKIN, STUDENT_ID, STUDENT_NAME, STUDENT_GRADE, TIME, PERIOD) VALUES (NULL, :username, :checkin, :stud_id, :stud_name, :stud_grade, :stud_time, :period)");
				$stmt->execute(array('username' => $_POST[Index::ROOM],
									 'checkin' => $checkin,
									 'stud_id' => $_POST[StudentData::ID],
									 'stud_name' => $student_name,
									 'stud_grade' => $student_grade,
									 'stud_time' => $date,
									 'period' => $period));
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
