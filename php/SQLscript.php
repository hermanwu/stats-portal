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

function getTeamID($teamName){
	if($teamName == 'Enhanced' ){
		return 21232998;
	}
	else{
		return 0;
	}
}

?>