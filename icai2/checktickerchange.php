<pre><?php
// date_default_timezone_set("Asia/Kolkata"); 
ini_set('max_execution_time',60*60);
error_reporting(0);
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
include("core/function.php");
if($_GET['log_file'])
define("log_file",get_logs_folder().$_GET['log_file']);
if($_GET['date'])
define("date",$_GET['date']);
else
define("date",date("Y-m-d"));


log_info("In Check Ticker Change ");
	
$cas=	selectrow(array('company_name','action_id','identifier','eff_date'),'tbl_ca' ,array('mnemonic'=>'CHG_TKR'));
	// code to match securty currency and bloomberg return currency
	$emailsids=array();
	if(!empty($cas))
	{
	foreach ($cas as $key=>$ca)
	{
		if(strtotime($ca['eff_date'])>=strtotime(date("Y-m-d")))
	{
		$text='<br>Ticker for corporate action with identifier : '.$ca['identifier'].' , companyname : '.$ca['company_name'].' and action_id : '.$ca['action_id'].' will be changed on '.date("d F, Y",strtotime($ca['eff_date'])).'.<br>';
		log_info("Ticker Change Found for  ".$ca['identifier']);
		$emailQueries='select email from tbl_ca_user where status="1" union select email from tbl_database_users where status="1"';
		$email_res=mysql_query($emailQueries);
		if(mysql_num_rows($email_res)>0)
		{
			
			while($email=mysql_fetch_assoc($email_res))
			{
				array_push($emailsids,$email['email']);
				
			//$emailsids[]=$email['email'];
			}
		}
		
//		print_r($emailsids);
	//	exit;
		if(!empty($emailsids))	
		{
			 $emailsids	=implode(',',$emailsids);
			 
			//$emailsids.=',dbajpai@indxx.com';
			
			$msg='Hi <br>'.$text." <br>Thanks <br>";
					
					// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			// Additional headers
			//$headers .= 'To: '.$dbuser['name'].' <'.$dbuser['email'].'>'. "\r\n";
$headers .= 'From: Indexing <indexing@indxx.com>' . "\r\n"."CC: indexing@indxx.com". "\r\n";					
						if(mail($emailsids,"Ticker Change Notification",$msg,$headers))
					{
						echo "Mail Send ";
						log_info("Mail Send to ".$emailsids);
						//echo "Mail sent to : ".$dbuser['name']."<br>";	
					}
					else
					{
						echo "Mail not sent";	
					}
					
			
		}
		}
	
	}
}







$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);

echo 'Page generated in '.$total_time.' seconds. ';
saveProcess();
mysql_close();
echo '<script>document.location.href="notificationss.php?log_file='.basename(log_file).'&date='.date.'";</script>';
//http://97.74.65.118/icai/dbbackup.php

?>

