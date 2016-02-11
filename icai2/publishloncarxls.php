<?php 
error_reporting(0);
 date_default_timezone_set("Asia/Kolkata"); 
include("core/function.php");
require_once 'PHPExcel/Classes/PHPExcel.php';
require_once 'PHPExcel/Classes//PHPExcel/Cell/AdvancedValueBinder.php';
if($_GET['log_file'])
			define("log_file", $_GET['log_file']);

			if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));


//$date=date("Y-m-d",time()-86400);
//$dateForExcel=date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))-86400);

$date=date;
$dateForExcel=date("Y-m-d H:i:s",strtotime(date));

$array=array();
$indexvalueArrayRes=mysql_query("SELECT indxx_value  FROM `tbl_indxx_value` WHERE `code` LIKE 'LCINDX' and date='".$date."' order by dateAdded desc limit 0,1");
$array=mysql_fetch_assoc($indexvalueArrayRes);


if(!empty($array) && $array['indxx_value']!=0.00)
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
            ->setCellValue('B1', 'Value')
            ->setCellValue('C1', 'Date');
	
$objPHPExcel->setActiveSheetIndex(0)

            ->setCellValue('A2', ".LCINDX")

			->setCellValue('B2',$array['indxx_value']) 
			
			->setCellValue('C2', $date);
			
//$objPHPExcel->getActiveSheet()->getStyle('C2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);


PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
$objPHPExcel->getActiveSheet()->setCellValue('C2', $dateForExcel);
$objPHPExcel->getActiveSheet()->getStyle('C2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
			
			$i++;

			

$objPHPExcel->getActiveSheet()->setTitle('values');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');


$objWriter->save("loncar/loncar-values-".$date.".xls");

	

}
else{
mail("dbajpai@indxx.com,jsharma@indxx.com","Loncar value not available", "Loncar value is not available for date : ".$date);
}

saveProcess(2);
//exit;
/*echo '<script>document.location.href="http://97.74.65.118/icai2/index.php?module=calcftpclose";</script>';
*/
$url="index.php?module=calcftpclose&date=" .date. "&log_file=" . basename(log_file);
	$link="<script type='text/javascript'>
window.open('".$url."');  
</script>";
echo $link;

?>