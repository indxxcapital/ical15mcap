<?php 

error_reporting(0);
include("core/function.php");
require_once 'PHPExcel/Classes/PHPExcel.php';
require_once 'PHPExcel/Classes//PHPExcel/Cell/AdvancedValueBinder.php';

if($_GET['log_file'])
			define("log_file", $_GET['log_file']);

			if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));

			
$query="Select distinct (client_id) from tbl_indxx_cs where status='1' ";
$date=date;
//$date=date('Y-m-d');
$dateForExcel=date("Y-m-d H:i:s",strtotime(date));


$array=array();
$res1=mysql_query($query);
if(mysql_num_rows($res1)>0)
{
while($client=mysql_fetch_assoc($res1))
{	
$res3=mysql_query("Select ftpusername from tbl_ca_client where id='".$client['client_id']."'");
$clientname=mysql_fetch_assoc($res3);
$array[$client['client_id']]['name']=$clientname['ftpusername'];

$indexQuery='select id,code from tbl_indxx_cs where client_id="'.$client['client_id'].'"';
$res2=mysql_query($indexQuery);

if(mysql_num_rows($res2)>0)
{
while($indxx=mysql_fetch_assoc($res2))
{
$array[$client['client_id']][$indxx['id']]['code']=$indxx['code'];
//$array[$client['client_id']]['code']=$indxx['code'];
echo "select indxx_value from tbl_indxx_cs_value where indxx_id='".$indxx['id']."' and code ='".$indxx['code']."' and date='".$date."'";
$res4=mysql_query("select indxx_value from tbl_indxx_cs_value where indxx_id='".$indxx['id']."' and code ='".$indxx['code']."' and date='".$date."'");

if(mysql_num_rows($res4))
{
$value=mysql_fetch_assoc($res4);
if(!empty($value))
{
$array[$client['client_id']][$indxx['id']]['value']=$value['indxx_value'];
}


}



}

}

}
}

//print_r($array);
//exit;
if(!empty($array))
{
foreach($array as $client_id=>$client)
{


$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Deepak bajpai")
							 ->setLastModifiedBy("Deepak bajpai")
							 ->setTitle("PHPExcel Test Document")
							 ->setSubject("PHPExcel Test Document")
							 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
							 ->setKeywords("office PHPExcel php")
							 ->setCategory("Test result file");
$objPHPExcel->setActiveSheetIndex(0)

            ->setCellValue('A1', 'Index')
            ->setCellValue('B1', 'Value');
			
			
if($client_id=='3')
{
		$objPHPExcel->setActiveSheetIndex(0)
		
			->setCellValue('C1','Date') ;
}
			
	$i=2;		
foreach($client as $indxx_id=>$index)
{
if(!empty($index) && $index['value'] && is_numeric($index['value']))
{
$objPHPExcel->setActiveSheetIndex(0)

            ->setCellValue('A'.$i, ".".$index['code'])

			->setCellValue('B'.$i,$index['value']) ;

			
if($client_id=='3')
{
		//$objPHPExcel->setActiveSheetIndex(0)
		
			//->setCellValue('C'.$i,$date);
			
		//$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
		
		PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $dateForExcel);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
		
}

			$i++;

}
}			

$objPHPExcel->getActiveSheet()->setTitle('values');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

if($client['name'])
$objWriter->save("../files/ca-output/".$client['name']."/".$client['name']."-values-".$date.".xls");
else
{
$objWriter->save("../files/ca-output/values-".$date."-".time().".xls");

}


}	

}
else{
mail("dbajpai@indxx.com","pja value not available", "pja wdaaa not available");
}
saveProcess(2);
//exit;
/*echo '<script>document.location.href="http://97.74.65.118/icai2/index.php?module=calcftpclose";</script>';
*/
$url="publishloncarxls.php?date=" .date. "&log_file=" . basename(log_file);
	$link="<script type='text/javascript'>
window.open('".$url."');  
</script>";
echo $link;

?>