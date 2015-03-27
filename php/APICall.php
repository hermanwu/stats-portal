<?php
	include 'ZendeskAPICurlCall.php';
	
    $searchQuery = $_POST["searchQuery"];  
    $queryName = $_POST["queryName"];
	
    function getJson($url, $qn) {
    	// cache files are created like cache/abcdef123456...
    	$cacheFile = '../cache' . DIRECTORY_SEPARATOR . $qn;
    	 
    	if (file_exists($cacheFile)) {
    		$fh = fopen($cacheFile, 'r');
    		$cacheTime = trim(fgets($fh));
    		// if data was cached recently, return cached data
    		if ($cacheTime > strtotime('-15 minutes')) {
    			return fgets($fh);
    		}
    
    		// else delete cache file
    		fclose($fh);
    		unlink($cacheFile);
    	}
    	$json = curlWrap($url, null, "GET");
    	 
    	$fh = fopen($cacheFile, 'w');
    	fwrite($fh, time() . "\n");
    	fwrite($fh, $json);
    	fclose($fh);
    
    	return $json;
    }
      	
    echo getJson($searchQuery, $queryName);
    //echo curlWrap($solvedTicketQuery, null, "GET");
?>