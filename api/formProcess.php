<?php


$errorMSG = "";
/* NAME */
if (empty($_POST["name"])) {
    $errorMSG = "<li>Name is required</<li>";
} else {
    $name = $_POST["name"];
}


/* EMAIL */
if (empty($_POST["email"])) {
    $errorMSG .= "<li>Email address is required</li>";
} else if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $errorMSG .= "<li>Invalid email format</li>";
}else {
    $email = $_POST["email"];
}

/* MSG SUBJECT */
// if (empty($_POST["msg_subject"])) {
//     $errorMSG .= "<li>Subject is required</li>";
// } else {
//     $msg_subject = $_POST["msg_subject"];
// }


/* COMPANY */
if (empty($_POST["company"])) {
    $errorMSG .= "<li>Your preferred site name is required. (example: company.itsmnetka.com)</li>";
} else if( preg_match('/^[a-z0-9]+$/i', $_POST["company"]) === 0 ){
    $errorMSG .= "<li>Invalid Your preferred site name format. Please enter only characters and number</li>";
}else{
    $company = $_POST["company"];
}

/*check policy*/
if(empty($_POST['policys']) ){
    $errorMSG .= "<li>Please agree Terms and Privacy</li>";
}else{
    $ckpolicy = $_POST['policys'];
}

/* TELEPHONE */
// if (empty($_POST["tel"])) {
//     $errorMSG .= "<li>Phone is required</li>";
// } else {
    $tel = $_POST["tel"];
// }

/* VOUCHER */
$voucher = $_POST['voucher'];


if(empty($errorMSG)){
	// $msg = "Name: ".$name.", Email: ".$email.", Subject: ".$msg_subject.", Message:".$message;
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
    if(!empty($company)){
        $resultckwebsite = mysqli_query($mysqli, "SELECT id FROM user_freetrial where website_url='".$company."'");
        $ckrowwebsite = mysqli_num_rows($resultckwebsite);
        if( $ckrowwebsite !==0 ){
            echo json_encode(['code'=>404, 'msg'=>'<li>'.$company.' is invalid data has already been used</li>']);
            exit;
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
                mysqli_query($mysqli, "UPDATE voucher_free SET use_coupon = '".$update_use."' WHERE voucher_code='".$voucher."'");
        }else{
            $dayforfree = "15 days";
            echo json_encode(['code'=>404, 'msg'=>'<li>Invalid Voucher Code</li>']);
            exit;
        } 
        /*insert db user*/
        $sqlre = "INSERT INTO user_freetrial (id, first_lastname, email_free,website_url,telephone,company,date_created ) VALUES('','".$name."','".$email."','".$company."','".$tel."','',NOW())";
        mysqli_query($mysqli, $sqlre);
        /*call curl api free trial*/
        $datasend = 'username='.$company.'&agent=5&package=L&type=DEMO&email='.$email.'&region=TH&country=TH-10&isper='.$dayforfree;

        echo json_encode(['code'=>200, 'msg'=>'Thank you for your order, now your service desk is ready. Please kindly check your registered e-mail for the details.']);
    }else{
        $dayforfree = "15 days";
        /*insert db user*/
        $sqlre = "INSERT INTO user_freetrial (id, first_lastname, email_free,website_url,telephone,company,date_created ) VALUES('','".$name."','".$email."','".$company."','".$tel."','',NOW())";
        mysqli_query($mysqli, $sqlre);
        /*call curl api free trial*/
        $datasend = 'username='.$company.'&agent=5&package=L&type=DEMO&email='.$email.'&region=TH&country=TH-10&isper='.$dayforfree;

        echo json_encode(['code'=>200, 'msg'=>'Thank you for your order, now your service desk is ready. Please kindly check your registered e-mail for the details.']);
    }
    
    

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
    exit;

 //    $msg = "Name: ".$name.", Email: ".$email.", Domain:".$company.", Tel:".$tel.", Voucher:".$voucher;
	// echo json_encode(['code'=>200, 'msg'=>$msg]);
	// exit;
}


echo json_encode(['code'=>404, 'msg'=>$errorMSG]);


?>