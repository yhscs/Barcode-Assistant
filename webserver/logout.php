<?php
session_start();
if(isset($_SESSION['valid'])) { 
	$_SESSION['valid'] = "false"; //Makes sure the session is killed.
}
if(session_destroy()) // Destroying All Sessions
{
header("Location: index.php"); // Redirecting To Home Page
}
header("Location: index.php");
?>