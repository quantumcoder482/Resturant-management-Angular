<?php

require_once('Data.php');
require('fpdf.php');
$service=new Data;
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$detail=$service->order_by_id();
define("ORDER_ID", $detail['id']);

$profile1=$service->profile_details();
$profile=json_decode($profile1);		

// Instanciation of inherited class
// $pdf = new PDF();
// virtual page


$pdf = new FPDF('P', 'mm', array(78,600));
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(false,0);
$pdf->AddPage();

	// header

	$object=new Data;
	$detail=$object->order_by_id(ORDER_ID);
	
	$profile1=$object->profile_details();
	$profile=json_decode($profile1);		
	
		//$pdf->Cell(10,1,'Page '.$pdf->PageNo().'/{nb}',0,0,'L');
		
	$pdf->SetFont('Arial','B',8);
	$pdf->SetX(10);
	$pdf->Cell(5,0,'GST Invoice',0,1);
	
	$pdf->SetFont('Arial','I',6);
	$pdf->SetX(35);
	$pdf->Cell(5,0,'(ORIGINAL FOR RECIPIENT)',0,1);
	
	$pdf->Line(3, 15, 75, 15);
	$pdf->Line(3, 15, 3, 60);
	$pdf->Line(75, 15, 75, 60);
	$pdf->Line(45, 15, 45, 60);
	$pdf->Line(3, 40, 45, 40);
	$pdf->Line(3, 60, 15, 60);
	$pdf->Line(40, 60, 75, 60);
	
	$pdf->SetX(3);
	
	$pdf->SetFont('Arial','B',8);
	// Title
	$pdf->Cell(0,20,$profile[0]->title,0,1);
	$pdf->SetX(3);
	$pdf->SetFont('Arial','I',6);
	$pdf->Cell(0,-12,'Panama Chowk, Gandhi Nagar',0,1);
	$pdf->Cell(0,18,'Jammu,Jammu & Kashmir-190001',0,1);
	$pdf->Cell(0,-12,'Contact:'.$profile[0]->contact,0,1);
	$pdf->Cell(0,18,'GSTIN: 22AHEPB2676G175',0,1);
	$pdf->Cell(0,-12,'E-mail:' .$profile[0]->email,0,1);
	
	$pdf->SetFont('Arial','B',8);
	$pdf->SetY(43);
	//$pdf->SetX(3);
	$pdf->Cell(0,0,'Buyer: '.$detail['contact_name'],0,1);
	
	$pdf->SetFont('Arial','I',6);
	$pdf->SetY(45);
	
	$pdf->Cell(0,3,'ADDRESS:'.$detail['contact_address'],0,1);
	$pdf->Cell(0,4,''.'MOBILE: '.$detail['contact_number'],0,1);
	
	
	
	
	$pdf->Line(45, 28, 75, 28);
	$pdf->Line(45, 38, 75, 38);
	$pdf->Line(45, 50, 75, 50);
	$pdf->Line(45, 60, 75, 60);
	
	$pdf->Line(60, 15, 60, 28);
	$pdf->Line(60, 38, 60, 50);
	
	$pdf->SetY(18);
	$pdf->Cell(48,0,'Invoice No.',0,1,'R');
	
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(42,10,''.$detail['order_num'],0,1,'R');
	
	$pdf->SetFont('Arial','I',6);
	$pdf->Cell(58,-20,'Dated',0,1,'R');
	
	$mydate=explode(" ",$detail['date']);
	$date1=date_create($mydate[0]);
	$date=date_format($date1,"d M Y");

	
	$pdf->SetFont('Arial','B',5);
	$pdf->Cell(63,30,''.$date,0,1,'R');
	
	$pdf->SetFont('Arial','I',6);
	$pdf->Cell(60,-15,'Mode/Terms of Payment',0,1,'R');
	
	$pdf->Cell(50,35,'Suppliers Ref',0,1,'R');
	$pdf->Cell(61,-35,'Other',0,1,'R');
	$pdf->Cell(55,60,'Terms of Delivery',0,1,'R');
	
	
$pdf->SetFont('Arial','I',6);
//$pdf->Setwidth(75);
$pdf->Sety(55);

$htmlTable='<table style="width:400px !important;">
<tr>
<td width="1">S.No.</td>
<td width="8">Description of Item</td>
<td width="8">GST</td>
<td width="8">Qty</td>
<td width="8">Price</td>
<td width="8">Total</td>
</tr>';

$no=1;
$i=0;
$g_total=0;
$ac=0;
$tmrp=0.0;    
      
    $items=$detail['item_names'];
    $product_names=explode(',%,%',$items,-1);
		
	$price=$detail['prices'];
	$prices=explode(',',$price,-1);
	
	$quantity=$detail['quantity'];
    $quantity1=explode(',',$quantity,-1);
	   
    $cgst1=$detail['cgst'];
    $cgst=explode(',',$cgst1,-1);
	    
	$sgst1=$detail['sgst'];
    $sgst=explode(',',$sgst1,-1);

	if(!empty($product_names)){
	    foreach($product_names as $row){ 
			$item_gst=$cgst[$i]+$sgst[$i];
	 
			$htmlTable.='
			<tr>
			<td width="1">'.$no.'</td>
			<td width="8">'.$row .'</td>
			<td width="8">'.$item_gst .'%'.'</td>
			<td width="8">'.$quantity1[$i].'</td>
			<td width="8">'.$prices[$i].'</td>
			<td width="8">'.$quantity1[$i] * $prices[$i].'</td>
			</tr>';

			$ac=$quantity1[$i]*$prices[$i];
			$g_total = $g_total + $ac;
			$no++;
			$i++;
		}
	} 
 

