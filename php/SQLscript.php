<?php 
function selectAll($DBconnection, $tableName, $conditions){
	$fullStatement = "SELECT * FROM ".$tableName." where ".$conditions;
	$result = mysqli_query($DBconnection, $fullStatement);
	return $result;
}

// select only one column of data from database and pass it back as an array
function selectOneColumn($DBconnection, $columnName, $tableName, $conditions){
	$fullStatement = "SELECT ".$columnName." FROM ".$tableName." where ".$conditions;
	$result = mysqli_query($DBconnection, $fullStatement);
	$returnArray = array();
	while ($row = mysqli_fetch_array($result)){
		$returnArray[]=$row[$columnName];
	}
	return $returnArray;
}

function getActiveTicket($connection, $agentId){
	try{
		$returnArray = array();
		$stmt=$connection->prepare("SELECT * FROM tickets 
									WHERE assigneeId =".$agentId." AND (ticketstatus = 'pending' OR ticketstatus = 'open') 
									ORDER BY LastAgentCommentDate");
		$stmt->execute();
		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		foreach($stmt->fetchAll() as $rows){
			$newTimeString = date("m-d", strtotime($rows['LastAgentCommentDate']));
			$returnArray[] = array(
				"ticketID" => $rows['TicketID'],
				"createdDate" => $rows['CreatedDate'],
				"updatedDate" => $newTimeString
			); 
		}
		$returnjson = json_encode($returnArray);
		return $returnjson;
	}
	catch(PDOException $e){
		return "Error: ".$e->getMessage();
	}
}
function retrieveActiveTicketArray($connection, $agentId){
	try{
		$returnArray = array();
		$stmt=$connection->prepare("SELECT * FROM tickets WHERE assigneeId =".$agentId." AND (ticketstatus = 'pending' OR ticketstatus = 'open')");
		$stmt->execute();
		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		foreach($stmt->fetchAll() as $rows){
			$returnArray[] = $rows['TicketID'];
			$returnArray[] = $rows['LastAgentCommentDate'];
		}
		$returnjson = json_encode($returnArray);
		return $returnjson;
	}
	catch(PDOException $e){
		return "Error: ".$e->getMessage();
	}
}

function setTicketToNotUpdated($connection){
	try{
		$sql = "UPDATE tickets SET Updated = 0 WHERE (Updated IS NULL OR Updated = 1)";
		$stmt=$connection->prepare($sql);
		$stmt->execute();
	}
	catch(PDOException $e){
		return "Error: ".$e->getMessage();
	}
}


function closeNotUpdatedTicket($connection, $agentId){
	try{
		$sql = "UPDATE tickets SET TicketStatus = 'closed' WHERE Updated = 0 AND AssigneeID = ".$agentId;
		$stmt=$connection->prepare($sql);
		$stmt->execute();
	}
	catch(PDOException $e){
		return "Error: ".$e->getMessage();
	}
}


?>