<?php

class Checkpvchange extends Application{

	function __construct()
	{
	//	echo "deepak";
		parent::__construct();
	}
	
	
	
	function index()
	{
	//echo "SELECT distinct(ticker)  FROM tbl_indxx_ticker WHERE status='1' union SELECT distinct(ticker)  FROM tbl_indxx_ticker_temp WHERE status='1' ";	
					if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));

		 if($_GET['log_file'])
			define("log_file", $_GET['log_file']);
				$this->log_info(log_file, "in check Price value change");
				
		
		$liveindexes=$this->db->getResult("SELECT distinct(ticker)  FROM tbl_indxx_ticker WHERE status='1' union SELECT distinct(ticker)  FROM tbl_indxx_ticker_temp WHERE status='1' ",true);
//$this->pr($liveindexes,true);
	
	$indxxvaluesarray=array();
	
	if(!empty($liveindexes)){
	foreach($liveindexes as $key=>$value)
	{
		
		$indxxvaluesarray[$key]=$value;
		
		$liveindexvalues=$this->db->getResult("SELECT  date,price , isin,curr from tbl_prices_local_curr where ticker='".$value['ticker']."'order by date desc limit 0,2",true);
		$indxxvaluesarray[$key]['values']=$liveindexvalues;
		
	}}
		
		//$this->pr($indxxvaluesarray,true);
	$str='';	
		if(!empty($indxxvaluesarray))
		{
		foreach($indxxvaluesarray as $indxx)
		{//echo $indxx['values'];
			
		if(count($indxx['values'])==2 )
		{
		
		//$this->pr($indxx);
		$value1=$indxx['values'][0]['price'];
		$value2=$indxx['values'][1]['price'];
		$diff=100*(($value1-$value2)/$value2);
//echo $indxx['code']."=>".$diff;
//echo "<br>";
	
	if($diff>=5 || $diff<=-5)
	{
		
		
		
		$str1='';
		
	$str1= "<tr><td>".$indxx['ticker']."(".$indxx['values'][0]['isin'].") </td><td>".$indxx['values'][0]['curr']."</td><td>".$diff."% :</td> <td>";
		$this->log_info(log_file, "Ticker Price value change ".$indxx['ticker']."(".$indxx['values'][0]['isin'].") -".$indxx['values'][0]['curr']." " .$diff."%") ." ";
	
	
	$indxxes=$this->db->getResult("SELECT name,code FROM `tbl_indxx` where id in (select indxx_id from tbl_indxx_ticker where ticker='".$indxx['ticker']."')",true);
		foreach($indxxes as $index)
		{
		$str1.="".$index['name']."(".$index['code'].") ";
		}
	$str1.="</td></tr>";
	$str.=$str1;
	}
	if($diff==0)
	{$str2='';
		$str2.= "<tr><td>".$indxx['ticker']."(".$indxx['values'][0]['isin'].")</td><td>".$indxx['values'][0]['curr']."</td><td>" .$diff."% :</td><td> ";
		$indxxes=$this->db->getResult("SELECT name,code FROM `tbl_indxx` where id in (select indxx_id from tbl_indxx_ticker where ticker='".$indxx['ticker']."')",true);
		foreach($indxxes as $index)
		{
		$str2.="".$index['name']."(".$index['code'].")";
		}
	$str2.="</td></tr> ";
	$str.=$str2;
	$this->log_info(log_file, "Ticker Price value change ".$indxx['ticker']."(".$indxx['values'][0]['isin'].") -".$indxx['values'][0]['curr']." " .$diff."%");
	}
		
		}
		}
		}
	//	exit;
		if($str)
		{
			
			$emailQueries='select email from tbl_ca_user where status="1" and type!="1" ';
		$email_res=mysql_query($emailQueries);
		if(mysql_num_rows($email_res)>0)
		{
			
			while($email=mysql_fetch_assoc($email_res))
			{
			$emailsids[]=$email['email'];
			}
		}
			
			if(!empty($emailsids))	
		{
			 $emailsids	=implode(',',$emailsids);
			 $emailsids='sumitag@indxx.com';
			//$emailsids.=',dbajpai@indxx.com';
			
//			$msg='Hi <br>
	//		Local Price Change Notification <br/>
	//		'.$str." <br>Thanks <br>";
					
					
					// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			// Additional headers
			//$headers .= 'To: '.$dbuser['name'].' <'.$dbuser['email'].'>'. "\r\n";
$headers .= 'From: Indexing <indexing@indxx.com>' . "\r\n"."CC: indexing@indxx.com". "\r\n";
					//echo $emailsids;
					//echo $msg;
				//	exit;
								 $msg = '';
	$msg.="<table  border='1'><tr>
			<th>Ticker</th>	<th>Currency</th>
			<th>%change</th>
			<th>Index</th>
			</tr>";
	$msg.=$str."</table>";
		
				
						if(mail($emailsids,"Price Change Notification",$msg,$headers))
					{
						echo "Mail Send ";
						
						//echo "Mail sent to : ".$dbuser['name']."<br>";	
					}
					else
					{
						echo "Mail not sent";	
					}
					
			
			
		}
		}
		
		$this->saveProcess(2);
		//$this->update_process("Closing",date,"1");	
	//	$this->Redirect2("../multicurrency2/db_backup.php?date=" .date. "&log_file=" . basename(log_file),"","");	
	}

}?>