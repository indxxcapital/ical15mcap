<pre><?php
//echo date("Ymd",strtotime($date));
 date_default_timezone_set("Asia/Kolkata"); 
ini_set('max_execution_time',60*60);
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

include("core/function.php");
//delete_old_ca();

$date=date("Y-m-d",strtotime(date("Y-m-d"))-86400);
//echo $date ;
//exit;
$handle = @fopen("../files/ca-input/ca_test.csv.".date("Ymd",strtotime($date)), "r");
$i=0;

$skipped=0;
$inserted=0;
$updated=0;
$empty=0;

$query='';
if ($handle) {
	
	delete_plain_ca();
	
	
   while (!feof($handle)) {
//     echo  ($i++)."=>".fgets($handle, 4096);
   
 //echo "<br>";
   $str= fgets($handle, 4096);
   $i++;

   if($i>19 )
  {
	// echo $str;
$security=explode("|",$str);
  	
	if(count($security)>5)		
	{
		if (strstr(json_encode($security), "CP_DELETE_REASON")) 
		{
			//echo "CP_DELETE_REASON<br>";
		}
		else{
		//echo "Insert into tbl_ca_plain_txt (value) values ('".mysql_real_escape_string(json_encode($security))."');";
		$query="Insert into tbl_ca_plain_txt (value) values ('".mysql_real_escape_string(json_encode($security))."');";
		   mysql_query($query);
		   }
		
		
	}
  } 
   
   }

   
   fclose($handle);
   

}

else{
echo "Error File not exist";
mail("dbajpai@indxx.com","File Read Error!","corporate actions file not available for today . ");
exit;
}

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);

   saveProcess();
mysql_close();
echo 'Page generated in '.$total_time.' seconds. ';
	echo '<script>document.location.href="http://192.169.255.12/icai2/process_ca.php";</script>';



?>