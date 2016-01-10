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

$per_page = 5; #how many results per page
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
if(count($PAGES) > 8) {
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
?>
<table>
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
	  echo "	<tr>";
	  echo "		<td>" . $row["ID"] . "</td>";
	  echo "		<td>" . ($row["CHECKIN"] === "1" ? "Arrival" : "Departure") . "</td>";
	  echo "		<td>" . $row["STUDENT_ID"] . "</td>";
	  echo "		<td>" . $row["STUDENT_NAME"] . "</td>";
	  echo "		<td>" . $row["STUDENT_GRADE"] . "</td>";
	  echo "		<td>" . $row["TIME"] . "</td>";
	  echo "		<td>" . $row["PERIOD"] . "</td>";
	  echo "		<td>" . ($row["AUTO"] === "1" ? "Yes" : "No") . "</td>";
	  echo " 	</tr>";
  }
  ?>
</table>
<?
echo "<div class=\"info\">\r\n";
echo "	<div class=\"pages\">\r\n";
echo "		Page: \r\n";
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