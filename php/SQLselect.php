<?php
include 'SQLscript.php';
include 'dbInfor.php';
$con=mysqli_connect($servername,$username,$password, $dbname);
ini_set('display_errors', 'On');
$teamId = $_POST["teamId"];
//$teamId = 21232998;	
//$con=mysqli_connect("localhost","zwu36_admin","wzx131106", "zwu36_statsPortal");
// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$phoneTeamIdArray = array();
$phoneTeamNameArray = array();
$phoneTeamEmailArray = array();

$resultPhoneTeam = selectAll($con, 'agents', 'TeamID='.$teamId.' and PrimaryCategory = 1');
while ($row = mysqli_fetch_array($resultPhoneTeam)){
	$phoneTeamIdArray[]=$row['ZenDeskID'];
	$phoneTeamNameArray[]=$row['DisplayName'];
	$phoneTeamEmailArray[]=$row['Email'];
}

$webTeamIdArray = array();
$webTeamNameArray = array();
$webTeamEmailArray = array();

$resultWebTeam = selectAll($con, 'agents', 'TeamID ='.$teamId.' and PrimaryCategory = 2');
while ($row = mysqli_fetch_array($resultWebTeam)){
	$webTeamIdArray[]=$row['ZenDeskID'];
	$webTeamNameArray[]=$row['DisplayName'];
	$webTeamEmailArray[]=$row['Email'];
}

$resultArray = array($phoneTeamNameArray, $webTeamNameArray, $phoneTeamEmailArray, $webTeamEmailArray, $phoneTeamIdArray ,$webTeamIdArray);


$encodedArray = json_encode($resultArray);

echo $encodedArray;

mysqli_close($con);

?>