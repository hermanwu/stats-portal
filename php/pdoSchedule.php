<?php
//ini_set('display_errors', 'On');
//setup db connection
//include 'dbInfor.php';
$dbConnection = new PDO("mysql:host=$servername;dbname=$dbname", $username,$password);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function updateSchedule($conn){
	try {		
		// set the PDO error mode to exception
		$updateTime = date('Y-m-d h:i a', time());
		$sql = "update schedule set lastUpdate= '$updateTime' where scheduleID = 1";
		$conn->exec($sql);
		echo "New record created successfully";
	}
	catch (PDOException $e){
		echo $sql."<br>".$e->getMessage();
	}
}

function selectSchedule($conn){
	try {
		$stmt = $conn->prepare("SELECT * from schedule where scheduleID = 1");
		$stmt->execute();
		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		foreach($stmt->fetchAll() as $rows){
			echo $rows['lastUpdate'];
		}
	}
	catch(PDOException $e){
		echo "Error: " . $e->getMessage();
	}
}
?>