<?php 
include 'dbInfor.php';

$agentId = $_POST["agentId"];
//$agentId = 11111;
$fullStatement = "DELETE from agents where ZenDeskID =".$agentId;

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
?>