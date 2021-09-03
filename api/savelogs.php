<?php
/* 	backend call for save log difference type =>exm. log upgrade plan, log transaction recurring 
*	method : POST
*	input parameter username = sitename, order_id = order id,invoice = invoice id, email = email
*	return json
*	10-05-2021
*/

  $servername = "localhost"; // database host
  $username = "root"; //database username
  $password = ""; //databse password
  $db = "itsmstore4"; //database name

// Create connection
  $mysqli = new mysqli($servername, $username, $password,$db);

// Check connection
   if ($mysqli->connect_error) {
   die("Connection failed: " . $mysqli->connect_error);
   }


   echo $_REQUEST['version'];

?>