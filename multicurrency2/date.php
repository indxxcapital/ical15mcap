<?php 
if(!empty($_REQUEST)){
	$start_date=$_REQUEST['start_date'];
	$end_date=$_REQUEST['end_date'];
	
if(isset($_GET['count']))
$count=$_GET['count']+1;
else
$count=1;
if(isset($_GET['date']))
$date=$_GET['date'];
else
$date=date("Y-m-d",strtotime($start_date)-86400);
//date is first date 
$enddate =$end_date;
$link='';
if($count%3==1)
{
	if(date("D",strtotime($date))=="Fri")
	$date=date("Y-m-d",strtotime($date)+3*86400);
	else
	$date=date("Y-m-d",strtotime($date)+86400);
}else{

}
$text='';

if($count%3==1)
{
	$text='Currently Running Opening for date '.$date;
	
	$link = "<script>window.open('http://198.12.158.159/testing/ical1.4backup/icai2/index.php?module=openingtest&date=".$date."','_blank')</script>";
}if($count%3==2)
{	$text='Currently Running Closing for date '.$date;
	
	$link = "<script>window.open('http://198.12.158.159/testing/ical1.4backup/multicurrency2/read_input_files.php?date=".$date."','_blank')</script>";

}if($count%3==0)
{
	
	$text='Currently Running CA for date '.$date;
	$link = "<script>window.open('http://198.12.158.159/testing/ical1.4backup/multicurrency2/read_ca_files.php?date=".$date."','_blank')</script>";
}
if(strtotime($date)<strtotime($enddate))
{
	echo $link;
echo $text ."<br>Don't Close this Tab & Browser.";

	
	header('Refresh: 300; URL=date.php?count='.$count."&date=".$date."&start_date=".$start_date."&end_date=".$end_date);
}
}
?>
<br />
<br />
<br />
<br />
<br />
<br />
<form method="post">
 Start Date:
  <input type="date" name="start_date">
  End Date: <input type="date" name="end_date">
  <input type="submit">

</form>