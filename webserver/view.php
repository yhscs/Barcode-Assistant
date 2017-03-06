<?php
#INCLUDE AND START SESSION
include '../config.php'; #Define $servername $username $password $dbname and $configready here.
session_start();
if(!isset($_SESSION['login_user']) || !isset($_SESSION['timestamp']) || !isset($_SESSION['valid'])) { 
	header("location: logout.php");
	die();
}

if(strtotime(date("Y-m-d H:i:s")) - strtotime($_SESSION['timestamp']) > 10*60 ){
	$_SESSION['valid'] = "false"; //Makes sure the session is killed.
	header("location: logout.php");
	die();
}

if($_SESSION['valid'] !== "true") {
	header("location: logout.php");
	die();
}

#Do while loop allows me to terminate the task at hand.
do {
#Connect to database
try {
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	$error = "Could not connect to the database. This should never happen.";
	break;
}

#On page load we are going to remove all of the >4hour people in a room first. Then nothing... weird will happen.
$stmt = $conn->prepare("SELECT * FROM LOG_INSIDE WHERE ROOM = :name"); #Select everything from the people who are currently in that room.
$stmt->execute(array('name' => $_SESSION['login_user'])); #based on the current selected room.
$data = $stmt->fetchAll();

foreach($data as $row) {		
	if(($thatMuchTime = strtotime(date("Y-m-d H:i:s")) - strtotime($row["TIME"])) > 4*60*60){ #If the current student has been away for more than 4 hours then we'll consider them gone.
#		The code here was used to show when a student was signed out automatically. Turned out to be quite spammy. Has been removed.
	
#		$fourHoursFromThen = strtotime($row["TIME"]) + 4*60*60;
#		$realTime = date("Y-m-d H:i:s", $fourHoursFromThen);
		
#		$stmt = $conn->prepare("INSERT INTO LOG (ID, ROOM, CHECKIN, STUDENT_ID, STUDENT_NAME, STUDENT_GRADE, TIME, PERIOD, AUTO) VALUES (NULL, :username, :checkin, :stud_id, :stud_name, :stud_grade, :stud_time, :period, :auto)");
#		$stmt->execute(array('username' => $_SESSION['login_user'],
#						'checkin' => "0",
#						'stud_id' => $row['STUDENT_ID'],
#						'stud_name' => $row['STUDENT_NAME'],
#						'stud_grade' => $row['STUDENT_GRADE'],
#						'stud_time' => $realTime,
#						'period' => $row['PERIOD'],
#						'auto' => "1")); #The calls here should ALWAYS be automatic.
						
		$index = $row["ID"];
		$stmt = $conn->prepare("DELETE FROM LOG_INSIDE WHERE ID = :index"); #Select the id of the students that is already signed in and delete it.
		$stmt->execute(array('index' => $index)); #based on the index.
	}
}

#Messages for various filters
$filterMessages = "ACTIVE FILTERS:<br>";

#Where var for filters.
$queryWhereVars = "";

#DESC IF NOT old-new
$queryUpDown = "DESC";
if (isset($_GET['sort'])) {
	if($_GET['sort'] === "old-new") {
		$queryUpDown = "ASC";  #Use fixed string so no SQL injection gets through!
		$filterMessages = $filterMessages . "Putting old logs on the top.<br>";
	}
}

#For the "name" field. Notice AND LOCATE so it uses "substring" instead of LIKE.
$nameIsSet=false;
if (isset($_GET['name'])) {
	if(!($_GET['name'] === "" || $_GET['name'] == null)) {
		$nameIsSet=true;
		$queryWhereVars = $queryWhereVars . " AND LOCATE( :name , STUDENT_NAME ) > 0";
	}
} 

#For the grade field.
$gradeIsSet=false;
if (isset($_GET['grade'])) {
	if(!($_GET['grade'] === "" || $_GET['grade'] == null)) {
		$gradeIsSet=true;
		$queryWhereVars = $queryWhereVars . " AND STUDENT_GRADE = :grade";
	}
} 

#For the student ID.
$idIsSet=false;
if (isset($_GET['student_id'])) {
	if(!($_GET['student_id'] === "" || $_GET['student_id'] == null)) {
		$idIsSet=true;
		$queryWhereVars = $queryWhereVars . " AND STUDENT_ID = :student_id";
	}
}

#For the period.
$periodIsSet=false;
if (isset($_GET['period'])) {
	if(!($_GET['period'] === "" || $_GET['period'] == null)) {
		$periodIsSet=true;
		$queryWhereVars = $queryWhereVars . " AND (PERIOD = :period OR PERIOD = :periodwithpassing)";
	}
} 

#For the time.
#OK, so time is complicated. Here is the run-down.

#First, check to see if the start_date is set. If it is, make $startDateIsSet true.
#If the user typed something in the box (not empty string) then set $startDateType to "SET".
$startDateIsSet=false;
$startDateType="";
if (isset($_GET['start_date'])) {
	if(!($_GET['start_date'] === "" || $_GET['start_date'] == null)) {
		$startDateIsSet=true; //"SET";
		$startDateType="SET";
	}
	if($_GET['start_date'] === "") {
		$startDateIsSet=true; //"BEGINNING";
	}
} 

#The same thing happens for the end date.
$endDateIsSet=false;
$endDateType="";
if (isset($_GET['end_date'])) {
	if(!($_GET['end_date'] === "" || $_GET['end_date'] == null)) {
		$endDateIsSet=true; //"SET";
		$endDateType="SET";
	}
	if($_GET['end_date'] === "") {
		$endDateIsSet=true; //"NOW";
	}
} 

#Now we need to parse the string. If both start_date and end_date contain something, continue
if($startDateIsSet && $endDateIsSet) {
	#BUT! They can't both be empty at the same time (see else)
	if(!($startDateType === "" && $endDateType === "")) {
		
		if($startDateType === "") {
			#If startDate is empty, use "the origin time" (basically the beginning of time for computers)
			$startDate = date('Y-m-d',strtotime("1970-01-01"));
		} else {
			#Otherwise, use our own time
			$startDate = date('Y-m-d',$success = strtotime($_GET['start_date']));
			if($success === false) {
				$startDateIsSet = false;
			}
		}
		
		if($endDateType === "") {
			#Do the same for the end date
			$endDate = date('Y-m-d');
		} else {
			$endDate = date('Y-m-d',$success = strtotime($_GET['end_date']));
			if($success === false){
				$endDateIsSet = false;
			}
		}
	} else {
		#If they are both empty, we might as well be selecting everything. So we omit this field.
		$startDateIsSet = false; 
		$endDateIsSet = false;
	}
}

#This is the master boolean for times. All the above vars should not be used later. Except for $startDate and $endDate
$timeRange = false; 
if($startDateIsSet && $endDateIsSet) {
	#If a value failed to parse (user entered) then this will fail to run!
	$queryWhereVars = $queryWhereVars . " AND cast(TIME as date) BETWEEN :startdate AND :enddate";
	$timeRange = true;
}
#And that's all for time.

#From where are we selecting these things? If the current view is set, use LOG_INSIDE instead.
$fromWhere = "LOG";
if (isset($_GET['view'])) {
	if($_GET['view'] === "current") {
		$fromWhere = "LOG_INSIDE";
		$filterMessages = $filterMessages . "Viewing current students only.<br>";
	}
}

#How many results per page
$per_page = 25;

#Get the total amount of log
$stmt = $conn->prepare("SELECT count(*) FROM " . $fromWhere . " WHERE ROOM = :where" . $queryWhereVars);
$stmt->bindParam(":where", $_SESSION['login_user'], PDO::PARAM_STR);

#We need to bind all of the "$wheryWhereVars"
if($nameIsSet === true) {
	$stmt->bindParam(":name", $_GET['name'], PDO::PARAM_STR);
}
if($gradeIsSet === true) {
	$stmt->bindParam(":grade", $_GET['grade'], PDO::PARAM_STR);
}
if($idIsSet === true) {
	$stmt->bindParam(":student_id", $_GET['student_id'], PDO::PARAM_STR);
}
if($periodIsSet === true) {
	$stmt->bindParam(":period", $_GET['period'], PDO::PARAM_STR);
	$passing = trim($_GET['period']) . " (Passing Period)";
	$stmt->bindParam(":periodwithpassing", $passing, PDO::PARAM_STR);
}
if($timeRange === true) {
	$stmt->bindParam(":startdate", $startDate, PDO::PARAM_STR);
	$stmt->bindParam(":enddate", $endDate, PDO::PARAM_STR);
}

#When that's done, we can execute the query.
$stmt->execute();
$total_rows = $stmt->fetch(); #We have the total amount of posts
$num_pages=ceil((int)$total_rows[0]/$per_page); #Maximum page number

#Never trust the user. (Fix page if wrong)
if (isset($_GET['page'])) {
	$CUR_PAGE = intval($_GET['page']);
} else {
	$CUR_PAGE=1;
}
if ($CUR_PAGE > $num_pages || $CUR_PAGE <= 0) {
	$CUR_PAGE = 1;
}

#Now figure out where to start
$start = abs(($CUR_PAGE-1)*$per_page); 

#Now let's form new query string without page variable
$uri = strtok($_SERVER['REQUEST_URI'],"?")."?";    
$tmpget = $_GET;
unset($tmpget['page']);
if ($tmpget) {
  $uri .= http_build_query($tmpget)."&";
}

#now we're getting total pages number and fill an array of links
for($i=1;$i<=$num_pages;$i++) {
	$PAGES[$i]=$uri.'page='.$i;
}

#This little if statement breaks up the page count if there are more than 10 pages to display.
if(count($PAGES) > 9) {
	$leftElipse = true;
	$rightElipse = true;
	$OLDPAGES = $PAGES;
	unset($PAGES);
	$PAGES[1] = $OLDPAGES[1];
	if($CUR_PAGE > 5 && $CUR_PAGE < $num_pages - 4){
		for($i = $CUR_PAGE - 3; $i <= 3 + $CUR_PAGE; $i++) {
			$PAGES[$i] = $OLDPAGES[$i];
		}
	} else if( $CUR_PAGE <= 5 ) {
		$leftElipse = false;
		for($i = 2; $i <= 8; $i++) {
			$PAGES[$i] = $OLDPAGES[$i];
		}
	} else if( $CUR_PAGE > $num_pages - 5) {
		$rightElipse = false;
		for($i = $num_pages - 7; $i < $num_pages; $i++) {
			$PAGES[$i] = $OLDPAGES[$i];
		}
	}
	$PAGES[$num_pages] = $OLDPAGES[$num_pages];
}

#Run our query for real this time.
$stmt = $conn->prepare("SELECT * FROM " . $fromWhere . " WHERE ROOM = :where " . $queryWhereVars .  " ORDER BY ID " . $queryUpDown . " LIMIT :starting,:postsperpage"); #select the actual data
$stmt->bindParam(":where", $_SESSION['login_user'], PDO::PARAM_STR);

#Same as above query for counting things. This time, however, do it for real.
if($nameIsSet === true) {
	$stmt->bindParam(":name", $_GET['name'], PDO::PARAM_STR);
	$filterMessages = $filterMessages . "Name: \"" . htmlspecialchars($_GET['name']) . "\"<br>";
}
if($gradeIsSet === true) {
	$stmt->bindParam(":grade", $_GET['grade'], PDO::PARAM_STR);
	$filterMessages = $filterMessages . "Grade: \"" . htmlspecialchars($_GET['grade']) . "\"<br>";
}
if($idIsSet === true) {
	$stmt->bindParam(":student_id", $_GET['student_id'], PDO::PARAM_STR);
	$filterMessages = $filterMessages . "Student ID: \"" . htmlspecialchars($_GET['student_id']) . "\"<br>";
}
if($periodIsSet === true) {
	$stmt->bindParam(":period", $_GET['period'], PDO::PARAM_STR);
	$passing = trim($_GET['period']) . " (Passing Period)";
	$stmt->bindParam(":periodwithpassing", $passing, PDO::PARAM_STR);
	$filterMessages = $filterMessages . "Period: \"" . htmlspecialchars($_GET['period']) . "\" (Includes passing period)<br>";
}
if($timeRange === true) {
	$stmt->bindParam(":startdate", $startDate, PDO::PARAM_STR);
	$stmt->bindParam(":enddate", $endDate, PDO::PARAM_STR);
	$filterMessages = $filterMessages . "Time:<br> * Start date: \"" . htmlspecialchars($startDate) . "\"<br> * End date: \"" . htmlspecialchars($endDate) . "\"<br>";
}
$stmt->bindParam(":starting", $start, PDO::PARAM_INT);
$stmt->bindParam(":postsperpage", $per_page, PDO::PARAM_INT);

#Execute all of that.
$stmt->execute();

#and put it in an array
$result = $stmt->fetchAll();

#And that is all of the header PHP. We can finally exit to HTML.
?><!DOCTYPE html>
<html>
<head>
	<title>Viewer</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="/css/style.css">
	<script src="/script/jquery.min.js"></script>
	<script src="/script/form.js"></script>
</head>
<body>
<div id="outside">

<?php if($fromWhere === "LOG") { ?>
<h1>Attendance Viewer for: <?php echo $_SESSION['login_user'];?></h1>
<?php } else { ?>
<h1>Current students for: <?php echo $_SESSION['login_user'];?></h1>
<?php } ?>

<div id="scroller">
<table id="main">
<?php if($fromWhere === "LOG") { ?>
	<tr>
		<th>ID:</th>
		<th>Arrival or Departure:</th>		
		<th>Student ID:</th>
		<th>Student Name:</th>
		<th>Student Grade:</th>
		<th>Time of Action:</th>
		<th>Period:</th>
	</tr>
<?php } else { ?>
	<tr>
		<th>ID:</th>
		<th>Student ID:</th>
		<th>Student Name:</th>
		<th>Student Grade:</th>
		<th>Time of Action:</th>
		<th>Period:</th>
	</tr>
<?php }
foreach ($result as $row) {	#Yes, this looks funky. But it makes the output look clean and helps debugging.?>
	<?php echo "<tr>"; ?>
	
		<?php echo "<td>" . $row["ID"] . "</td>"; ?>
		
		<?php if($fromWhere === "LOG") {echo "<td>" . ($row["CHECKIN"] === "1" ? "Arrival" : "Departure") . "</td>";} ?>
		
		<?php echo "<td>" . $row["STUDENT_ID"] . "</td>"; ?>
		
		<?php echo "<td>" . $row["STUDENT_NAME"] . "</td>"; ?>
		
		<?php echo "<td>" . $row["STUDENT_GRADE"] . "</td>"; ?>
		
		<?php echo "<td>" . $row["TIME"] . "</td>";	?>
		
		<?php echo "<td>" . $row["PERIOD"] . "</td>"; ?>
		
	<?php echo "</tr>"; ?>
	
<?php } ?>
</table>
</div>
<div class="info">
<?php 
if($num_pages != 0) { ?>
	<div class="pages">
	<h3 class="center">Page</h3>
	
<?php 
	$counter = 1;
	foreach ($PAGES as $i => $link){
		if(($leftElipse === true && $counter === 2) || 
		   ($rightElipse === true && $counter === 9)) { ?>
		<b> ... </b>
		
<?php 	}
		if ($i == $CUR_PAGE){ ?>
		<b> <?php echo $i;?> </b>
		
<?php 	} else { ?>
		<a href="<?php echo htmlspecialchars($link);?> "><?php echo $i; ?></a>
		
<?php	}
		$counter++;
	} ?>
	</div>
<?php
} else { ?>
	<span>No results</span>
<?php
}

if(!($filterMessages === "ACTIVE FILTERS:<br>")) { ?>
<span>
<?php echo $filterMessages; ?>
</span>
<?php } ?>
</div>

<h4 class="center"><?php echo "<br>Total results: " . (int)$total_rows[0] . ".<br><br>";?></h4>

<?php } while (0); #If there is a break, the code will jump to here automatically. ?>
<div id="options">
	<form id="filter">
		<h3 class="titlepadding">Student ID:</h3>
		<input id="student_id" 	class="text" 		type="text" 		name="student_id" 	placeholder="Seven digit Student ID"><br>

		<h3 class="titlepadding">Student name:</h3>
		<input id="name"		class="text" 		type="text" 		name="name" 		placeholder="Part of name or whole name"><br>
		
		<h3 class="titlepadding">Student grade:</h3>
		<input id="grade" 		class="text" 		type="number" 		name="grade" 		placeholder="Any value 9-12" min="9" max="12"><br>
				
		<h3 class="titlepadding">Period:</h3>
		<input id="period" 		class="text" 		type="number" 		name="period" 		placeholder="Any value 1-11" min="1" max="11"><br>
		
		<h3 class="titlepadding">Start date (Leave blank to view from the beginning):</h3>
		<input id="start_date" 	class="text"		type="date" 		name="start_date" 	placeholder="Date: YYYY-MM-DD"><br>
		
		<h3 class="titlepadding">End date (Leave blank to view up until now):</h3>
		<input id="end_date" 	class="text"		type="date" 		name="end_date" 	placeholder="Date: YYYY-MM-DD"><br>
		
		<h3 class="titlepadding">Extra options:</h3>
		<label><input id="current" 				type="checkbox"		name="view" 	value="current"> View only current students</label><br> 
		<label><input id="sort_old"				type="checkbox"		name="sort" 	value="old-new"> Old on top</label>
		
		<span><?php echo $sortError;?></span>
		
		<div class="padding"><input type="submit" value=" Filter! "></div>
	</form>
</div>
<div id="logout">
	<form action="/logout.php" method="get" style="padding: 0">
		<input type="submit" value=" Logout ">
	</form>
</div>
<h3 class="center"><?php echo "For security purposes, you have " . gmdate("i:s" , (10*60) - (strtotime(date("Y-m-d H:i:s")) - strtotime($_SESSION['timestamp']))) . " minutes left in your session."; ?></h3><br>
<h3 class="center">Update:</h3>
<p class="center">We are back online!</p><br>
</div>
</body>
</html>
