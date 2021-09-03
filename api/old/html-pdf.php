<?php
define('FPDF_FONTPATH','font/');
require('../fpdf183/fpdf.php');
date_default_timezone_set('UTC');

if( empty($_REQUEST['order_id']) ){
	//$value = array('code'=>'Error','message'=>'Request Customer Name.');
	echo json_encode( array('mscode'=>'Error','message'=>'Request OrderID.') );
	exit;
}
if( empty($_REQUEST['invoice_no']) ){
	echo json_encode( array('mscode'=>'Error','message'=>'Request InvoiceID.') );
	exit;
}
if( empty($_REQUEST['username']) ){
	echo json_encode( array('mscode'=>'Error','message'=>'Request Username.') );
	exit;
}
if( empty($_REQUEST['email']) ){
	echo json_encode( array('mscode'=>'Error','message'=>'Request Email.') );
	exit;
}

class PDF extends FPDF
{
//Colored table
function FancyTable($header,$data)
{
//Colors, line width and bold font
$this->SetFillColor(128,128,128);
$this->SetTextColor(0);
$this->SetDrawColor(0,0,0);
$this->SetLineWidth(.3);
$this->SetFont('angsana','',16);
//Header
$w=array(70,20,40,30,30);
for($i=0;$i<count($header);$i++)
$this->Cell($w[$i],10,$header[$i],1,0,'C',true);
$this->Ln();
//Color and font restoration
$this->SetFillColor(255,255,255);
$this->SetTextColor(0);
$this->SetFont('angsana','',16);
//Data
$fill=false;
foreach($data as $row)
{
$this->Cell($w[0],10,$row[23],'LR',0,'C',$fill);	
$this->Cell($w[1],10,$row[5],'LR',0,'C',$fill);
$this->Cell($w[2],10,$row[27],'LR',0,'C',$fill);
$this->Cell($w[3],10,$row[3],'LR',0,'C',$fill);
$this->Cell($w[4],10,$row[6],'LR',0,'C',$fill);
$this->Ln();
$fill=!$fill;
}
$this->Cell(array_sum($w),0,'','T');
}
}

$mysqli = new mysqli("localhost", "root", "","itsmstore4");
if ($mysqli->connect_error) {
   die("Connection failed: " . $mysqli->connect_error);
}

$strSQL = "SELECT meta_key,meta_value FROM wp_postmeta where post_id=".$_REQUEST['order_id']." and meta_key='status' and meta_value='active'";
$objQuery = mysqli_query($mysqli,$strSQL);
$resultData = array();
$ckrow = mysqli_num_rows($objQuery);
if( $ckrow !== 0){

$pdf=new PDF();


// เพิ่มฟอนต์ภาษาไทยเข้ามา ตัวธรรมดา กำหนด ชื่อ เป็น angsana
// $pdf->AddFont('angsana','','angsa.php');
// $pdf->AddFont('angsana','B','angsab.php');

//Column titles
$header=array(iconv( 'UTF-8','cp874' , 'รายการ' ),iconv( 'UTF-8','cp874' , 'จำนวน' ),iconv( 'UTF-8','cp874' , 'หน่วย' ),iconv( 'UTF-8','cp874' , 'หน่วยละ' ),iconv( 'UTF-8','cp874' , 'จำนวนเงิน' ));
//Data loading

//*** Load MySQL Data ***//
$resultperiod = mysqli_query($mysqli, "SELECT meta_key,meta_value FROM wp_postmeta where post_id=".$_REQUEST['order_id']."");
   
$resultData = array();
while ($rowck = mysqli_fetch_array($resultperiod, MYSQLI_ASSOC)) {
//for ($i=0;$i<10;$i++) {
//$result = mysql_fetch_array($objQuery);
$result["Drg_Name"];
$result["Drg_Qtt"];
$result["Unt_Name"];
$result["Price_Sale"];
$result["Total"];


array_push($resultData,$result);
}
//************************//



$pdf->SetFont('Arial','',16);

//*** Table 1 ***//
//$pdf->AddPage();
//$pdf->Image('logo.png',80,8,33);
//$pdf->Ln(35);
//$pdf->BasicTable($header,$resultData);

//*** Table 2 ***//
//$pdf->AddPage();
//$pdf->Image('logo.png',80,8,33);
//$pdf->Ln(35);
//$pdf->ImprovedTable($header,$resultData);

//*** Table 3 ***//
$pdf->AddPage();
//$pdf->Image('logo.jpg',80,8,33);
$pdf->SetFont('angsana','B',16);
$pdf->setXY( 20, 20 );
$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'ใบเสร็จรับเงิน' ) );
$pdf->SetFont('angsana','',16);
#$pdf->setXY( 130, 20 );
#$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'เลขที่' ) );
$pdf->setXY( 20, 30 );
$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'ร้านยาวิบุญเภสัช' ) );
$pdf->setXY( 135, 20 );
$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'วันที่ ' ). date('d').'/'. date('m').'/'.( date('Y')+543 ).'' );
$pdf->setXY( 180, 20 );
$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'หน้าที่ 1' ) );
$pdf->setXY( 20, 40 );
$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'เลขประจำตัวผู้เสียภาษีอากร 3920600715449' ) );
$pdf->setXY( 20, 50 );
$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'เลขที่ 47/17 หมู่ 3 ต. นาเหรง อ. นบพิตำ จ. นครศรีธรรมราช 80160' ) );
$pdf->setXY( 20, 60 );
$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'เลขที่รายการสั่งซื้อ ' ) );
#$pdf->setXY( 20, 70 );
#$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'ที่อยู่สังกัด' ) );
$pdf->Ln(15);
$pdf->FancyTable($header,$resultData);
$pdf->Ln();
#$pdf->setXY( 155, 200 );
#$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'รวมเงิน' ) ); 
#$pdf->setXY( 172, 195 );
#$pdf->cell(30,10," ",1,0); 
$pdf->Ln(15);
#$pdf->setXY( 140, 230 );
#$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , '.........................................................' ) ); 
#$pdf->setXY( 163, 240 );
#$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'ผู้รับเงิน' ) ); 

