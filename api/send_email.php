<?php
$name = $_POST['fame'];
$sitename = $_POST['sitename'];
//$name = $fname." ".$lname;
$email = $_POST['email'];
$tel = $_POST['tel'];
$voucher = $_POST['voucher'];
// $toEmail = "";

$email = filter_var($email, FILTER_SANITIZE_EMAIL);
if(filter_var($email, FILTER_VALIDATE_EMAIL)){
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
	/*check name is same*/
    if(!empty($sitename)){
        $resultckwebsite = mysqli_query($mysqli, "SELECT id FROM user_freetrial where website_url='".$sitename."'");
        $ckrowwebsite = mysqli_num_rows($resultckwebsite);
        if( $ckrowwebsite !==0 ){
            $showMessage =  $sitename.' is invalid data has already been used';
        }
    }
    /*check voucher*/
    if(!empty($voucher) ){
        $result = mysqli_query($mysqli, "SELECT use_coupon,voucher_code FROM voucher_free where voucher_code='".$voucher."' and use_coupon < use_limit");
        $ckrow = mysqli_num_rows($result);
        if( $ckrow !== 0){
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            foreach ($row as $key => $value) {
                $update_use = $row['use_coupon'] + 1;
            }
                /*update use coupon*/
                $dayforfree = "30 days";
                //mysqli_query($mysqli, "UPDATE voucher_free SET use_coupon = '".$update_use."' WHERE voucher_code='9UCHC2QV'");
        }else{
            $dayforfree = "10 days";
            $showMessage =  'Invalid Voucher Code';
        }
        
    }else{
        $dayforfree = "10 days";
    }
    /*insert db user*/
    $sqlre = "INSERT INTO user_freetrial (id, first_lastname, email_free,website_url,telephone,company,date_created ) VALUES('','".$name."','".$email."','".$sitename."','".$tel."','',NOW())";
    //mysqli_query($mysqli, $sqlre);
    /*call curl api free trial*/
    $datasend = 'username='.$sitename.'&agent=5&package=L&type=DEMO&email='.$email.'&region=TH&country=TH-10&isper='.$dayforfree;

    $showMessage ='Thank you for your order, now your service desk is ready. Please kindly check your registered e-mail for the details.';
    
    // $curl = curl_init();

    // curl_setopt_array($curl, array(
    //   CURLOPT_URL => 'http://localhost:8000/newpayment1/public/api/chargeforfree',
    //   CURLOPT_RETURNTRANSFER => true,
    //   CURLOPT_ENCODING => '',
    //   CURLOPT_MAXREDIRS => 10,
    //   CURLOPT_TIMEOUT => 0,
    //   CURLOPT_FOLLOWLOCATION => true,
    //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //   CURLOPT_CUSTOMREQUEST => 'POST',
    //   CURLOPT_POSTFIELDS => $datasend,
    //   CURLOPT_HTTPHEADER => array(
    //     'Content-Type: application/x-www-form-urlencoded'
    //   ),
    // ));

    // $response = curl_exec($curl);

    // curl_close($curl);
    // echo $response;
	
}else{
	$showMessage =  "invalid email";
}
// $showMessage = 'Your Query has been received, We will contact you soon.';
// if (filter_var($email, FILTER_VALIDATE_EMAIL)) {	
// 	$subject = "Contact us email";
// 	$headers  = 'MIME-Version: 1.0' . "\r\n";
// 	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
// 	$headers .= 'From: '.$email."\r\n".
//     'Reply-To: '.$email."\r\n" .
//     'X-Mailer: PHP/' . phpversion();	
// 	$message = 'Hello,<br/>'
// 	. 'Name:' . $name . '<br/>'	
// 	. 'Message:' . $comment . '<br/><br/>';		
// 	mail($toEmail, $subject, $message, $headers);
// 	$showMessage = "Your Query has been received, We will contact you soon.";	
// } else {
// 	$showMessage =  "invalid email";
// }
$jsonData = array(
	"message"	=> $showMessage
);
echo json_encode($jsonData); 
?>