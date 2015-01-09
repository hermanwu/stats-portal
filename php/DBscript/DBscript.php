<?php

// Create connection
$con=mysqli_connect("localhost","root","root", "statsPortal");
//$con=mysqli_connect("localhost","zwu36_admin","wzx131106", "zwu36_statsPortal");

// Check connection
if (mysqli_connect_errno()) {
	die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Check whether table exists
$sqlSelectTicketTable =
		"SELECT *
		 FROM information_schema.tables 
		 WHERE table_schema = 'statsPortal' 
		 AND table_name = 'Tickets'";
		

//$sqlCreateDB="CREATE DATABASE statsPortal";
$sqlCreateTicketsTable = 
		"CREATE TABLE Tickets
		(
		TicketID INT NOT NULL,
		AssigneeID INT,
		CreatedDate CHAR(100),
		LastAgentCommentDate CHAR(100),
		LastUpdatedTime CHAR(100),
		TicketStatus TINYINT(1),
		PRIMARY KEY (TicketID)
		)";

$sqlSelectOrganizataionTable =
"SELECT * 
 FROM information_schema.tables
 WHERE table_schema = 'statsPortal' 
 AND table_name = 'Organizations'";
		

$sqlCreateOrganizationsTable =
		"CREATE TABLE Organizations
		(
		OrganizationID INT NOT NULL,
		OrganizationName CHAR(150) NOT NULL,	
		PRIMARY KEY (OrganizationID)
		)";


$sqlSelectTicketTableResult = mysqli_query($con,$sqlSelectTicketTable);

if(mysqli_num_rows($sqlSelectTicketTableResult)>0){
	echo "Ticket Table exists<br>";
}
else{
	
	if (mysqli_query($con,$sqlCreateTicketsTable)) {
		echo "Ticket Table created successfully<br>";
	} else {
		die("Error creating table: " . mysqli_error($con));
	}	
}

$sqlSelectOrganizationTableResult = mysqli_query($con, $sqlSelectOrganizataionTable);

if(mysqli_num_rows($sqlSelectOrganizationTableResult)>0){
	echo "Organizations Table exists<br>";
}
else{

	if (mysqli_query($con,$sqlCreateOrganizationsTable)) {
		echo "Organizations Table created successfully<br>";
	} else {
		die("Error creating table: " . mysqli_error($con));
	}
}




die ("all scripts finished successfully");



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



?>