if($rows >10 && $rows <=20){

//$pdf=new PDF();


// เพิ่มฟอนต์ภาษาไทยเข้ามา ตัวธรรมดา กำหนด ชื่อ เป็น angsana
//$pdf->AddFont('angsana','','angsa.php');
//$pdf->AddFont('angsana','B','angsab.php');

//Column titles
$header=array(iconv( 'UTF-8','cp874' , 'รายการ' ),iconv( 'UTF-8','cp874' , 'จำนวน' ),iconv( 'UTF-8','cp874' , 'หน่วย' ),iconv( 'UTF-8','cp874' , 'หน่วยละ' ),iconv( 'UTF-8','cp874' , 'จำนวนเงิน' ));
//Data loading

//*** Load MySQL Data ***//
$objConnect = mysql_connect("localhost","root","123456") or die("Error Connect to Database");
$objDB = mysql_select_db("pharmacy");
mysql_query("SET NAMES 'tis620' ");
$strSQL = "SELECT * FROM sale_log INNER JOIN sale on sale_log.Rec_Id = sale.Rec_Id INNER JOIN drug on sale_log.Drg_Id = drug.Drg_Id WHERE sale_log.Rec_Id ='$Rec_Id' LIMIT 10,20";
$objQuery = mysql_query($strSQL);
$resultData = array();
for ($i=0;$i<mysql_num_rows($objQuery);$i++) {
//for ($i=0;$i<10;$i++) {
$result = mysql_fetch_array($objQuery);
$result["Drg_Name"];
$result["Drg_Qtt"];
$result["Unt_Name"];
$result["Price_Sale"];
$result["Total"];


array_push($resultData,$result);
}
//************************//



$pdf->SetFont('angsana','',16);

//*** Table 1 ***//
//$pdf->AddPage();
//$pdf->Image('logo.png',80,8,33);
//$pdf->Ln(35);
//$pdf->BasicTable($header,$resultData);

//*** Table 2 ***//
//$pdf->AddPage();
//$pdf->Image('logo.png',80,8,33);
//$pdf->Ln(35);
//$pdf->ImprovedTable($header,$resultData);

//*** Table 3 ***//
$pdf->AddPage();
//$pdf->Image('logo.jpg',80,8,33);
$pdf->SetFont('angsana','B',16);
$pdf->setXY( 20, 20 );
$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'ใบเสร็จรับเงิน' ) );
$pdf->SetFont('angsana','',16);
#$pdf->setXY( 130, 20 );
#$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'เลขที่' ) );
$pdf->setXY( 20, 30 );
$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'ร้านยาวิบุญเภสัช' ) );
$pdf->setXY( 135, 20 );
$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'วันที่ ' ). date('d').'/'. date('m').'/'.( date('Y')+543 ).'' );
$pdf->setXY( 180, 20 );
$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'หน้าที่ 2' ) );
$pdf->setXY( 20, 40 );
$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'เลขประจำตัวผู้เสียภาษีอากร 3920600715449' ) );
$pdf->setXY( 20, 50 );
$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'เลขที่ 47/17 หมู่ 3 ต. นาเหรง อ. นบพิตำ จ. นครศรีธรรมราช 80160' ) );
$pdf->setXY( 20, 60 );
$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'เลขที่รายการสั่งซื้อ '.$Rec_Id ) );
#$pdf->setXY( 20, 70 );
#$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'ที่อยู่สังกัด' ) );
$pdf->Ln(15);
$pdf->FancyTable($header,$resultData);
$pdf->Ln();
#$pdf->setXY( 155, 200 );
#$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'รวมเงิน' ) ); 
#$pdf->setXY( 172, 195 );
#$pdf->cell(30,10," ",1,0); 
$pdf->Ln(15);
#$pdf->setXY( 140, 230 );
#$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , '.........................................................' ) ); 
#$pdf->setXY( 163, 240 );
#$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'ผู้รับเงิน' ) ); 

}

$pdf->Output("MyPDF/MyPDF_sell.pdf","F");

}
echo 'PDF open Click <a href="MyPDF/MyPDF_sell.pdf">here</a> to Downloa';

?>