$htmlTable.='<tr>

	<td width="1"></td>
	<td width="8" bgcolor="#D0D0FF">Sub Total</td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8">'.$detail['bill_amount'].'</td>
	</tr>';

if($detail['discount']){

$htmlTable.='<tr>

	<td width="1"></td>
	<td width="8">Discount</td>

	<td width="8"></td>
	<td width="8"></td>
	<td width="8"> </td>
	<td width="8">'.$detail['discount'].'</td>
	</tr>';
	}

$htmlTable.='<tr>

	<td width="1"></td>
	<td width="8">CGST</td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8">'.$detail['order_cgst'].'</td>
	</tr>';

$htmlTable.='<tr>

	<td width="8"></td>
	<td width="8">SGST</td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8">'.$detail['order_sgst'].'</td>
	</tr>';

$htmlTable.='<tr>

	<td width="1"></td>
	<td width="8">Total(Roundoff)</td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8">'.round($detail['payable_amount']).'</td>
	</tr>';

$htmlTable.='</table>';
$pdf->WriteHTML2("$htmlTable");
	
	$y=$pdf->GetY();
    $height=$y+40;

$pdf->Close();
/*  Create new document  */


$pdf_new = new FPDF('P', 'mm', array(78,$height));
$pdf_new->AliasNbPages();
$pdf_new->SetAutoPageBreak(false,0);
$pdf_new->AddPage();


	// header

	$object=new Data;
	$detail=$object->order_by_id(ORDER_ID);
	
	$profile1=$object->profile_details();
	$profile=json_decode($profile1);		
	
		//$pdf_new->Cell(10,1,'Page '.$pdf_new->PageNo().'/{nb}',0,0,'L');
		
	$pdf_new->SetFont('Arial','B',8);
	$pdf_new->SetX(10);
	$pdf_new->Cell(5,0,'GST Invoice',0,1);
	
	$pdf_new->SetFont('Arial','I',6);
	$pdf_new->SetX(35);
	$pdf_new->Cell(5,0,'(ORIGINAL FOR RECIPIENT)',0,1);
	
	$pdf_new->Line(3, 15, 75, 15);
	$pdf_new->Line(3, 15, 3, 60);
	$pdf_new->Line(75, 15, 75, 60);
	$pdf_new->Line(45, 15, 45, 60);
	$pdf_new->Line(3, 40, 45, 40);
	$pdf_new->Line(3, 60, 15, 60);
	$pdf_new->Line(40, 60, 75, 60);
	
	$pdf_new->SetX(3);
	
	$pdf_new->SetFont('Arial','B',8);
	// Title
	$pdf_new->Cell(0,20,$profile[0]->title,0,1);
	$pdf_new->SetX(3);
	$pdf_new->SetFont('Arial','I',6);
	$pdf_new->Cell(0,-12,'Panama Chowk, Gandhi Nagar',0,1);
	$pdf_new->Cell(0,18,'Jammu,Jammu & Kashmir-190001',0,1);
	$pdf_new->Cell(0,-12,'Contact:'.$profile[0]->contact,0,1);
	$pdf_new->Cell(0,18,'GSTIN: 22AHEPB2676G175',0,1);
	$pdf_new->Cell(0,-12,'E-mail:' .$profile[0]->email,0,1);
	
	$pdf_new->SetFont('Arial','B',8);
	$pdf_new->SetY(43);
	//$pdf_new->SetX(3);
	$pdf_new->Cell(0,0,'Buyer: '.$detail['contact_name'],0,1);
	
	$pdf_new->SetFont('Arial','I',6);
	$pdf_new->SetY(45);
	
	$pdf_new->Cell(0,3,'ADDRESS:'.$detail['contact_address'],0,1);
	$pdf_new->Cell(0,4,''.'MOBILE: '.$detail['contact_number'],0,1);
	
	
	
	
	$pdf_new->Line(45, 28, 75, 28);
	$pdf_new->Line(45, 38, 75, 38);
	$pdf_new->Line(45, 50, 75, 50);
	$pdf_new->Line(45, 60, 75, 60);
	
	$pdf_new->Line(60, 15, 60, 28);
	$pdf_new->Line(60, 38, 60, 50);
	
	$pdf_new->SetY(18);
	$pdf_new->Cell(48,0,'Invoice No.',0,1,'R');
	
	$pdf_new->SetFont('Arial','B',8);
	$pdf_new->Cell(42,10,''.$detail['order_num'],0,1,'R');
	
	$pdf_new->SetFont('Arial','I',6);
	$pdf_new->Cell(58,-20,'Dated',0,1,'R');
	
	$mydate=explode(" ",$detail['date']);
	$date1=date_create($mydate[0]);
	$date=date_format($date1,"d M Y");

	
	$pdf_new->SetFont('Arial','B',5);
	$pdf_new->Cell(63,30,''.$date,0,1,'R');
	
	$pdf_new->SetFont('Arial','I',6);
	$pdf_new->Cell(60,-15,'Mode/Terms of Payment',0,1,'R');
	
	$pdf_new->Cell(50,35,'Suppliers Ref',0,1,'R');
	$pdf_new->Cell(61,-35,'Other',0,1,'R');
	$pdf_new->Cell(55,60,'Terms of Delivery',0,1,'R');
	
	
