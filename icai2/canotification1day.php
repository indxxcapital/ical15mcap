<pre><?php
 date_default_timezone_set("Asia/Kolkata"); 
ini_set('max_execution_time',60*60);
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

include("core/function.php");

$datevalue=date("Y-m-d");

$txt='';
$ca_query=mysql_query("select identifier,action_id,id,mnemonic,field_id,company_name,ann_date,eff_date,amd_date,currency from tbl_ca cat where  ( eff_date between '".date("Y-m-d")."' and  '".$datevalue."' )    and status='1' and action_id not in (select ca_action_id from tbl_ca_admin_approve where ca_action_id=cat.action_id)  and action_id not in (select ca_action_id from tbl_ca_admin_approve_temp where ca_action_id=cat.action_id)");
if(mysql_num_rows($ca_query)>0)
{

$txt.='Hi , Following Corporate Action having Exdate '.$datevalue.'  is not approved yet <br> ';
while($row=mysql_fetch_assoc($ca_query))
{
$adminmsg.="".$row['company_name'].'(<strong>'.$row['identifier'].'</strong>): "'.$row['mnemonic'].'" <br> ';



//print_r($row);
}




}
if($adminmsg)
{
	
	$sub='Corporate Action Approval Pending';
	$mailArray=array();
	
	$query1='Select email  from tbl_ca_user where status = "1"';
$result1=mysql_query($query1);
if(mysql_num_rows($result1)>0)
{
while($row=mysql_fetch_assoc($result1))
{

$mailArray[]=$row['email'];
}
}


$query2='Select email  from tbl_database_users where status = "1"';
$result2=mysql_query($query2);
if(mysql_num_rows($result2)>0)
{
while($row=mysql_fetch_assoc($result2))
{
$mailArray[]=$row['email'];
}
}

$mailArray[]='nkumar@indxx.com';

$mailArray=array_unique($mailArray);
$emails= implode("," , $mailArray);


$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'To: '.$emails.''. "\r\n";
$headers .= 'From: Indexing <indexing@indxx.com>' . "\r\n";
		
		
		if(mail($emails,$sub,$adminmsg,$headers))
		{
			echo "Mail sent to ".$emails."<br>";	
		}

}

//echo $adminmsg


echo '<script>document.location.href="http://192.169.255.12/icai2/canotification2day.php";</script>';
?>