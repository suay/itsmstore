<?php
/* 	backend call for delete sitename exprie
*	method : POST
*	input parameter customername = sitename, dateend = date exprie,package = plan
*	return json
*	26-04-2021
*/

	if( empty($_REQUEST['customername']) ){
		//$value = array('code'=>'Error','message'=>'Request Customer Name.');
		echo json_encode( array('mscode'=>'Error','message'=>'Request Customer Name.') );
		exit;
	}
	if( empty($_REQUEST['dateend']) ){
		echo json_encode( array('mscode'=>'Error','message'=>'Request Date End.') );
		exit;
	}
	if( empty($_REQUEST['package']) ){
		echo json_encode( array('mscode'=>'Error','message'=>'Request Package.') );
		exit;
	}



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

//Response after the connection is successful

   //echo "Connected successfully";

   $result = mysqli_query($mysqli, "SELECT * FROM wp_postmeta where meta_key='_order_website' and meta_value ='".$_REQUEST['customername']."' order by meta_id desc");

   $ckrow = mysqli_num_rows($result);

   if( $ckrow !== 0){
   
	   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{    
		    foreach ($row as $key => $value) {
		        //ค้างต้องลบ รายการด้วยหรือเปล่า
	    		// $deletes = "DELETE FROM wp_postmeta WHERE meta_id = '".$row['meta_id']."'";
	    		$rede = mysqli_query($mysqli, "DELETE FROM wp_postmeta WHERE meta_id = '".$row['meta_id']."'");	

		    }
		  	$message = "successfully";
		}  
   
   	echo json_encode( array('mscode'=>'Success','message'=>$message) );
   	
   }else{
   	 echo json_encode( array('mscode'=>'Success','message'=>'successful') );
   }
   

?>