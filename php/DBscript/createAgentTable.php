<?php
// Create connection
$con=mysqli_connect("localhost","root","root", "statsPortal");

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$sqlCreateDB="CREATE DATABASE statsPortal";

$sqlCreateAgentsTable = "CREATE TABLE Agents 
(
ZenDeskID INT,
Status INT NOT NULL,
DisplayName CHAR(100) NOT NULL,
Email CHAR(100) NOT NULL,
Password CHAR(100),
TeamID INT,
SME INT,
PrimaryCategory INT,
SecondlyCategory INT,
PRIMARY KEY(ZenDeskID)
)";

/*
if (mysqli_query($con,$sqlCreateDB)) {
	echo "Database my_db created successfully";
} else {
	echo "Error creating database: " . mysqli_error($con);
}
*/

/*
$db_selected = mysql_select_db('statsportal', $con);
if (!$db_selected) {
	die ('Can\'t use foo : ' . mysql_error());
}
*/

if (mysqli_query($con,$sqlCreateAgentsTable)) {
	echo "Agents Table created successfully";
} else {
	echo "Error creating database: " . mysqli_error($con);
}
?>

