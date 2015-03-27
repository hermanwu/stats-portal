<?php 


/*
//hostgator 
$servername = "localhost";
$username = "zwu36_admin";
$password = "wzx131106";
$dbname = "zwu36_statsPortal";
*/

//localhost
$servername = "localhost";
$username = "root";
$password = "wzx131106";
$dbname = "statsportal";

/*
//serverMonkey
$servername = "localhost";
$username = "root";
$password = "abcd.1234";
$dbname = "statsPortal"; 
*/

$PDOconnection = new PDO("mysql:host=$servername;dbname=$dbname", $username,$password);
$PDOconnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>