<?php
	// Christian's database
	$hostname = "localHost";
	$databaseC = "ecomerce";
	$user = "root";
	$password = "";

	$mysqliC = new mysqli($hostname, $user, $password, $databaseC);

	if ($mysqliC->connect_error) {
    	die("Connection failed Christian's database: " . $mysqliC->connect_error);
	}

	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	// Hector's database
	$hostname = "localHost";
	$databaseH = "ecommerce";
	$user = "root";
	$password = "";

	$mysqliH = new mysqli($hostname, $user, $password, $databaseH);

	if ($mysqliH->connect_error) {
    	die("Connection failed Hector's database: " . $mysqliH->connect_error);
	}

	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	// MSE's database
	$hostname = "localHost";
	$databaseM = "ecommerce 2.0";
	$user = "root";
	$password = "";

	$mysqliM = new mysqli($hostname, $user, $password, $databaseM);

	if ($mysqliM->connect_error) {
    	die("Connection failed MSE's database: " . $mysqliM->connect_error);
	}

	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>