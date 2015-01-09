<?php
$con=mysqli_connect("localhost","root","root", "statsPortal");

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


	$agents = array(
		"MarkRusinski",
		"HermanWu",
		"ChristopherMccloskey",
		"AdamEsch"
	);	

	$agentsWeb = array(
		"AshokRajendran",
		"AdityaBandikallu",
		"SergeyBelous",
		"AhmedIsmail",
		"FloraLi",
		"SajindraPradhananga",
		"SiddhantMalani"	
	);


    define("ZDUSER", "hermanwu@air-watch.com:12345");
    define("ZDURL", "https://airwatch.zendesk.com/api/v2");
    
    function curlWrap($url, $json, $action)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_URL, ZDURL.$url);
        //curl_setopt($ch, CURLOPT_USERPWD, ZDUSER."/token:".ZDAPIKEY);
        curl_setopt($ch, CURLOPT_USERPWD,ZDUSER );
        switch($action){
            case "POST":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                break;
            case "GET":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                break;
            case "PUT":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                break;
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        $output = curl_exec($ch);
        curl_close($ch);
        //$decoded = json_decode($output);
        return $output;
    }

	$size = count($agents);
	$status = 1;
	$teamId = 21232998;
	$category = 1;
	
	for ($x=0; $x<$size; $x++) {
		$searchUserQuery ="/search.json?query=type:user+email:".$agents[$x]."@air-watch.com";
		$jsonString = curlWrap($searchUserQuery, null, "GET");
		$jsonObj = json_decode($jsonString, true);
		
 	mysqli_query($con,"INSERT INTO Agents (ZenDeskID, Status, DisplayName, Email, TeamID, PrimaryCategory) VALUES ("
		 .$jsonObj['results'][0]['id'].", "
		 .$status.", '"
		 .$jsonObj['results'][0]['name']."', '"
		 .$jsonObj['results'][0]['email']."', "
		 .$teamId.", "
		 .$category.")");
	}
	
	$sizeWeb = count($agentsWeb);
	$statusWeb = 1;
	$teamIdWeb = 21232998;
	$categoryWeb = 2;
	
	for ($x=0; $x<$sizeWeb; $x++) {
		$searchUserQuery ="/search.json?query=type:user+email:".$agentsWeb[$x]."@air-watch.com";
		$jsonString = curlWrap($searchUserQuery, null, "GET");
		$jsonObj = json_decode($jsonString, true);
	
		mysqli_query($con,"INSERT INTO Agents (ZenDeskID, Status, DisplayName, Email, TeamID, PrimaryCategory) VALUES ("
			 .$jsonObj['results'][0]['id'].", "
			 .$statusWeb.", '"
			 .$jsonObj['results'][0]['name']."', '"
			 .$jsonObj['results'][0]['email']."', "
			 .$teamIdWeb.", "
			 .$categoryWeb.")");
	}
	

?>

