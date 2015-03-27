<?php
ini_set('display_errors', 'On');
include 'dbInfor.php';
include 'pdoSchedule.php';
include 'SQLscript.php';

if(isset($_POST['action'])&&!empty($_POST['action'])){
	$action = $_POST['action'];
	switch($action){
		case 'selectSchedule' : 
			$teamID = $_POST['input1'];
			echo selectSchedule($PDOconnection, $teamID);
			break;
		case 'getActiveTicket' :
			$agentId = $_POST['input1'];
			echo getActiveTicket($PDOconnection, $agentId);
			break;
		case 'retrieveActiveTicketArray' :
			$agentId = $_POST['input1'];
			echo retrieveActiveTicketArray($PDOconnection, $agentId);
			break;
			
		
	}
}


?>