<?php 

$servername = "localhost";
$username = "root";
$password = "wzx131106";
$dbname = "statsportal";

$conn = new mysqli($servername, $username, $password);

//echo phpinfo();
// Check connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
/*
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
		
		if(curl_errno($ch)){
			echo 'Curl error: ' . curl_error($ch);
		}
        curl_close($ch);
        return $output;
    } 	

$url = "/search.json?query=assignee:adityabandikallu@air-watch.com+type:ticket+status<solved";

$json = curlWrap($url, null, "GET");
//echo "Connected successfully";
echo $json;
*/

//$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");

//task scheduler test
/*
$txt = "John Doe\n";
file_put_contents("newfile.txt", $txt, FILE_APPEND);
$txt = "Jane Doe\n";
file_put_contents("newfile.txt", $txt, FILE_APPEND);
*/
//fclose($myfile);
//fclose($myfile);


?>