<?php
	$host = "localhost"; //Host address
	$user = "useername"; //Name of database user
	$pass = "password"; //Password for database user
	$name = "track"; //Name of Database
	$mysqli = new mysqli($host, $user, $pass, $name);
	GLOBAL $mysqli;
	if(mysqli_connect_errno()) {
		echo "Connection Failed here: " . mysqli_connect_errno();
		exit();
	}
?>