<?php 
include 'ZendeskAPICurlCall.php';
include 'dbInfor.php';
$con=mysqli_connect($servername,$username,$password, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

	$agentName = $_POST["agentName"];

	//$email = $_POST["postEmail"];
	$url =$agentName."Active";
	$cacheFile = '../cache' . DIRECTORY_SEPARATOR . $url;
	$fh = fopen($cacheFile, 'r');

	$cacheTime = trim(fgets($fh));
	$jsonActiveTickets = fgets($fh);
	$jsonObj = json_decode($jsonActiveTickets, true);

	$lastUpdatedTimeArray = array();
	
	foreach ($jsonObj['results'] as $obj)
	{
		//echo $assigneeId;
		$ticketID = $obj['id'];
		$assigneeId = $obj['assignee_id'];
		$lastUpdatedTimeArray[]=$ticketID;
		$result = mysqli_query($con,"SELECT * FROM tickets where TicketID =".$ticketID);
		//echo "test";
		$row = mysqli_fetch_array($result);
		if ( $row == null)
		{	
			$ticketCommentQuery ="/tickets/".$ticketID."/comments.json";
			$ticketCommentJson = curlWrap($ticketCommentQuery, null, "GET");
			$ticketCommentJsonObj = json_decode($ticketCommentJson, true);
			$sizeOfComments = $ticketCommentJsonObj['count'];
			$endIndex = $sizeOfComments-1;
			while ($ticketCommentJsonObj['comments'][$endIndex]['public'] != true ||
				   $ticketCommentJsonObj['comments'][$endIndex]['author_id'] != $assigneeId){
				if ($endIndex == 0){
					break;
				}
				$endIndex--;
			}
			$ticketLastUpdatedDate = $ticketCommentJsonObj['comments'][$endIndex]['created_at'];
			$lastUpdatedTimeArray[]=$ticketLastUpdatedDate;
		}
		else{
				//echo $row['LastAgentCommentDate'];
				//echo $row['LastAgentCommentDate'];
			$lastUpdatedTimeArray[] = $row['LastAgentCommentDate'];
		}	
		/*
		$ticketCommentQuery ="/tickets/".$obj['id']."/comments.json";
		$ticketCommentJson = curlWrap($ticketCommentQuery, null, "GET");
	
		//transfer comment from string to json object
		$ticketCommentJsonObj = json_decode($ticketCommentJson, true);
		//get the number of comments
		$sizeOfComments = $ticketCommentJsonObj['count'];
		$endIndex = $sizeOfComments-1;
	
		while ($ticketCommentJsonObj['comments'][$endIndex]['public'] != true ||
				$ticketCommentJsonObj['comments'][$endIndex]['author_id'] != $assigneeId){
			$endIndex--;
			if ($endIndex == 0){
				break;
			}
		}
		$ticketLastUpdatedDate = $ticketCommentJsonObj['comments'][$endIndex]['created_at'];
	
		mysqli_query($con,"REPLACE INTO Tickets (TicketID, AssigneeID, CreatedDate, LastAgentCommentDate) VALUES ("
					.$ticketID.", "
					.$assigneeId.", '"
					.(string)$ticketCreatedDate."', '"
					.(string)$ticketLastUpdatedDate."')");
		*/
   }
   //echo $lastUpdatedTimeArray[1];
   $js_array = json_encode($lastUpdatedTimeArray);
   //echo "var javascript_array = ". $js_array . ";\n";
   echo $js_array;
   //echo $ticketArray[0];
   //echo $jsonActiveTickets;
   mysqli_close($con);
?>