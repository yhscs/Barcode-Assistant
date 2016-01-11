<?php
include '/home/aj4057/config.php'; #Define $servername $username $password $dbname and $configready here.
session_start();
if(!isset($_SESSION['login_user'])){
	header("location: index.php");
	die();
}


do {
try {
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	$error = "Could not connect to the database. This should never happen.";
	break;
}

$per_page = 20; #how many results per page
$stmt = $conn->prepare("SELECT count(*) FROM LOG WHERE ROOM = :where"); #Get the total amount of posts
$stmt->execute(array('where' => $_SESSION['login_user']));
$total_rows = $stmt->fetch(); #We have the total amount of posts
$num_pages=ceil((int)$total_rows[0]/$per_page); #max page number


#never trust the user.
if (isset($_GET['page'])) {
	$CUR_PAGE = intval($_GET['page']);
} else {
	$CUR_PAGE=1;
}
if ($CUR_PAGE > $num_pages || $CUR_PAGE <= 0) {
	$CUR_PAGE = 1;
}
$start = abs(($CUR_PAGE-1)*$per_page); #now figure out where to start

#now let's form new query string without page variable
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

$stmt = $conn->prepare("SELECT * FROM LOG WHERE ROOM = :where ORDER BY ID DESC LIMIT :starting,:postsperpage"); #select the actual data
$stmt->bindParam(":where", $_SESSION['login_user'], PDO::PARAM_STR);
$stmt->bindParam(":starting", $start, PDO::PARAM_INT);
$stmt->bindParam(":postsperpage", $per_page, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(); #and put it in an array
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
<?php
  foreach ($result as $row) {
	  echo "	<tr>\r\n";
	  echo "		<td>" . $row["ID"] . "</td>\r\n";
	  echo "		<td>" . ($row["CHECKIN"] === "1" ? "Arrival" : "Departure") . "</td>\r\n";
	  echo "		<td>" . $row["STUDENT_ID"] . "</td>\r\n";
	  echo "		<td>" . $row["STUDENT_NAME"] . "</td>\r\n";
	  echo "		<td>" . $row["STUDENT_GRADE"] . "</td>\r\n";
	  echo "		<td>" . $row["TIME"] . "</td>\r\n";
	  echo "		<td>" . $row["PERIOD"] . "</td>\r\n";
	  echo "		<td>" . ($row["AUTO"] === "1" ? "Yes" : "No") . "</td>\r\n";
	  echo " 	</tr>\r\n";
  }
?>
</table>
</div>
<?
echo "<div class=\"info\">\r\n";
echo "	<div class=\"pages\">\r\n";
echo "		Page<br> \r\n";
$counter = 1;
foreach ($PAGES as $i => $link){
	if(($leftElipse === true && $counter === 2) || 
	   ($rightElipse === true && $counter === 9)) {
		echo "		<b> ... </b>\r\n";
	}
	if ($i == $CUR_PAGE){
		echo "		<b>" . $i . "</b>\r\n";
	} else {
		if ($i == 1) {
			echo "		<a href=\"" . explode('?', $link, 2)[0] . "\">" . $i . "</a>\r\n";
		} else {
			echo "		<a href=\"" . $link . "\">" . $i . "</a>\r\n";
		}
	}
	$counter++;
}
echo "	</div>\r\n";
echo "</div>\r\n";
} while (0); # Really works!
?>
<div id="options">
	<form id="filter">
		<input id="stname"						type="checkbox" 	name="select" 	value="name">Student name:
		<input id="name"		class="text" 	type="text" 		name="name"><br>
		
		<input id="stgrade" 					type="checkbox" 	name="select" 	value="grade">Student grade:
		<input id="grade" 		class="text" 	type="text" 		name="grade"><br>
		
		<input id="stid" 						type="checkbox" 	name="select" 	value="id">Student ID:
		<input id="student_id" 	class="text" 	type="text" 		name="student_id"><br>
		
		<input id="period_val"					type="checkbox" 	name="select" 	value="period">Period:
		<input id="period" 		class="text" 	type="text" 		name="period"><br>
		
		<input id="time" 						type="checkbox"		name="select" 	value="time">Time range (YYYY-MM-DD HH:MM:SS):<br>
		
		Start date and time:
		<input id="time_start" 	class="text" 	type="text" 		name="time_start"><br>
		End date and time:
		<input id="time_end" 	class="text" 	type="text" 		name="time_end" ><br>
		
		<div class="center">
			Sort type:<br>
			<input id="sort" 					type="radio" 		name="sort" 	value="new-old" checked="yes"> New on top<br> 
			<input id="sort" 					type="radio"		name="sort" 	value="old-new"> Old on top
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