$pdf_new->SetFont('Arial','I',6);
//$pdf_new->Setwidth(75);
$pdf_new->Sety(55);

$htmlTable='<table style="width:400px !important;">
<tr>
<td width="1">S.No.</td>
<td width="8">Description of Item</td>
<td width="8">GST</td>
<td width="8">Qty</td>
<td width="8">Price</td>
<td width="8">Total</td>
</tr>';

$no=1;
$i=0;
$g_total=0;
$ac=0;
$tmrp=0.0;    
      
    $items=$detail['item_names'];
    $product_names=explode(',%,%',$items,-1);
		
	$price=$detail['prices'];
	$prices=explode(',',$price,-1);
	
	$quantity=$detail['quantity'];
    $quantity1=explode(',',$quantity,-1);
	   
    $cgst1=$detail['cgst'];
    $cgst=explode(',',$cgst1,-1);
	    
	$sgst1=$detail['sgst'];
    $sgst=explode(',',$sgst1,-1);

	if(!empty($product_names)){
	    foreach($product_names as $row){ 
			$item_gst=$cgst[$i]+$sgst[$i];
	 
			$htmlTable.='
			<tr>
			<td width="1">'.$no.'</td>
			<td width="8">'.$row .'</td>
			<td width="8">'.$item_gst .'%'.'</td>
			<td width="8">'.$quantity1[$i].'</td>
			<td width="8">'.$prices[$i].'</td>
			<td width="8">'.$quantity1[$i] * $prices[$i].'</td>
			</tr>';

			$ac=$quantity1[$i]*$prices[$i];
			$g_total = $g_total + $ac;
			$no++;
			$i++;
		}
	} 
 

$htmlTable.='<tr>

	<td width="1"></td>
	<td width="8" bgcolor="#D0D0FF">Sub Total</td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8">'.$detail['bill_amount'].'</td>
	</tr>';

if($detail['discount']){

$htmlTable.='<tr>

	<td width="1"></td>
	<td width="8">Discount</td>

	<td width="8"></td>
	<td width="8"></td>
	<td width="8"> </td>
	<td width="8">'.$detail['discount'].'</td>
	</tr>';
	}

$htmlTable.='<tr>

	<td width="1"></td>
	<td width="8">CGST</td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8">'.$detail['order_cgst'].'</td>
	</tr>';

$htmlTable.='<tr>

	<td width="8"></td>
	<td width="8">SGST</td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8">'.$detail['order_sgst'].'</td>
	</tr>';

$htmlTable.='<tr>

	<td width="1"></td>
	<td width="8">Total(Roundoff)</td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8"></td>
	<td width="8">'.round($detail['payable_amount']).'</td>
	</tr>';

$htmlTable.='</table>';
$pdf_new->WriteHTML2("$htmlTable");

// footer part
	$pdf_new->SetX(3);
    $pdf_new->Cell(0,3,'Declaration ',0,0,'L');
	$pdf_new->SetX(3);
	$pdf_new->Cell(0,20,'We declare that this invoice shows the actual price of the goods',0,0,'L');
    $pdf_new->SetX(3);
	$pdf_new->Cell(100,30,'described and that all particulars are true and correct. ',0,0,'L');
	$pdf_new->SetX(3);
	$pdf_new->Cell(0,10,'for Dominee ',0,0,'R');
	$pdf_new->SetFont('Arial','I',6);

	$pdf_new->SetX(3);
    //  $pdf_new->Cell(0,37,'Authorised Signatory ',0,0,'R');
	$pdf_new->Cell(0,37,'Authorised Signatory ',0,0,'R');
    // $pdf_new->SetY(-15);
	 // Arial italic 8
    $pdf_new->SetFont('Arial','I',6);
    // Page number
    $pdf_new->SetX(3);
	$pdf_new->Cell(0,45,'SUBJECT TO JAMMU JURISDICTION ',0,0,'C');	 
	$pdf_new->SetX(3);
	$pdf_new->Cell(0,55,'This is a Computer Generated Invoice. ',0,0,'L');
	
	

	//$pdf_new->Cell(0,10,'Like karyanawala on facebook for daily offers ',0,0,'R');
	//$pdf_new->Line(10, 250, 200, 250);
	//$pdf_new->Line(10, 275, 200, 275);
	//  $pdf_new->SetY(-45);
	 
	 

//$pdf_new->Output();

$filename="../invoice/".$detail['id'].".pdf";
$pdf_new->Output($filename,'F');

	
?>