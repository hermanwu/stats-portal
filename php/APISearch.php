<?php
//connect to sql database
include 'dbInfor.php';
$con=mysqli_connect($servername,$username,$password, $dbname);
//$con=mysqli_connect("localhost","zwu36_admin","wzx131106", "zwu36_statsPortal");
// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

//$email = $_POST["postEmail"];  

$createdDate = $_POST["postCreatedDate"];
//$createdDate = "2014-11-15";
$cacheFileName = $_POST["postFunctionName"];
//$cacheFileName = "test";

$searchQueryForRedTicket="created>".$createdDate."+type:ticket+fieldvalue:red+group:20045507+group:21550038+group:21232998+group:22222246+group:20823347";
$searchQueryForYellowTicket="created>".$createdDate."+type:ticket+fieldvalue:yellow+group:20045507+group:21550038+group:21232998+group:22222246+group:20823347";
//$searchQueryForRedTicket="created>2014-10-17+type:ticket+fieldvalue:red+group:20045507+group:21550038+group:21232998+group:22222246+group:20823347";
//$searchQueryForYellowTicket="created>2014-10-17+type:ticket+fieldvalue:yellow+group:20045507+group:21550038+group:21232998+group:22222246+group:20823347";
//$searchQuery="created>".$createdDate."+type:ticket+fieldvalue:".$temperature."+group:20045507+group:21550038+group:21232998+group:22222246+group:20823347";
$searchAPIBase="/search.json?query=";
$completeSearchQueryForRedTicket=$searchAPIBase.$searchQueryForRedTicket;
$completeSearchQueryForYellowTicket=$searchAPIBase.$searchQueryForYellowTicket;

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
    
    
    //find the organization group name
   
    function getOrganizationGroupName($ogid, $dbcon){
    	//echo "SELECT * FROM Organizations where OrganizationID =".$ogid;
    	$result = mysqli_query($dbcon,"SELECT * FROM Organizations where OrganizationID =".$ogid);
    	$row = mysqli_fetch_array($result);
    	if ( $row == null)
    	{
    		$organizationSearchQuery ="/organizations/".$ogid;
    		$organzationJson = curlWrap($organizationSearchQuery, null, "GET");
    		$organizationJsonObj = json_decode($organzationJson, true);
    		$organizationName = mysqli_real_escape_string($dbcon, $organizationJsonObj['organization']['name']);
    		if (!mysqli_query($dbcon,"INSERT INTO organizations (OrganizationID, OrganizationName) VALUES ("
    				.$ogid.", '"
    			    .(string)$organizationName."')"))
    		{
    			echo("Error description: " . mysqli_error($dbcon));
    		};
    		
    		return $organizationName;
    		
    	}
    	else{
    		return $row['OrganizationName'];
    	}
    }
    
    function getTicketArray($searchQuery, $ticketTemperature, $con){
    	$jsonResult = curlWrap($searchQuery, null, "GET");
    	$jsonObj = json_decode($jsonResult, true);
    	
    	foreach ($jsonObj['results'] as $index => $obj ){
    		
    		if($obj['organization_id']!=null){
    			$ogName = getOrganizationGroupName($obj['organization_id'], $con);
    		}
    		else{
    			$ogName = "No Organization";
    		}
    		$jsonObj['results'][$index]['organization_Name'] = $ogName;
    		$jsonObj['results'][$index]['temperature'] = $ticketTemperature;
    	}
    	
    	
    	$jsonObj = $jsonObj['results'];
    	return $jsonObj;
    }
    
    
    $cacheFile2 = '../cache' . DIRECTORY_SEPARATOR . $cacheFileName;
    
    if (file_exists($cacheFile2)) {
    
    	$fh = fopen($cacheFile2, 'r');
    	$cacheTime = trim(fgets($fh));
    	// if data was cached recently, return cached data
    	if ($cacheTime > strtotime('-30 minutes')) {
    		echo fgets($fh);
    		exit;
    	}
    	// else delete cache file
    	fclose($fh);
    	unlink($cacheFile2);
    }
    
    	$jsonObj1 = getTicketArray($completeSearchQueryForRedTicket, "red", $con);
    	$jsonObj2 = getTicketArray($completeSearchQueryForYellowTicket, "yellow", $con);
    	
    	
    	$jsonNewString = json_encode(array_merge($jsonObj1, $jsonObj2));
 
    	$fh = fopen($cacheFile2, 'w');
    	fwrite($fh, time() . "\n");
    	fwrite($fh, $jsonNewString);
    	fclose($fh);
    	
    	echo $jsonNewString;
    
?>