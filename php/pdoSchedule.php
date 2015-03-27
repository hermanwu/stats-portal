<?php
//ini_set('display_errors', 'On');
//setup db connection
//include 'dbInfor.php';
$dbConnection = new PDO("mysql:host=$servername;dbname=$dbname", $username,$password);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function updateSchedule($conn, $teamID){
	try {		
		// set the PDO error mode to exception
		$updateTime = date('Y-m-d h:i a', time());
		$sql = "update schedule set LastUpdate= '$updateTime' where TeamID =".$teamID;
		$conn->exec($sql);
		//echo "New record created successfully";
	}
	catch (PDOException $e){
		echo $sql."<br>".$e->getMessage();
	}
}

function selectSchedule($conn, $teamID){
	try {
		$stmt = $conn->prepare("SELECT * from schedule where TeamID = ".$teamID);
		$stmt->execute();
		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		foreach($stmt->fetchAll() as $rows){
			return $rows['LastUpdate'];
		}
	}
	catch(PDOException $e){
		return "Error: " . $e->getMessage();
	}
}
?>