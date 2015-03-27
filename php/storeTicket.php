<?php
//ini_set('MAX_EXECUTION_TIME', -1);
ini_set('display_errors', 'On');
include 'dbInfor.php';
include 'SQLscript.php';
include 'ZendeskAPICurlCall.php';
include 'pdoSchedule.php';

$con=mysqli_connect($servername,$username,$password, $dbname);

$teamID = $_POST["postTeam"];
//$teamID = 21232998; 

$cacheNameUrl = "cacheTicketCall".$teamID;

$cacheFile = '../cache' . DIRECTORY_SEPARATOR .$cacheNameUrl;

if (file_exists($cacheFile)) {
	$fh = fopen($cacheFile, 'r');
	$cacheTime = trim(fgets($fh));
	// if data was cached recently, return cached data
	if ($cacheTime > strtotime('-15 minutes')) {
		exit();	
	}
	// else delete cache file
	fclose($fh);
	unlink($cacheFile);
}
//update the current time to last update date
$fh = fopen($cacheFile, 'w');
fwrite($fh, time() . "\n");
fclose($fh);

$testEmails = selectOneColumn($con, 'ZenDeskID', 'agents', 'TeamID='.$teamID);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

setTicketToNotUpdated($PDOconnection);

/*
$lastUpdatedTimeString = selectSchedule($dbConnection, $teamID);
$lastUpdatedTime = date_create_from_format('Y-m-d h:i a', $lastUpdatedTimeString);
$beforeLastUpdatedDate = date('Y-m-d', $lastUpdatedTime->getTimestamp()-60*60*24);
*/
//store this time to database as the updated time;

foreach ($testEmails as $agentId)
{
	$openTicketQuery ="/search.json?query=assignee:".$agentId."+type:ticket+status<solved";
	$jsonString = curlWrap($openTicketQuery, null, "GET");
	$jsonObj = json_decode($jsonString, true);
	//wait 5 secs
	//sleep(1);
	foreach ($jsonObj['results'] as $obj)
	{
			$assigneeId = $obj['assignee_id'];
			//echo $assigneeId;
			$ticketID = $obj['id'];
			//echo $ticketID;
			$ticketCreatedDate = $obj['created_at'];
			//echo $ticketCreatedDate;
			$ticketUpdateData = $obj['updated_at'];
			$ticketStatus = $obj['status'];
			$ticketType = $obj['type'];
			$ticketSubject = mysqli_real_escape_string($con, $obj['subject']);
			$ticketPriority = $obj['priority'];
			$groupID = $obj['group_id'];
		
			$ticketCommentQuery ="/tickets/".$obj['id']."/comments.json";
			$ticketCommentJson = curlWrap($ticketCommentQuery, null, "GET");
		
			//transfer comment from string to json object
			$ticketCommentJsonObj = json_decode($ticketCommentJson, true);
			//get the number of comments
			$sizeOfComments = $ticketCommentJsonObj['count'];
			$endIndex = $sizeOfComments-1;
		
			while ($ticketCommentJsonObj['comments'][$endIndex]['public'] != true ||
				   $ticketCommentJsonObj['comments'][$endIndex]['author_id'] != $assigneeId)
			{
				if ($endIndex == 0){
					break;
				}
				$endIndex--;
			}
			$ticketLastUpdatedDate = $ticketCommentJsonObj['comments'][$endIndex]['created_at'];	
		
		mysqli_query($con,"REPLACE INTO tickets (
				TicketID, 
				AssigneeID, 
				CreatedDate, 
				LastAgentCommentDate,
				TicketStatus,
				Type,
				Subject,
				Priority,
				GroupID,
				UpdatedDate,
				Updated) VALUES ("
			.$ticketID.", "
			.$assigneeId.", '"
			.(string)$ticketCreatedDate."', '"
			.(string)$ticketLastUpdatedDate."', '"
			.(string)$ticketStatus."', '"		
			.(string)$ticketType."', '"
			.(string)$ticketSubject."', '"
			.(string)$ticketPriority."', '"
			.$groupID."', '"
			.(string)$ticketUpdateData."', 1)");
	}
	closeNotUpdatedTicket($PDOconnection, $agentId);
}



updateSchedule($dbConnection, $teamID);

mysqli_close($con);
?>