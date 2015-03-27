<?php 

/*
//include 'ZendeskAPICurlCall.php';
//include 'dbInfor.php';
$con=mysqli_connect($servername,$username,$password, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

	//$agentName = $_POST["emailAddress"];
	$agentName = 'sajindrapradhananga@air-watch.com';
	
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
			echo "no db";
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
			$lastUpdatedTimeArray[] = $row['LastAgentCommentDate'];
		}	
   }
   //echo $lastUpdatedTimeArray[1];
   $js_array = json_encode($lastUpdatedTimeArray);
   //echo "var javascript_array = ". $js_array . ";\n";
   echo $js_array;
   //echo $ticketArray[0];
   //echo $jsonActiveTickets;
   mysqli_close($con);
   */
?>