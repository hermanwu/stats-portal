<?php
//ini_set('display_errors', 'On');
//include 'ZendeskAPICurlCall.php';
include 'dbInfor.php';
include 'SQLscript.php';
include 'APICall.php';
$con=mysqli_connect($servername,$username,$password, $dbname);
//$con=mysqli_connect("localhost","zwu36_admin","wzx131106", "zwu36_statsPortal");

$teamID = $_POST["postTeam"];
//$teamID = 21232998; 

$cacheNameUrl = "cacheTicketCall".$teamID;

$cacheFile = '../cache' . DIRECTORY_SEPARATOR .$teamId.$cacheNameUrl;

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

$testEmails = selectOneColumn($con, 'Email', 'agents', 'TeamID='.$teamID);



// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

foreach ($testEmails as $email)
{
	$openTicketQuery ="/search.json?query=assignee:".$email."+type:ticket+status<solved";
	$queryName = $email."Active";
	$jsonString = getJson($openTicketQuery, $queryName);
	$jsonObj = json_decode($jsonString, true);
	//wait 5 secs
	sleep(5);
	
	foreach ($jsonObj['results'] as $obj)
	{
		$assigneeId = $obj['assignee_id'];
		//echo $assigneeId;
		$ticketID = $obj['id'];
		//echo $ticketID;
		$ticketCreatedDate = $obj['created_at'];
		//echo $ticketCreatedDate;
		
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
		
		
		
		mysqli_query($con,"REPLACE INTO tickets (TicketID, AssigneeID, CreatedDate, LastAgentCommentDate) VALUES ("
		.$ticketID.", "
		.$assigneeId.", '"
		.(string)$ticketCreatedDate."', '"
		.(string)$ticketLastUpdatedDate."')");
	}
}

//update the current time to last update date

mysqli_close($con);
//echo curlWrap($openTicketQuery, null, "GET");
$fh = fopen($cacheFile, 'w');
fwrite($fh, time() . "\n");
fclose($fh);
?>