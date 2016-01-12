<?php
#INCLUDE AND START SESSION
include '/home/aj4057/config.php'; #Define $servername $username $password $dbname and $configready here.
session_start();
if(!isset($_SESSION['login_user'])){
	header("location: index.php");
	die();
}

#Do while loop allows me to terminate the task at hand.
do {
#CONNECT TO DATABASE
try {
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	$error = "Could not connect to the database. This should never happen.";
	break;
}

#WHERE VAR FOR FILTERS
$queryWhereVars = "";

#DESC IF NOT old-new
$queryUpDown = "DESC";
if (isset($_GET['sort'])) {
	if($_GET['sort'] === "old-new") {
		$queryUpDown = "ASC";
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

#How many results per page
$per_page = 20;

#Get the total amount of log
$stmt = $conn->prepare("SELECT count(*) FROM LOG WHERE ROOM = :where" . $queryWhereVars);
$stmt->bindParam(":where", $_SESSION['login_user'], PDO::PARAM_STR);
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
$start = abs(($CUR_PAGE-1)*$per_page); #Now figure out where to start

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
$stmt = $conn->prepare("SELECT * FROM LOG WHERE ROOM = :where " . $queryWhereVars .  " ORDER BY ID " . $queryUpDown . " LIMIT :starting,:postsperpage"); #select the actual data
$stmt->bindParam(":where", $_SESSION['login_user'], PDO::PARAM_STR);
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
$stmt->bindParam(":starting", $start, PDO::PARAM_INT);
$stmt->bindParam(":postsperpage", $per_page, PDO::PARAM_INT);
$stmt->execute();

#and put it in an array
$result = $stmt->fetchAll();
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
<h1>Attendance Viewer for: <?php echo $_SESSION['login_user'];?></h1>
<div id="scroller">
<table id="main">
	<tr>
		<th>ID:</th>
		<th>Arrival or Departure:</th>		
		<th>Student ID:</th>
		<th>Student Name:</th>
		<th>Student Grade:</th>
		<th>Time of Action:</th>
		<th>Period:</th>
		<th>Automatic Sign Out:</th>
	</tr>
<?php foreach ($result as $row) {	?>
	<?php echo "<tr>";	?>
	
		<?php echo "<td>" . $row["ID"] . "</td>";	?>
		
		<?php echo "<td>" . ($row["CHECKIN"] === "1" ? "Arrival" : "Departure") . "</td>";	?>
		
		<?php echo "<td>" . $row["STUDENT_ID"] . "</td>"	;	?>
		
		<?php echo "<td>" . $row["STUDENT_NAME"] . "</td>";		?>
		
		<?php echo "<td>" . $row["STUDENT_GRADE"] . "</td>";	?>
		
		<?php echo "<td>" . $row["TIME"] . "</td>";		?>
		
		<?php echo "<td>" . $row["PERIOD"] . "</td>";	?>
		
		<?php echo "<td>" . ($row["AUTO"] === "1" ? "Yes" : "No") . "</td>";	?>
		
	<?php echo "</tr>";	?>
	
<?php } 	?>
</table>
</div>
<div class="info">
<?php 
if($num_pages != 0) { 											?>
	<div class="pages">
	Page<br>
	
<?php 
	$counter = 1;
	foreach ($PAGES as $i => $link){
		if(($leftElipse === true && $counter === 2) || 
		   ($rightElipse === true && $counter === 9)) { 		?>
		<b> ... </b>
		
<?php 	}
		if ($i == $CUR_PAGE){ 									?>
		<b> <?php echo $i;?> </b>
		
<?php 	} else { 												?>
		<a href=" <?php echo $link;?> "> <?php echo $i; ?></a>
		
<?php	}
		$counter++;
	}															?>
	</div>
<?php
} else {														?>
	<span>No results</span>
<?php
}

} while (0); #If there is a break, the code will jump to here automatically.
																?>
</div>
<div id="options">
	<form id="filter">
		Student name:
		<input id="name"		class="text" 		type="text" 		name="name" 		placeholder="Part of name or whole name"><br>
		
		Student grade:
		<input id="grade" 		class="text" 		type="number" 		name="grade" 		placeholder="Any value 9-12" min="9" max="12"><br>
		
		Student ID:
		<input id="student_id" 	class="text" 		type="text" 		name="student_id" 	placeholder="Seven digit Student ID"><br>
		
		Period:
		<input id="period" 		class="text" 		type="number" 		name="period" 		placeholder="Any value 1-11" min="1" max="11"><br>
		
		Start date:<br>
		<input id="date_start" 	class="text"		type="date" 		name="date_start" 	placeholder="Date: YYYY-MM-DD"><br>
		
		End date:<br>
		<input id="date_end" 	class="text"		type="date" 		name="date_end" 	placeholder="Date: YYYY-MM-DD"><br>
		
		<div class="center">
			Sort type:<br>
			<label><input id="sort" 				type="radio" 		name="sort" 	value="new-old" checked="yes"> New on top</label><br> 
			<label><input id="sort" 				type="radio"		name="sort" 	value="old-new"> Old on top</label>
		</div>
		
		<span><?php echo $sortError;?></span>
		
		<div class="padding"><input type="submit" value=" Filter! "></div>
	</form>
</div>
<div id="logout">
	<form action="/view.php" method="get" style="padding: 0">
		<button type="submit" name="view" value="current">Students in Room</button>
	</form>
	<form action="/logout.php" method="get" style="padding: 0">
		<input type="submit" value=" Logout ">
	</form>
</div>
</div>
</body>
</html>