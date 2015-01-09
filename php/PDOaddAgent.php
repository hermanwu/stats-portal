<?php 
include 'dbInfor.php';
include 'ZendeskAPICurlCall.php';

$agentEmail=$_POST['agentEmail'];
//$agentEmail="hermanwu@air-watch.com";
$category=$_POST['category'];
$teamId = $_POST['teamId'];
//$category=1;
//$teamId = 21232998;

$searchUserQuery ="/search.json?query=type:user+email:".$agentEmail;
$jsonString = curlWrap($searchUserQuery, null, "GET");
$jsonObj = json_decode($jsonString, true);

if($jsonObj['results'][0]['id']==null){
	echo "Cannot find user, please check the email address";
	exit();
}
else{
	$zenDeskID = $jsonObj['results'][0]['id'];
	$displayName = $jsonObj['results'][0]['name'];
	$status = 1;
	
	$sql = "INSERT INTO agents (ZenDeskID, Status, DisplayName, Email, TeamID, PrimaryCategory) VALUES ("
					.$zenDeskID.", "
					.$status.", '"
					.$displayName."', '"
					.$agentEmail."', "
				    .$teamId.", "
					.$category.")";
	
	try{
		$conn = new PDO("mysql:host=$servername;dbname=$dbname",$username,$password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->exec($sql);
		echo 1;
		
	}
	catch(PDOException $e){
		echo $e->getMessage();
	}
}


/*
$fullStatement = "DELETE from Agents where ZenDeskID =".$agentId;

try{
	$conn = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$sql = $fullStatement;
	
	$conn->exec($sql);
	echo 1;
}
catch(PDOException $e){
	echo $e->getMessage();
}

$conn=null;
*/
?>