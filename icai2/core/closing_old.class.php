<?php 
//// to add the Static Function 
class Closing extends Functions{	
var $db;
function Closing($obj,$db_obj){
$this->Obj=$obj;
$this->db=$db_obj;
}
function preclosing(){


if($_GET['log_file'])
			define("log_file", $_GET['log_file']);
							if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));

		 $datevalue=date;
if(date("D",strtotime($datevalue))=="Fri")
 $livedate=date("Y-m-d",strtotime($datevalue)+86400*3);
else
 $livedate=date("Y-m-d",strtotime($datevalue)+86400);

//echo "select tbl_indxx_temp.* from tbl_indxx_temp  where status='1' and usersignoff='1' and dbusersignoff='1' and submitted='1' and calcdate='".$datevalue."' or dateStart='".$livedate."'";
$indxxs=$this->db->getResult("select tbl_indxx_temp.* from tbl_indxx_temp  where status='1' and usersignoff='1' and dbusersignoff='1' and submitted='1' and calcdate='".$datevalue."' or dateStart='".$livedate."'",true);	


$final_array=array();
		
		if(!empty($indxxs))
		{
			foreach($indxxs as $row)
			{
	//$this->pr($indxx);
					
			$final_array[$row['id']]=$row;
			

			
			
			/*$indxx_value=$this->db->getResult("select tbl_indxx_value_temp.* from tbl_indxx_value_temp where indxx_id='".$row['id']."' and  code='".$row['code']."' order by date desc ",false,1);	
		//	$this->pr($indxx_value,true);
			if(!empty($indxx_value))
			{
			$final_array[$row['id']]['index_value']=$indxx_value;
			}
			else{*/
			if($row['recalc'])
			{
			$indxx_value=$this->db->getResult("select tbl_indxx_value.* from tbl_indxx_value where code='".$row['code']."' order by date desc ",false,1);	
		//	$this->pr($indxx_value,true);
			if(!empty($indxx_value))
			{
			$final_array[$row['id']]['index_value']=$indxx_value;
			}	
			}
			else
			{
			$final_array[$row['id']]['index_value']['market_value']=$row['investmentammount'];
			$final_array[$row['id']]['index_value']['divpvalue']=$row['divpvalue'];
			$final_array[$row['id']]['index_value']['olddivisor']=$row['divisor'];
			$final_array[$row['id']]['index_value']['newdivisor']=$row['divisor'];
			$final_array[$row['id']]['index_value']['indxx_value']=$row['indexvalue'];
			if($final_array[$row['id']]['index_value']['olddivisor']==0){
			$final_array[$row['id']]['index_value']['olddivisor']=$row['investmentammount']/$row['indexvalue'];
			}
			if($final_array[$row['id']]['index_value']['newdivisor']==0){
			$final_array[$row['id']]['index_value']['newdivisor']=$row['investmentammount']/$row['indexvalue'];
			}}


			//}
			//$this->pr(	$final_array,true);
			
			
			// $query="SELECT  it.name,it.isin,it.ticker,(select price from tbl_final_price_temp fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$row['id']."') as calcprice,(select localprice from tbl_final_price_temp fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$row['id']."') as localprice,(select currencyfactor from tbl_final_price_temp fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$row['id']."') as currencyfactor,(select share from tbl_share_temp sh where sh.isin=it.isin  and sh.indxx_id='".$row['id']."') as calcshare FROM `tbl_indxx_ticker_temp` it where it.indxx_id='".$row['id']."'";			
		
	//	exit;
	
	$query = "SELECT  it.id, it.name, it.isin, it.ticker, it.curr, it.sedol, it.cusip,it.weight, it.countryname, 
							fp.localprice, fp.currencyfactor, fp.price as calcprice
							FROM `tbl_indxx_ticker_temp` it left join tbl_final_price_temp fp on fp.isin=it.isin 
							 where it.indxx_id='" . $row['id'] . "' 
							and fp.indxx_id='" . $row['id'] . "'  and fp.date='" . $datevalue . "'";			
		
		
			$indxxprices=	$this->db->getResult($query,true);	
		
		if(!empty($indxxprices))
		{
		foreach($indxxprices as $key=>$ticker)
		{
			//echo "select share from tbl_share_temp where indxx_id='".$row['id']."' and isin='".$ticker['isin']."' limit 0,1";
		$share=$this->db->getResult("select share from tbl_share_temp where indxx_id='".$row['id']."' and isin='".$ticker['isin']."' limit 0,1",false);
		
		if(!empty($share))
		{
		$indxxprices[$key]['calcshare']=$share['share'];
		}else{
		$indxxprices[$key]['calcshare']=0;
		}
		}
		}
		$final_array[$row['id']]['values']=$indxxprices;
		
		
	//	$this->pr($indxxprices,true);	
			
			
			}	
		
		}

//$this->pr($final_array,true);

if(!empty($final_array))
	{
		foreach($final_array as $indxxKey=> $closeIndxx)
		{
			
			$file="../files/ca-output_upcomming/pre-closing-".$closeIndxx['code']."-".$closeIndxx['dateStart']."-".$datevalue.".txt";

			$open=fopen($file,"w+");

			$entry1='Date'.",";
			$entry1.=$datevalue.",\n";
			$entry1.='Index Value'.",";
			$entry3='Effective Date'.",";
			$entry3.='TICKER'.",";
			$entry3.='Name'.",";
			$entry3.='ISIN'.",";
			$entry3.='SEDOL'.",";
			$entry3.='CUSIP'.",";
			$entry3.='Country'.",";
			$entry3.='Index Shares'.",";
			$entry3.='Weight'.",";
			$entry3.='Price'.",";
			$entry3.='Currency'.",";
			$entry3.='Currency Factor'.",";
			$entry4='';
			
			
			//$this->pr($closeIndxx);
			$oldindexvalue=$closeIndxx['index_value']['indxx_value'];
			$newindexvalue=0;
			$oldDivisor=$closeIndxx['index_value']['newdivisor'];
			$newDivisor=$oldDivisor;
			$marketValue=0;
			$sumofDividendes=0;
			$shareinsertArray=array();
			foreach($closeIndxx['values'] as $TickerKey=> $closeprices)
			{
			//$this->pr($closeprices,true);
		if(!$closeprices['calcshare'] && !$closeprices['weight'])
		{//echo "Share and weight not available for ".$closeprices['ticker']."=>".$closeprices['name'];
		//exit;
		}
		$shareValue=0;
		$weightValue=0;
		if($closeprices['calcshare'])
			$shareValue=$closeprices['calcshare'];	
		else
		{	$shareValue=($closeIndxx['index_value']['market_value']*$closeprices['weight'])/($closeprices['calcprice']*100);	
		$shareinsertArray[]='("'.$closeprices['isin'].'","'.$closeIndxx['id'].'","'.$shareValue.'")';
		//echo $shareValue;
		$closeprices['calcshare']=$shareValue;
		}
		$closeIndxx['values'][$TickerKey]['calcshare']=$shareValue;
		
			$securityPrice=$closeprices['calcprice'];
			
		
		if($closeprices['weight'])
		$weightValue=$closeprices['weight'];
		else
		$weightValue=(($closeprices['calcprice']*$shareValue)/$closeIndxx['index_value']['market_value']);
		
			// $weightValue."<br>";
			//echo $shareValue."<br>";
			//exit;
			if(!$securityPrice){
			//echo "Price Not Found For ".$closeprices['ticker']."=>".$closeprices['name'];
			//exit;
			}
			/*if(!$shareValue)
			{
			echo "Share Not Found For ".$closeprices['ticker']."=>".$closeprices['name'];
			exit;
			}*/
			
			
		 	$marketValue+=number_format($closeprices['calcshare']*$closeprices['calcprice'],11,'.','');	
		//	$sumofDividendes+=$shareValue*$dividendPrice;	
			//echo "<br>";
			
			

			}
		
 $marketValue= number_format($marketValue,11,'.','');	
	//exit;
//echo $closeIndxx['id']."<br>";
		
		//$newDivisor=number_format($oldDivisor-($sumofDividendes/$oldindexvalue),4,'.','');
		if($closeIndxx['index_value']['divpvalue'])
		{
		$marketValue+=$closeIndxx['index_value']['divpvalue'];
		}
	//echo $marketValue;
//exit;	
	//echo "<br>";
		
		
		
	//	$this->pr($closeIndxx['values']);
		
		 foreach($closeIndxx['values'] as $closeprices)
		{
			$weightValue=(($closeprices['calcprice']*$closeprices['calcshare'])/$marketValue);
			
		$entry4.= "\n".$datevalue.",";
            $entry4.=  $closeprices['ticker'].",";
            $entry4.= $closeprices['name'].",";
            $entry4.=$closeprices['isin'].",";
			 $entry4.=$closeprices['sedol'].",";;
            $entry4.=$closeprices['cusip'].",";;
            $entry4.=$closeprices['countryname'].",";
            $entry4.=$closeprices['calcshare'].",";
			$entry4.=$weightValue.",";
       		$entry4.=$closeprices['localprice'].",";
	     	$entry4.=$closeprices['curr'].",";
	     	$entry4.=$closeprices['currencyfactor'].",";
			
		} 
		
		
		
		
		$newDivisor=$marketValue/$oldindexvalue;
	//	echo "<br>";
		$oldDivisor=$newDivisor;
		//echo $marketValue;
		 $newindexvalue=number_format(($marketValue/$newDivisor),4,'.','');
		$entry2=$newindexvalue.",\n";
			$entry2.="Divisor,".$newDivisor.",\n";
		$entry2.="Market Value,".$marketValue.",\n\n";
		//exit;
		if($newindexvalue  && $newindexvalue!='0.0000')
		{
			//echo $newindexvalue;
			//exit;
		
		//exit;
		
		if(!empty($shareinsertArray)){
	
		$this->db->query("insert into tbl_share_temp (isin,indxx_id,share) values ".implode(",",$shareinsertArray).";");
		}

		
	 $insertQuery='INSERT into tbl_indxx_value_temp (indxx_id,code,market_value,indxx_value,date,olddivisor,newdivisor) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.$marketValue.'","'.$newindexvalue.'","'.$datevalue.'","'.$oldDivisor.'","'.$newDivisor.'")';
		$this->db->query($insertQuery);	
		$insertQuery='INSERT into tbl_indxx_value_open_temp (indxx_id,code,market_value,indxx_value,date,olddivisor,newdivisor) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.$marketValue.'","'.$newindexvalue.'","'.$datevalue.'","'.$oldDivisor.'","'.$newDivisor.'")';
		$this->db->query($insertQuery);	
		
		if($open){   
 if(   fwrite($open,$entry1.$entry2.$entry3.$entry4))
{
	$query=$this->db->Query("update tbl_indxx_temp set runindex='1',finalsignoff='1' where tbl_indxx_temp.id='".$indxxKey."'");
	
	        fclose($open);

 $filetext= "file Written for ".$closeIndxx['code']."<br>";

}
}  
}else{
mail(($_SESSION['User']['email'] ?$_SESSION['User']['email'].",ical@indxx.com" : 'ical@indxx.com'),"Run Index not  done","Runindex not done for ".$closeIndxx['code']);

}
		
		unset($final_array[$indxxKey]);
		}
	}

}
	function closingLive()
	{		
		
		//$this->pr($_SESSION);
		
		//$this->_baseTemplate="main-template";
		//$this->_bodyTemplate="404";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		
		$type="close";
		
		//$datevalue=$this->_date;
		
		//$datevalue=date("Y-m-d",strtotime($datevalue)-86400);
		if($_GET['log_file'])
			define("log_file", $_GET['log_file']);
							if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));

		$datevalue=date;
		
				$this->log_info(log_file, "Closing file generation process started for live indexes.");
		if($_GET['id'])
		{
			$page=$_GET['id'];	
		}
		else
		{
			$page=0;	
		}
		
		$limit=5;
		//echo "select tbl_indxx.* from tbl_indxx  where status='1' and usersignoff='1' and dbusersignoff='1' and submitted='1' limit $page,3";
		//exit;
		$indxxs=mysql_query("select tbl_indxx.* from tbl_indxx  where status='1' and usersignoff='1' and dbusersignoff='1' and submitted='1' ");
		
		if ($err_code = mysql_errno())
		{
			$this->log_error(log_file, "Unable to read live indexes. MYSQL error code " . $err_code .
					". Exiting closing file process.");
			$this->mail_exit(log_file, __FILE__, __LINE__);
		}
		
		$final_array=array();
		
			while(false != ($row = mysql_fetch_assoc($indxxs)))
		{
	//$this->pr($indxxs,true);
					
					
					$this->log_info(log_file, "Processing closing data file for index = " . $row['code']);
		//if($row['id']==31)
		//{
			
				$final_array[$row['id']]=$row;
			
			
			
			
			
			
			
			$client=$this->db->getResult("select tbl_ca_client.ftpusername from tbl_ca_client where id='".$row['client_id']."'",false,1);	
		//	
		$final_array[$row['id']]['client']=$client['ftpusername'];
			
			$this->log_info(log_file, "FTP folder of " . $row['code'] ." is :".$client['ftpusername'] );
			
			$indxx_value=$this->db->getResult("select tbl_indxx_value_open.* from tbl_indxx_value_open where indxx_id='".$row['id']."' order by date desc ",false,1);	
		//	$this->pr($indxx_value,true);
			if(!empty($indxx_value))
			{
			$final_array[$row['id']]['index_value']=$indxx_value;
			}
			else{
			$final_array[$row['id']]['index_value']['market_value']=$row['investmentammount'];
			$final_array[$row['id']]['index_value']['olddivisor']=$row['divisor'];
			$final_array[$row['id']]['index_value']['newdivisor']=$row['divisor'];
			$final_array[$row['id']]['index_value']['indxx_value']=$row['indexvalue'];
			if($final_array[$row['id']]['index_value']['olddivisor']==0){
			$final_array[$row['id']]['index_value']['olddivisor']=$row['investmentammount']/$row['indexvalue'];
			}
			if($final_array[$row['id']]['index_value']['newdivisor']==0){
			$final_array[$row['id']]['index_value']['newdivisor']=$row['investmentammount']/$row['indexvalue'];
			}


			}
			//$this->pr(	$final_array,true);
			
			//$indxx_value=$this->db->getResult("select tbl_indxx_value_open.* from tbl_indxx_value_open where indxx_id='".$row['id']."' order by date desc ",false,1);	
			
			
			$query = "SELECT  it.id, it.name, it.isin, it.ticker, it.curr, it.sedol, it.cusip, it.countryname, 
							fp.localprice, fp.currencyfactor, fp.price as calcprice, sh.share as calcshare 
							FROM `tbl_indxx_ticker` it left join tbl_final_price fp on fp.isin=it.isin 
							left join tbl_share sh on sh.isin=it.isin where it.indxx_id='" . $row['id'] . "' 
							and fp.indxx_id='" . $row['id'] . "' and sh.indxx_id='" . $row['id'] . "' and fp.date='" . $datevalue . "'";			
		
		
		
			$indxxpricesres=	mysql_query($query);	
		
		//$this->pr($indxxprices,true);
		$indxxprices=array();
			
				
		while(false != ($indxxprice = mysql_fetch_assoc($indxxpricesres)))
		{
			$indxxprices[]=$indxxprice;
			
				
			$indxx_dp_value=$this->db->getResult("select tbl_dividend_ph.* from tbl_dividend_ph where indxx_id='".$row['id']."' and ticker_id ='".$indxxprice['id']."' ",false,1);	
			if(!empty($indxx_dp_value))
			{
			foreach($indxx_dp_value as $dpvalue)
			{	$final_array[$row['id']]['divpvalue']+=$dpvalue['share']*$dpvalue['dividend'];
			}
			}
				
				
			/*if(!empty($cas))
			{
			foreach($cas as $cakey=> $ca)
			{
			$ca_value_query="Select field_name,field_value,field_id from tbl_ca_values where ca_id='".$ca['id']."'  and ca_action_id='".$ca['action_id']."' ";
			$ca_values=$this->db->getResult($ca_value_query,true);	
			
			$cas[$cakey]['ca_values']=$ca_values;
			}
			}
			*/
			
			//$indxxprices[$key]['ca']=$cas;
			}
			
			
			$final_array[$row['id']]['values']=$indxxprices;
		unset($indxxprices);
			mysql_free_result($indxxpricesres);
		//$this->pr($indxxprices);	
			
			
				
		
			}
			
		//}
			

	//	$this->pr($final_array,true);	
			mysql_free_result($indxxs);
		$backup_folder = "../files/output/backup/";
		if (!file_exists($backup_folder))
			mkdir($backup_folder, 0777, true);
		//$this->pr($final_array,true);
		if($type=='close')
{	
		//  file_put_contents('../files/backup/preclosedata'.date("Y-m-d-H-i-s").time().'.json', json_encode($final_array));
	if(!empty($final_array))
	{
		foreach($final_array as $indxxKey=> $closeIndxx)
		{
			
			//$this->pr($closeIndxx,true);
			if(!$closeIndxx['client'])
			{$file="../files/ca-output/Closing-".$closeIndxx['code']."-".$datevalue.".txt";
			}else
			{$file="../files/ca-output/".$closeIndxx['client']."/Closing-".$closeIndxx['code']."-".$datevalue.".txt";
			$client_folder = "../files/ca-output/".$closeIndxx['client']."/";
			if (!file_exists($client_folder))
			mkdir($client_folder, 0777, true);

			}
			$open=fopen($file,"w+");

			$entry1='Date'.",";
			$entry1.=date("Y-m-d",strtotime($datevalue)).",\n";
			$entry1.='Index Value'.",";
			$entry3='Effective Date'.",";
			$entry3.='TICKER'.",";
			$entry3.='Name'.",";
			$entry3.='ISIN'.",";
			$entry3.='SEDOL'.",";
			$entry3.='CUSIP'.",";
			$entry3.='Country'.",";
			$entry3.='Index Shares'.",";
			$entry3.='Weight'.",";
			$entry3.='Price'.",";
			
			if($closeIndxx['display_currency'])
			{$entry3.='Currency'.",";
			$entry3.='Currency Factor'.",";
			}$entry4='';
			
			
			//$this->pr($closeIndxx);
			$oldindexvalue=$closeIndxx['index_value']['indxx_value'];
			$newindexvalue=0;
			$newindexvalue2=0;
			$oldDivisor=$closeIndxx['index_value']['newdivisor'];
			$newDivisor=$oldDivisor;
			$marketValue=0;
			$sumofDividendes=0;
			
			foreach($closeIndxx['values'] as $closeprices)
			{
			//$this->pr($closeprices);
		
			$shareValue=$closeprices['calcshare'];	
			$securityPrice=$closeprices['calcprice'];
			$localprice=(float)$closeprices['localprice'];
			$dividendPrice=0;
			if(!empty($closeprices['ca']))
			{
			
				foreach($closeprices['ca'] as $ca_actions)
				{
					if($ca_actions['mnemonic']=='DVD_CASH')
					{
			//			echo $datevalue;
						
					$ca_prices= $this->getCaPrices($ca_actions['id'],$ca_actions['action_id'],$ca_actions['currency'],$final_array[$indxxKey]['curr'],$datevalue);			$dividendPrice+=$ca_prices['ca_price_index_currency'];
				//	echo "<br>";
					}
				
				
				}
				
			}
			//echo $dividendPrice."<br>";
			$marketValue+=$shareValue*$securityPrice;	
			$sumofDividendes+=$shareValue*$dividendPrice;	
		//	$sumofDividendes;
		//exit;
			
			/*$entry4.= "\n".date("Ymd",strtotime($datevalue)).",";
            $entry4.=  $closeprices['ticker'].",";
            $entry4.= $closeprices['name'].",";
            $entry4.=$closeprices['isin'].",";;
            $entry4.=$closeprices['sedol'].",";;
            $entry4.=$closeprices['cusip'].",";;
            $entry4.=$closeprices['countryname'].",";
            $entry4.=$closeprices['calcshare'].",";
       		$entry4.=number_format($localprice,2,'.','').",";
			if($closeIndxx['display_currency'])
	     	{$entry4.=$closeprices['curr'].",";
			$entry4.=number_format($closeprices['currencyfactor'],6,'.','').",";
			}*/

			}
		
	
//echo $closeIndxx['id']."<br>";
		//echo $oldindexvalue;
		//exit;
		
		//$newDivisor=number_format($oldDivisor-($sumofDividendes/$oldindexvalue),4,'.','');
		
if($closeIndxx['divpvalue'])
{
	$marketValue+=$closeIndxx['divpvalue'];
	$newindexvalue2=$marketValue/$newDivisor;
	 $newindexvalue=number_format((($marketValue)/$newDivisor),2,'.','');
}
else
 {$newindexvalue=number_format(($marketValue/$newDivisor),2,'.','');
 $newindexvalue2=$marketValue/$newDivisor;
 }	
 
 $weightArray=array();
 foreach($closeIndxx['values'] as $closeprices)
			{
				$localprice=(float)$closeprices['localprice'];
				
			$entry4.= "\n".date("Ymd",strtotime($datevalue)).",";
            $entry4.=  $closeprices['ticker'].",";
            $entry4.= $closeprices['name'].",";
            $entry4.=$closeprices['isin'].",";;
            $entry4.=$closeprices['sedol'].",";;
            $entry4.=$closeprices['cusip'].",";;
            $entry4.=$closeprices['countryname'].",";
            $entry4.=$closeprices['calcshare'].",";
       		$weight=(($closeprices['calcshare']*$closeprices['calcprice'])/$marketValue);
			$entry4.=$weight.",";
			$entry4.=number_format($localprice,2,'.','').",";
			if($closeIndxx['display_currency'])
	     	{$entry4.=$closeprices['curr'].",";
			$entry4.=number_format($closeprices['currencyfactor'],6,'.','').",";
			}
			$weightArray[]="('".$closeprices['isin']."','".$weight."','".$closeprices['calcprice']."','".$closeprices['calcshare']."','".$datevalue."','".$closeIndxx['code']."','".$closeIndxx['id']."')";
			
			
			}
 if(!empty($weightArray))
{
	//echo "insert into tbl_weights (isin,weight,price,share,date,code,indxx_id) values " .implode(",",$weightArray).";";
	 $this->db->query("insert into tbl_weights (isin,weight,price,share,date,code,indxx_id) values " .implode(",",$weightArray).";");
}
 
 
 //	$newindexvalue=number_format(($marketValue/$newDivisor),2,'.','');
		$entry2=$newindexvalue.",\n";
		$entry2.="Divisor,".$newDivisor.",\n";
		$entry2.="Market Value,".$marketValue.",\n\n";
		//echo $entry1.$entry2.$entry3.$entry4;
		//exit;
	 $insertQuery='INSERT into tbl_indxx_value (indxx_id,code,market_value,indxx_value,date,olddivisor,newdivisor) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.$marketValue.'","'.$newindexvalue2.'","'.$datevalue.'","'.$oldDivisor.'","'.$newDivisor.'")';
		//exit;
		$this->db->query($insertQuery);	
		
		if($open){   
 if(   fwrite($open,$entry1.$entry2.$entry3.$entry4))
{    

//$insertlogQuery='INSERT into tbl_indxx_log (type,indxx_id,value) values ("1","'.$closeIndxx['id'].'","'.mysql_real_escape_string($entry1.$entry2.$entry3.$entry4).'")';
	//	$this->db->query($insertlogQuery);
    fclose($open);
echo "file Writ for ".$closeIndxx['code']."<br>";

}
}  

		
		
		
		unset($final_array[$indxxKey]);
		}
		
	}
	
	 // file_put_contents('../files/backup/postclosedata'.date("Y-m-d-H-i-s").time().'.json', json_encode($final_array));
}
		unset($final_array);
		
		$this->saveProcess(2);
	//$this->Redirect2("index.php?module=calcindxxclosingtemp&date=" .date. "&log_file=" . basename(log_file),"","");		
		
		
	}
		
			function closingTemp()
	{		
		
		//$this->pr($_SESSION);
		
		//$this->_baseTemplate="main-template";
		//$this->_bodyTemplate="404";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		
		$type="close";
		
		
					if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));

		
		$datevalue=date;
		
		//$datevalue=date("Y-m-d",strtotime($datevalue)-86400);
		if($_GET['log_file'])
			define("log_file", $_GET['log_file']);
				$this->log_info(log_file, "Closing file generation process started for upcomming indexes.");
		if($_GET['id'])
		{
			$page=$_GET['id'];	
		}
		else
		{
			$page=0;	
		}
		
		$limit=5;
		//echo "select tbl_indxx.* from tbl_indxx  where status='1' and usersignoff='1' and dbusersignoff='1' and submitted='1' limit $page,3";
		//exit;
		$indxxs=mysql_query("select tbl_indxx_temp.* from tbl_indxx_temp  where status='1' and usersignoff='1' and dbusersignoff='1' and submitted='1' ");
		
		if ($err_code = mysql_errno())
		{
			$this->log_error(log_file, "Unable to read temp indexes. MYSQL error code " . $err_code .
					". Exiting closing file process.");
			$this->mail_exit(log_file, __FILE__, __LINE__);
		}
		
		$final_array=array();
		
			while(false != ($row = mysql_fetch_assoc($indxxs)))
		{
	//$this->pr($indxxs,true);
					
					
					$this->log_info(log_file, "Processing closing data file for index = " . $row['code']);
		//if($row['id']==31)
		//{
			
				$final_array[$row['id']]=$row;
			
			
			
			
			
			
			
			$client=$this->db->getResult("select tbl_ca_client.ftpusername from tbl_ca_client where id='".$row['client_id']."'",false,1);	
		//	
		$final_array[$row['id']]['client']=$client['ftpusername'];
			
			$this->log_info(log_file, "FTP folder of " . $row['code'] ." is :".$client['ftpusername'] );
			
			$indxx_value=$this->db->getResult("select tbl_indxx_value_open_temp.* from tbl_indxx_value_open_temp where indxx_id='".$row['id']."' order by date desc ",false,1);	
		//	$this->pr($indxx_value,true);
			if(!empty($indxx_value))
			{
			$final_array[$row['id']]['index_value']=$indxx_value;
			}
			else{
			$final_array[$row['id']]['index_value']['market_value']=$row['investmentammount'];
			$final_array[$row['id']]['index_value']['olddivisor']=$row['divisor'];
			$final_array[$row['id']]['index_value']['newdivisor']=$row['divisor'];
			$final_array[$row['id']]['index_value']['indxx_value']=$row['indexvalue'];
			if($final_array[$row['id']]['index_value']['olddivisor']==0){
			$final_array[$row['id']]['index_value']['olddivisor']=$row['investmentammount']/$row['indexvalue'];
			}
			if($final_array[$row['id']]['index_value']['newdivisor']==0){
			$final_array[$row['id']]['index_value']['newdivisor']=$row['investmentammount']/$row['indexvalue'];
			}


			}
			//$this->pr(	$final_array,true);
			
			//$indxx_value=$this->db->getResult("select tbl_indxx_value_open.* from tbl_indxx_value_open where indxx_id='".$row['id']."' order by date desc ",false,1);	
			
			
			$query = "SELECT  it.id, it.name, it.isin, it.ticker, it.curr, it.sedol, it.cusip, it.countryname, 
							fp.localprice, fp.currencyfactor, fp.price as calcprice, sh.share as calcshare 
							FROM `tbl_indxx_ticker_temp` it left join tbl_final_price_temp fp on fp.isin=it.isin 
							left join tbl_share_temp sh on sh.isin=it.isin where it.indxx_id='" . $row['id'] . "' 
							and fp.indxx_id='" . $row['id'] . "' and sh.indxx_id='" . $row['id'] . "' and fp.date='" . $datevalue . "'";		
		
		
		
			$queryRes=	mysql_query($query);	
		
		//$this->pr($indxxprices,true);
		
			$indxxprices[]=array();
				
		while(false != ($indxxprice = mysql_fetch_assoc($queryRes)))
		{
				$indxxprices[]=$indxxprice;
			$indxx_dp_value=$this->db->getResult("select tbl_dividend_ph_temp.* from tbl_dividend_ph_temp where indxx_id='".$row['id']."' and ticker_id ='".$indxxprice['id']."' ",false,1);	
			if(!empty($indxx_dp_value))
			{
			foreach($indxx_dp_value as $dpvalue)
			{	$final_array[$row['id']]['divpvalue']+=$dpvalue['share']*$dpvalue['dividend'];
			}
			}
				
				
			/*if(!empty($cas))
			{
			foreach($cas as $cakey=> $ca)
			{
			$ca_value_query="Select field_name,field_value,field_id from tbl_ca_values where ca_id='".$ca['id']."'  and ca_action_id='".$ca['action_id']."' ";
			$ca_values=$this->db->getResult($ca_value_query,true);	
			
			$cas[$cakey]['ca_values']=$ca_values;
			}
			}
			*/
			
			//$indxxprices[$key]['ca']=$cas;
			}
			
			
			$final_array[$row['id']]['values']=$indxxprices;
		
			mysql_free_result($queryRes);
			unset($indxxprices);
		//$this->pr($indxxprices);	
			
			
				
		
			}
			
		//}
			

		
			mysql_free_result($indxxs);
		$backup_folder = "../files/output/backup/";
		if (!file_exists($backup_folder))
			mkdir($backup_folder, 0777, true);
		//$this->pr($final_array,true);
		if($type=='close')
{	
		//  file_put_contents('../files/backup/preclosedata'.date("Y-m-d-H-i-s").time().'.json', json_encode($final_array));
	if(!empty($final_array))
	{
		foreach($final_array as $indxxKey=> $closeIndxx)
		{
			
			if(!$closeIndxx['client'])
			{$file="../files/ca-output_upcomming/Closing-".$closeIndxx['code']."-".$datevalue.".txt";
			}else
			{$file="../files/ca-output_upcomming/".$closeIndxx['client']."/Closing-".$closeIndxx['code']."-".$datevalue.".txt";
			$client_folder = "../files/ca-output_upcomming/".$closeIndxx['client']."/";
			if (!file_exists($client_folder))
			mkdir($client_folder, 0777, true);

			}
			$open=fopen($file,"w+");

			$entry1='Date'.",";
			$entry1.=date("Y-m-d",strtotime($datevalue)).",\n";
			$entry1.='Index Value'.",";
			$entry3='Effective Date'.",";
			$entry3.='TICKER'.",";
			$entry3.='Name'.",";
			$entry3.='ISIN'.",";
			$entry3.='SEDOL'.",";
			$entry3.='CUSIP'.",";
			$entry3.='Country'.",";
			$entry3.='Index Shares'.",";
			$entry3.='Weight'.",";
			$entry3.='Price'.",";
			
			if($closeIndxx['display_currency'])
			{$entry3.='Currency'.",";
			$entry3.='Currency Factor'.",";
			}$entry4='';
			
			
			//$this->pr($closeIndxx);
			$oldindexvalue=$closeIndxx['index_value']['indxx_value'];
			$newindexvalue=0;
			$newindexvalue2=0;
			$oldDivisor=$closeIndxx['index_value']['newdivisor'];
			$newDivisor=$oldDivisor;
			$marketValue=0;
			$sumofDividendes=0;
			
			foreach($closeIndxx['values'] as $closeprices)
			{
			//$this->pr($closeprices);
		
			$shareValue=$closeprices['calcshare'];	
			$securityPrice=$closeprices['calcprice'];
			$localprice=(float)$closeprices['localprice'];
			$dividendPrice=0;
			if(!empty($closeprices['ca']))
			{
			
				foreach($closeprices['ca'] as $ca_actions)
				{
					if($ca_actions['mnemonic']=='DVD_CASH')
					{
			//			echo $datevalue;
						
					$ca_prices= $this->getCaPrices($ca_actions['id'],$ca_actions['action_id'],$ca_actions['currency'],$final_array[$indxxKey]['curr'],$datevalue);			$dividendPrice+=$ca_prices['ca_price_index_currency'];
				//	echo "<br>";
					}
				
				
				}
				
			}
			//echo $dividendPrice."<br>";
			$marketValue+=$shareValue*$securityPrice;	
			$sumofDividendes+=$shareValue*$dividendPrice;	
		//	$sumofDividendes;
		//exit;
			
			/*$entry4.= "\n".date("Ymd",strtotime($datevalue)).",";
            $entry4.=  $closeprices['ticker'].",";
            $entry4.= $closeprices['name'].",";
            $entry4.=$closeprices['isin'].",";;
            $entry4.=$closeprices['sedol'].",";;
            $entry4.=$closeprices['cusip'].",";;
            $entry4.=$closeprices['countryname'].",";
            $entry4.=$closeprices['calcshare'].",";
       		$entry4.=number_format($localprice,2,'.','').",";
			if($closeIndxx['display_currency'])
	     	{$entry4.=$closeprices['curr'].",";
			$entry4.=number_format($closeprices['currencyfactor'],6,'.','').",";
			}*/

			}
		
	
//echo $closeIndxx['id']."<br>";
		//echo $oldindexvalue;
		//exit;
		
		//$newDivisor=number_format($oldDivisor-($sumofDividendes/$oldindexvalue),4,'.','');
		
if($closeIndxx['divpvalue'])
{
	$marketValue+=$closeIndxx['divpvalue'];
	$newindexvalue2=$marketValue/$newDivisor;
	 $newindexvalue=number_format((($marketValue)/$newDivisor),2,'.','');
}
else
 {$newindexvalue=number_format(($marketValue/$newDivisor),2,'.','');
 $newindexvalue2=$marketValue/$newDivisor;
 }	
 
 //	$newindexvalue=number_format(($marketValue/$newDivisor),2,'.','');
		$entry2=$newindexvalue.",\n";
		$entry2.="Divisor,".$newDivisor.",\n";
		$entry2.="Market Value,".$marketValue.",\n\n";
		//echo $entry1.$entry2.$entry3.$entry4;
		//exit;
		
		$weightArray=array();
 foreach($closeIndxx['values'] as $closeprices)
			{
				$localprice=(float)$closeprices['localprice'];
				
			$entry4.= "\n".date("Ymd",strtotime($datevalue)).",";
            $entry4.=  $closeprices['ticker'].",";
            $entry4.= $closeprices['name'].",";
            $entry4.=$closeprices['isin'].",";;
            $entry4.=$closeprices['sedol'].",";;
            $entry4.=$closeprices['cusip'].",";;
            $entry4.=$closeprices['countryname'].",";
            $entry4.=$closeprices['calcshare'].",";
       		$weight=(($closeprices['calcshare']*$closeprices['calcprice'])/$marketValue);
			$entry4.=$weight.",";
			$entry4.=number_format($localprice,2,'.','').",";
			if($closeIndxx['display_currency'])
	     	{$entry4.=$closeprices['curr'].",";
			$entry4.=number_format($closeprices['currencyfactor'],6,'.','').",";
			}
			$weightArray[]="('".$closeprices['isin']."','".$weight."','".$closeprices['calcprice']."','".$closeprices['calcshare']."','".$datevalue."','".$closeIndxx['code']."','".$closeIndxx['id']."')";
			
			
			}
 if(!empty($weightArray))
{
	//echo "insert into tbl_weights (isin,weight,price,share,date,code,indxx_id) values " .implode(",",$weightArray).";";
	 $this->db->query("insert into tbl_weights_temp (isin,weight,price,share,date,code,indxx_id) values " .implode(",",$weightArray).";");
}
 
		
		
	$insertQuery='INSERT into tbl_indxx_value_temp (indxx_id,code,market_value,indxx_value,date,olddivisor,newdivisor) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.$marketValue.'","'.$newindexvalue2.'","'.$datevalue.'","'.$oldDivisor.'","'.$newDivisor.'")';
		$this->db->query($insertQuery);	
		
		if($open){   
 if(   fwrite($open,$entry1.$entry2.$entry3.$entry4))
{    

//$insertlogQuery='INSERT into tbl_indxx_log_temp (type,indxx_id,value) values ("1","'.$closeIndxx['id'].'","'.mysql_real_escape_string($entry1.$entry2.$entry3.$entry4).'")';
	//	$this->db->query($insertlogQuery);
    fclose($open);
echo "file Writ for ".$closeIndxx['code']."<br>";

}
}  

		
		
		
		unset($final_array[$indxxKey]);
		}
		
	}
	
	 // file_put_contents('../files/backup/postclosedata'.date("Y-m-d-H-i-s").time().'.json', json_encode($final_array));
}
		unset($final_array);
		
		$this->saveProcess(2);
	//$this->Redirect2("index.php?module=compositclose&date=" .date. "&log_file=" . basename(log_file),"","");		
		
		
	}

	function compositclose()
	{
		
		//echo $this->_date;

			if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));

		if($_GET['log_file'])
			define("log_file", $_GET['log_file']);
				$this->log_info(log_file, "In Composite Closing generattion ");
		
	$clientData=$this->db->getResult("select id,ftpusername from tbl_ca_client where status='1'");
	//$this->pr($clientData,true);
	
	///$date=date('Y-m-d',strtotime($this->_date)-86400);
	$date=date;
	
	//$date="2014-03-28";
	if(!empty($clientData))
	{
		foreach($clientData as $client)
		{
				$file="../files/ca-output/".$client['ftpusername']."/compositclosing-".$date.".txt";
				
				$client_folder = "../files/ca-output/".$client['ftpusername']."/";
			if (!file_exists($client_folder))
			mkdir($client_folder, 0777, true);



$this->log_info(log_file, "Preparing composit file for ". $client['ftpusername']);
			
				$entry1="Date".",".$date.",\r\n";
				$entry1.="Name,Code,Market Value,Index Value,\r\n";
				
				$indexes=$this->db->getResult("select id,name,code from tbl_indxx where client_id='".$client['id']."'",true);
				if(!empty($indexes))
				{
				
				foreach($indexes as $index)
				{
					
				$data=	$this->db->getResult("select market_value,indxx_value from tbl_indxx_value where indxx_id='".$index['id']."' and date='".$date."'");
				$entry1.=$index['name'].','.$index['code'].','.$data['market_value'].','.$data['indxx_value'].",\r\n";
				
				
				//$this->pr($data);
				}	
								
				}
				
				$open=fopen($file,"w+");
					if($open){   
 if(   fwrite($open,$entry1))
{
	echo "file Written Successfully ";
	$this->log_info(log_file, "omposit file written for ". $client['ftpusername']);
	
	}
}	
				//$this->pr($indexes);
		
		}
	}
	$this->saveProcess(2);
	
	
	//$this->Redirect2("index.php?module=calccash&date=" .date. "&log_file=" . basename(log_file),"","");	
	
//	$this->Redirect("index.php?module=calccsi","","");	
	}
	function calccash()
	{
		
					if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));

		//echo "select * from tbl_indxx_cs  where status='1' ";
		$indxxs=$this->db->getResult("select * from tbl_cash_index  where 1=1 ",true);	
		//$this->pr($indxxs);
		// $datevalue2=date('Y-m-d',strtotime($this->_date)-86400);
	//	 $datevalue2='2014-08-22';
	if($_GET['log_file'])
			define("log_file", $_GET['log_file']);
				$this->log_info(log_file, "In CalcCash File  generattion  for live index");
	
		 $datevalue2=date;
		 $final_array=array();
		 if(!empty($indxxs))
		 {
			 
			 foreach($indxxs as $row)
			 {
				
					$this->log_info(log_file, "Preparing data for cash index :".$row['name']);
				
				$final_array[$row['id']]=$row;
					$client=$this->db->getResult("select tbl_ca_client.ftpusername from tbl_ca_client where id='".$row['client_id']."'",false,1);	
					$final_array[$row['id']]['client']=$client['ftpusername'];
					$cashindxx_value=$this->db->getResult("select indxx_value from tbl_cash_indxx_value  where indxx_id='".$row['id']."' order by dateAdded desc ",false,1);
				
				$final_array[$row['id']]['last_index_value']=$cashindxx_value['indxx_value'];
		
	//	echo "select price from tbl_cash_prices  where isin like '%".$row['isin']."%' order by date desc ";
				$cashrates=$this->db->getResult("select price from tbl_cash_prices  where isin like '%".$row['isin']."%' order by date desc ",true,2);	
				$final_array[$row['id']]['last_2_days_cash_rate']=$cashrates;
				
				
				
			
		}
		
		}
		
		//$this->pr($final_array,true);

		if(!empty($final_array))
		{  file_put_contents('../files/backup/preCLOSECASHdata'.date("Y-m-d-H-i-s").'.json', json_encode($final_array));
		 	foreach($final_array as $key=>$closeIndxx)
		{
			
			if(!empty($closeIndxx['last_2_days_cash_rate']))
			{
			if(!$closeIndxx['client'])
			$file="../files/ca-output/Closing-".$closeIndxx['code']."-".$datevalue2.".txt";
			else
			$file="../files/ca-output/".$closeIndxx['client']."/Closing-".$closeIndxx['code']."-".$datevalue2.".txt";
			
			$client_folder="../files/ca-output/".$closeIndxx['client']."/";
			if (!file_exists($client_folder))
			mkdir($client_folder, 0777, true);

			
			
			$open=fopen($file,"w+");
			
			$entry1='Date'.",";
			$entry1.=date("Y-m-d",strtotime($datevalue2)).",\n";
			$entry1.='Index Value'.",";
			
			
			$entry4='';
		$index_value=0;
		
			if(!empty($closeIndxx))
		{
			
			$index_value=$closeIndxx['last_index_value']*($closeIndxx['last_2_days_cash_rate'][0]['price']/$closeIndxx['last_2_days_cash_rate'][1]['price']);
			
			/*
		foreach($closeIndxx['values'] as $security)
		{
			
			//$this->pr($security);
			//echo ($security['indxx_value'][0]['indxx_value']/$security['indxx_value'][1]['indxx_value'])-1;
			//exit;
		$index_value=$closeIndxx['last_index_value']*(1+($security['fraction']*(($security['indxx_value'][0]['indxx_value']/$security['indxx_value'][1]['indxx_value'])-1)));
		//echo 	$index_value;
		//exit;
		
            $entry4.= "\n".$security['indxx_name'].",";
            $entry4.=$security['code'].","; 
			 $entry4.=$security['fraction'].",";
            $entry4.=$security['indxx_value'][0]['indxx_value'].",";
	//		echo (($closeIndxx['last_index_value']*($security['fraction']-1)*$closeIndxx['libor_rate'])/360);
			
		$newIndex_value=$index_value-(($closeIndxx['last_index_value']*($security['fraction']-1)*($closeIndxx['libor_rate'])/100)/360);
		}
		
		*/}

//echo $newIndex_value;
//exit;


	$entry2=number_format($index_value,2,'.','').",\n";

 $insertQuery='INSERT into tbl_cash_indxx_value (indxx_id,code,indxx_value,date) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.number_format($index_value,2,'.','').'","'.$datevalue2.'")';
		$this->db->query($insertQuery);	
	
	if($open){   
 if(   fwrite($open,$entry1.$entry2.$entry3.$entry4))
{
$this->log_info(log_file, "file written for :".$row['name']);
				
}}
		
		}
		 file_put_contents('../files/backup/postOPENCASHdata'.date("Y-m-d-H-i-s").'.json', json_encode($final_array));
		}
		
		}
	$this->saveProcess(2);
//$this->Redirect2("index.php?module=calccashtemp&date=" .date. "&log_file=" . basename(log_file),"","");			
		
		
		
	}
function calccashtemp()
	{
		
					if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));

		//echo "select * from tbl_indxx_cs  where status='1' ";
		$indxxs=$this->db->getResult("select * from tbl_cash_index_temp  where 1=1 ",true);	
		//$this->pr($indxxs);
		 //$datevalue2=date('Y-m-d',strtotime($this->_date)-86400);
	//	 $datevalue2='2014-08-22';
		 $datevalue2=date;
		 if($_GET['log_file'])
			define("log_file", $_GET['log_file']);
				$this->log_info(log_file, "In CalcCash File  generation  for upcoming index");
	
		 $final_array=array();
		 if(!empty($indxxs))
		 {
			 
			 foreach($indxxs as $row)
			 {
			$this->log_info(log_file, "Preparing data for index : ".$row['name']);
				$final_array[$row['id']]=$row;
					$client=$this->db->getResult("select tbl_ca_client.ftpusername from tbl_ca_client where id='".$row['client_id']."'",false,1);	
					$final_array[$row['id']]['client']=$client['ftpusername'];
					$cashindxx_value=$this->db->getResult("select indxx_value from tbl_cash_indxx_value_temp  where indxx_id='".$row['id']."' order by dateAdded desc ",false,1);
				
				$final_array[$row['id']]['last_index_value']=$cashindxx_value['indxx_value'];
		
	//	echo "select price from tbl_cash_prices  where isin like '%".$row['isin']."%' order by date desc ";
				$cashrates=$this->db->getResult("select price from tbl_cash_prices  where isin like '%".$row['isin']."%' order by date desc ",true,2);	
				$final_array[$row['id']]['last_2_days_cash_rate']=$cashrates;
				
				
				
			
		}
		
		}
		
		//$this->pr($final_array);

		if(!empty($final_array))
		{  file_put_contents('../files/backup/preCLOSECASH_tempdata'.date("Y-m-d-H-i-s").'.json', json_encode($final_array));
		 	foreach($final_array as $key=>$closeIndxx)
		{
			if(!$closeIndxx['client'])
			$file="../files/ca-output/Closing-".$closeIndxx['code']."-".$datevalue2.".txt";
			else
			$file="../files/ca-output/".$closeIndxx['client']."/Closing-".$closeIndxx['code']."-".$datevalue2.".txt";
			
			$client_folder="../files/ca-output/".$closeIndxx['client']."/";
			if (!file_exists($client_folder))
			mkdir($client_folder, 0777, true);

			
			
			
			$open=fopen($file,"w+");
			
			$entry1='Date'.",";
			$entry1.=date("Y-m-d",strtotime($datevalue2)).",\n";
			$entry1.='Index Value'.",";
			
			
			$entry4='';
		$index_value=0;
		
			if(!empty($closeIndxx))
		{
			if($closeIndxx['last_2_days_cash_rate'][1]['price'])
			$index_value=$closeIndxx['last_index_value']*($closeIndxx['last_2_days_cash_rate'][0]['price']/$closeIndxx['last_2_days_cash_rate'][1]['price']);
			
			/*
		foreach($closeIndxx['values'] as $security)
		{
			
			//$this->pr($security);
			//echo ($security['indxx_value'][0]['indxx_value']/$security['indxx_value'][1]['indxx_value'])-1;
			//exit;
		$index_value=$closeIndxx['last_index_value']*(1+($security['fraction']*(($security['indxx_value'][0]['indxx_value']/$security['indxx_value'][1]['indxx_value'])-1)));
		//echo 	$index_value;
		//exit;
		
            $entry4.= "\n".$security['indxx_name'].",";
            $entry4.=$security['code'].","; 
			 $entry4.=$security['fraction'].",";
            $entry4.=$security['indxx_value'][0]['indxx_value'].",";
	//		echo (($closeIndxx['last_index_value']*($security['fraction']-1)*$closeIndxx['libor_rate'])/360);
			
		$newIndex_value=$index_value-(($closeIndxx['last_index_value']*($security['fraction']-1)*($closeIndxx['libor_rate'])/100)/360);
		}
		
		*/}

//echo $newIndex_value;
//exit;


	$entry2=number_format($index_value,2,'.','').",\n";

 $insertQuery='INSERT into tbl_cash_indxx_value_temp (indxx_id,code,indxx_value,date) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.number_format($index_value,2,'.','').'","'.$datevalue2.'")';
		$this->db->query($insertQuery);	
	
	if($open){   
 if(   fwrite($open,$entry1.$entry2.$entry3.$entry4))
{
$this->log_info(log_file, "file writing done for cash temp index : ".$row['name']);
}}
		
		}
		 file_put_contents('../files/backup/postOPENCASH_tempdata'.date("Y-m-d-H-i-s").'.json', json_encode($final_array));
		}
		
		
	//	$this->saveProcess(2);
		
		
		$this->saveProcess(2);
//$this->Redirect2("index.php?module=calclsc&date=" .date. "&log_file=" . basename(log_file),"","");		
		
		
	}
	function calclsc()
	{
		
					if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));

		//echo "select * from tbl_indxx_cs  where status='1' ";
		$indxxs=$this->db->getResult("select * from tbl_indxx_lsc  where status='1' ",true);	
	//	$this->pr($indxxs,true);
		 //$datevalue2=date('Y-m-d',strtotime($this->_date)-86400);
		 $datevalue2=date;
		  if($_GET['log_file'])
			define("log_file", $_GET['log_file']);
				$this->log_info(log_file, "In LongShortCash File  generation  for live index");
	
		 $final_array=array();
		 if(!empty($indxxs))
		 {
			 
			 foreach($indxxs as $row)
			 {
				if($this->checkHoliday($row['zone'], $datevalue2)){
				$final_array[$row['id']]=$row;
					$this->log_info(log_file, "Preparing data for :" .$row['name']);
				
					$client=$this->db->getResult("select tbl_ca_client.ftpusername from tbl_ca_client where id='".$row['client_id']."'",false,1);	
					$final_array[$row['id']]['client']=$client['ftpusername'];
				//echo "select * from tbl_lsc_adj_factor  where lsc_indxx_id='".$row['id']."' ";
				$calcfactor=$this->db->getResult("select * from tbl_lsc_adj_factor  where lsc_indxx_id='".$row['id']."' ",false,1);	
				$final_array[$row['id']]['calcfactor']=$calcfactor;
			
				if(!empty($calcfactor))
				{
					
				$long_indxx_value=$this->db->getResult("select indxx_value,date from tbl_indxx_value  where code='".$calcfactor['long_code']."' order by date desc ",false,1);
				$final_array[$row['id']]['long_index_value']=$long_indxx_value;
				$short_indxx_value=$this->db->getResult("select indxx_value,date from tbl_indxx_value  where code='".$calcfactor['short_code']."' order by date desc ",false,1);
				$final_array[$row['id']]['short_index_value']=$short_indxx_value;
					//echo "select indxx_value,date from tbl_cash_indxx_value  where code='".$calcfactor['cash_code']."' order by date desc ";
				$cash_indxx_value=$this->db->getResult("select indxx_value,date from tbl_cash_indxx_value  where code='".$calcfactor['cash_code']."' order by date desc ",false,1);
					
				$final_array[$row['id']]['cash_index_value']=$cash_indxx_value;
				//$this->pr($final_array);
				if(!empty($long_indxx_value) && !empty($short_indxx_value) && !empty($cash_indxx_value))
				{if($cash_indxx_value['date']!=$short_indxx_value['date']  && $cash_indxx_value['date'] !=$long_indxx_value['date'])
				{
					
					//echo "Date Mismatch";
					
					$this->log_error(log_file, "long short value of index :" .$row['name']." not calculated due to error mismatch");
					
					
					$msg="Long short Cash Index is not calculated ".$row['name']." due to value mismatch";
					
					mail(($_SESSION['User']['email'] ?$_SESSION['User']['email'].",ical@indxx.com" : 'ical@indxx.com'),"Long Short Cash Index Not Calculated ",$msg);
				unset($final_array[$row['id']]);
				
				}}else{
				$msg="Long short Cash Index is not calculated ".$row['name']." due to value unavailable";
				$this->log_error(log_file, "long short value of index :" .$row['name']." not calculated due to value not available");	
				mail(($_SESSION['User']['email'] ?$_SESSION['User']['email'].",ical@indxx.com" : 'ical@indxx.com'),"Long Short Cash Index Not Calculated ",$msg);
						unset($final_array[$row['id']]);
				
				}
				
				
					/*
				foreach($calcfactors as $key=> $calcfactor)
				{
				//$this->pr($calcfactor);
				
				
				$indxx_name=$this->db->getResult("select name from tbl_indxx  where code='".$calcfactor['code']."' ",false,1);
			//	echo "select indxx_value from tbl_indxx_value  where code='".$calcfactor['code']."' order by date desc ";
				$indxx_value=$this->db->getResult("select indxx_value,date from tbl_indxx_value  where code='".$calcfactor['code']."' order by date desc ",false,2);
					$calcfactors[$key]['indxx_name']=$indxx_name['name'];
					$calcfactors[$key]['indxx_value']=$indxx_value;
				
				
				}
				*/}
				$final_array[$row['id']]['values']=$calcfactors;
				
				}
			
			}
			 
			 
		 }
		 
//$this->pr( $final_array,true);
	//exit;	
		if(!empty($final_array))
		{  file_put_contents('../files/backup/preCLOSELSCdata'.date("Y-m-d-H-i-s").'.json', json_encode($final_array));
		 	foreach($final_array as $key=>$closeIndxx)
		{
			
			
			if(!$closeIndxx['client'])
			$file="../files/ca-output/Closing-".$closeIndxx['code']."-".$datevalue2.".txt";
			else
			$file="../files/ca-output/".$closeIndxx['client']."/Closing-".$closeIndxx['code']."-".$datevalue2.".txt";
			
			$client_folder="../files/ca-output/".$closeIndxx['client']."/";
			if (!file_exists($client_folder))
			mkdir($client_folder, 0777, true);

			
			
			$open=fopen($file,"w+");
			
			$entry1='Date'.",";
			$entry1.=date("Y-m-d",strtotime($datevalue2)).",\n";
			$entry1.='Index Value'.",";
			$entry3='CODE'.",";
			$entry3.='FACTOR'.",";
			$entry3.='Index Value'.",";
			
			
			$entry4='';
		$index_value=0;
		
			if(!empty($closeIndxx))
		{
			
			$index_value=($closeIndxx['long_index_value']['indxx_value']*$closeIndxx['calcfactor']['long_fraction'])-($closeIndxx['short_index_value']['indxx_value']*$closeIndxx['calcfactor']['short_fraction'])+($closeIndxx['cash_index_value']['indxx_value']*$closeIndxx['calcfactor']['cash_fraction']);
			
			 $entry4.= "\n".$closeIndxx['calcfactor']['long_code'].",";
            $entry4.=$closeIndxx['calcfactor']['long_fraction'].",";
            $entry4.=$closeIndxx['long_index_value']['indxx_value'].",";
	
			$entry4.= "\n".$closeIndxx['calcfactor']['short_code'].",";
            $entry4.=$closeIndxx['calcfactor']['short_fraction'].",";
            $entry4.=$closeIndxx['short_index_value']['indxx_value'].",";
			
			$entry4.= "\n".$closeIndxx['calcfactor']['cash_code'].",";
            $entry4.=$closeIndxx['calcfactor']['cash_fraction'].",";
            $entry4.=$closeIndxx['cash_index_value']['indxx_value'].",";
			
			
			
			/*
		foreach($closeIndxx['values'] as $security)
		{
			
			//$this->pr($security);
			//echo ($security['indxx_value'][0]['indxx_value']/$security['indxx_value'][1]['indxx_value'])-1;
			//exit;
		$index_value=$closeIndxx['last_index_value']*(1+($security['fraction']*(($security['indxx_value'][0]['indxx_value']/$security['indxx_value'][1]['indxx_value'])-1)));
		//echo 	$index_value;
		//exit;
		
            $entry4.= "\n".$security['indxx_name'].",";
            $entry4.=$security['code'].","; 
			 $entry4.=$security['fraction'].",";
            $entry4.=$security['indxx_value'][0]['indxx_value'].",";
	//		echo (($closeIndxx['last_index_value']*($security['fraction']-1)*$closeIndxx['libor_rate'])/360);
			
		$newIndex_value=$index_value-(($closeIndxx['last_index_value']*($security['fraction']-1)*($closeIndxx['libor_rate'])/100)/360);
		}
		
		*/}

//echo $newIndex_value;
//exit;


	$entry2=number_format($index_value,2,'.','').",\n";
//exit;
 $insertQuery='INSERT into tbl_indxx_lsc_value (indxx_id,code,indxx_value,date) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.number_format($index_value,2,'.','').'","'.$datevalue2.'")';
		$this->db->query($insertQuery);	
	
	if($open){   
 if(   fwrite($open,$entry1.$entry2.$entry3.$entry4))
{
$this->log_info(log_file, "long short value written for index  :" .$row['name']);	
}}
		
		}
		 file_put_contents('../files/backup/postOPENLSCdata'.date("Y-m-d-H-i-s").'.json', json_encode($final_array));
		}
		
		
		$this->saveProcess(2);
		//$this->Redirect2("index.php?module=calccsi&date=" .date. "&log_file=" . basename(log_file),"","");
	/*echo '<script>document.location.href="http://97.74.65.118/icai2/publishcsixls.php";</script>';
		//$this->Redirect("index.php?module=calcftpclose","","");		*/
	}
function calccsi()
	{
		
					if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));

		
		//echo "select * from tbl_indxx_cs  where status='1' ";
		$indxxs=$this->db->getResult("select * from tbl_indxx_cs  where status='1' ",true);	
		//$this->pr($indxxs);
		 //$datevalue2=date('Y-m-d',strtotime($this->_date)-(86400));
		 $datevalue2=date;
//		 $datevalue2="2015-04-24";
		  if($_GET['log_file'])
			define("log_file", $_GET['log_file']);
				$this->log_info(log_file, "In Complex Strategies index File  generation  for live index");
	
		 $final_array=array();
		 if(!empty($indxxs))
		 {
			 
			 foreach($indxxs as $row)
			 {
				
					$this->log_info(log_file, "Preparing data for ".$row['name']);
				
				$final_array[$row['id']]=$row;
				
				
					$client=$this->db->getResult("select tbl_ca_client.ftpusername from tbl_ca_client where id='".$row['client_id']."'",false,1);	
					$final_array[$row['id']]['client']=$client['ftpusername'];
			//	echo "select * from tbl_csi_adj_factor  where ca_indxx_id='".$row['id']."' ";
				$calcfactors=$this->db->getResult("select * from tbl_csi_adj_factor  where cs_indxx_id='".$row['id']."' ",true);	
				if(!empty($calcfactors))
				{
				foreach($calcfactors as $key=> $calcfactor)
				{
				//$this->pr($calcfactor);
				
				
				$indxx_name=$this->db->getResult("select name from tbl_indxx  where code='".$calcfactor['code']."' ",false,1);
			//	echo "select indxx_value from tbl_indxx_value  where code='".$calcfactor['code']."' order by date desc ";
				$indxx_value=$this->db->getResult("select indxx_value from tbl_indxx_value  where code='".$calcfactor['code']."' and date='".$datevalue2."' order by date desc ",false,1);
					$calcfactors[$key]['indxx_name']=$indxx_name['name'];
					$calcfactors[$key]['indxx_value']=$indxx_value['indxx_value'];
				
				
				}
				}
				$final_array[$row['id']]['values']=$calcfactors;
				
				
			
			}
			 
			 
		 }
		 
		//$this->pr( $final_array,true);
		
		if(!empty($final_array))
		{  file_put_contents('../files/backup/preCLOSECCSIdata'.date("Y-m-d-H-i-s").'.json', json_encode($final_array));
		 	foreach($final_array as $key=>$closeIndxx)
		{
			if(!$closeIndxx['client'])
			$file="../files/ca-output/Closing-".$closeIndxx['code']."-".$datevalue2.".txt";
			else
			$file="../files/ca-output/".$closeIndxx['client']."/Closing-".$closeIndxx['code']."-".$datevalue2.".txt";
			
			$client_folder="../files/ca-output/".$closeIndxx['client']."/";
			if (!file_exists($client_folder))
			mkdir($client_folder, 0777, true);
			
			$open=fopen($file,"w+");
			
			$entry1='Date'.",";
			$entry1.=date("Y-m-d",strtotime($datevalue2)).",\n";
			$entry1.='Index Value'.",";
			$entry3='NAME'.",";
			$entry3.='CODE'.",";
			$entry3.='FACTOR'.",";
			$entry3.='Index Value'.",";
			
			
			$entry4='';
		$index_value=0;
		
			if(!empty($closeIndxx))
		{
		foreach($closeIndxx['values'] as $security)
		{
		$index_value+=$security['fraction']*$security['indxx_value'];
		
		
            $entry4.= "\n".$security['indxx_name'].",";
            $entry4.=$security['code'].","; 
			 $entry4.=$security['fraction'].",";
            $entry4.=$security['indxx_value'].",";
		
		}
		
		}

	$entry2=number_format($index_value,2,'.','').",\n";

 $insertQuery='INSERT into tbl_indxx_cs_value (indxx_id,code,indxx_value,date) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.number_format($index_value,2,'.','').'","'.$datevalue2.'")';
		$this->db->query($insertQuery);	
	
	if($open){   
 if(   fwrite($open,$entry1.$entry2.$entry3.$entry4))
{
	$this->log_info(log_file, "file writing done for ".$row['name']);
}}
		
		}
		 file_put_contents('../files/backup/postOPENCSIdata'.date("Y-m-d-H-i-s").'.json', json_encode($final_array));
		}
		
		
		$this->saveProcess(2);
		//$this->Redirect2("index.php?module=calcsl&date=" .date. "&log_file=" . basename(log_file),"","");	
		//$this->Redirect("index.php?module=calcftpclose","","");		
	}
		
	function calcsl()
	{
			if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));


		 if($_GET['log_file'])
			define("log_file", $_GET['log_file']);
				$this->log_info(log_file, "In Short and liveraged index File  generation  for live index");
		//echo "select * from tbl_indxx_cs  where status='1' ";
		$indxxs=$this->db->getResult("select * from tbl_indxx_sl  where status='1' ",true);	
		//$this->pr($indxxs);
		 //$datevalue2=date('Y-m-d',strtotime($this->_date)-86400);
		 $datevalue2=date;
		 
		 
		//  $datevalue2='2015-04-24';
		 $final_array=array();
		 if(!empty($indxxs))
		 {
			 
			 foreach($indxxs as $row)
			 {
				$this->log_info(log_file, "Preparing data for index : ".$row['name']);
				$final_array[$row['id']]=$row;
				
				
					$client=$this->db->getResult("select tbl_ca_client.ftpusername from tbl_ca_client where id='".$row['client_id']."'",false,1);	
					$final_array[$row['id']]['client']=$client['ftpusername'];
			//	echo "select * from tbl_sl_adj_factor  where ca_indxx_id='".$row['id']."' ";
				$calcfactors=$this->db->getResult("select * from tbl_sl_adj_factor  where cs_indxx_id='".$row['id']."' ",true);	
				
				$slindxx_value=$this->db->getResult("select indxx_value from tbl_indxx_sl_value  where indxx_id='".$row['id']."' order by dateAdded desc ",false,1);
				
				$liborrates=$this->db->getResult("select price from tbl_libor_prices  where ticker like '%LIBR360  Index%' and date ='".$datevalue2."' ",false,1);	
				$final_array[$row['id']]['libor_rate']=$liborrates['price'];
				$final_array[$row['id']]['last_index_value']=$slindxx_value['indxx_value'];
				if(!empty($calcfactors))
				{
				foreach($calcfactors as $key=> $calcfactor)
				{
				//$this->pr($calcfactor);
				
				
				$indxx_name=$this->db->getResult("select name from tbl_indxx  where code='".$calcfactor['code']."' ",false,1);
			//	echo "select indxx_value from tbl_indxx_value  where code='".$calcfactor['code']."' order by date desc ";
				$indxx_value=$this->db->getResult("select indxx_value,date from tbl_indxx_value  where code='".$calcfactor['code']."' and date<='".$datevalue2."' order by date desc ",false,2);
					$calcfactors[$key]['indxx_name']=$indxx_name['name'];
					$calcfactors[$key]['indxx_value']=$indxx_value;
				
				
				}
				}
				$final_array[$row['id']]['values']=$calcfactors;
				
				
			
			}
			 
			 
		 }
		 
	//$this->pr( $final_array,true);
		
		if(!empty($final_array))
		{  file_put_contents('../files/backup/preCLOSESLdata'.date("Y-m-d-H-i-s").'.json', json_encode($final_array));
		 	foreach($final_array as $key=>$closeIndxx)
		{
			if(!$closeIndxx['client'])
			$file="../files/ca-output/Closing-".$closeIndxx['code']."-".$datevalue2.".txt";
			else
			$file="../files/ca-output/".$closeIndxx['client']."/Closing-".$closeIndxx['code']."-".$datevalue2.".txt";
			
			$client_folder="../files/ca-output/".$closeIndxx['client']."/";
			if (!file_exists($client_folder))
			mkdir($client_folder, 0777, true);
			
			
			$open=fopen($file,"w+");
			
			$entry1='Date'.",";
			$entry1.=date("Y-m-d",strtotime($datevalue2)).",\n";
			$entry1.='Index Value'.",";
			$entry3='NAME'.",";
			$entry3.='CODE'.",";
			$entry3.='FACTOR'.",";
			$entry3.='Index Value'.",";
			
			
			$entry4='';
		$index_value=0;
		
			if(!empty($closeIndxx))
		{
		foreach($closeIndxx['values'] as $security)
		{
			
			//$this->pr($security);

//echo (1+($security['fraction']*(($security['indxx_value'][0]['indxx_value']/$security['indxx_value'][1]['indxx_value'])-1)));
	//		exit;
		$index_value=$closeIndxx['last_index_value']*(1+($security['fraction']*(($security['indxx_value'][0]['indxx_value']/$security['indxx_value'][1]['indxx_value'])-1)));
		//echo 	$index_value;
	//	exit;
		
            $entry4.= "\n".$security['indxx_name'].",";
            $entry4.=$security['code'].","; 
			 $entry4.=$security['fraction'].",";
            $entry4.=$security['indxx_value'][0]['indxx_value'].",";
		//	echo (($closeIndxx['last_index_value']*($security['fraction']-1)*($closeIndxx['libor_rate'])/100)/360);
			
		$newIndex_value=$index_value-(($closeIndxx['last_index_value']*($security['fraction']-1)*($closeIndxx['libor_rate'])/100)/360);
		}
		
		}

//echo $newIndex_value;
//exit;


	$entry2=number_format($newIndex_value,2,'.','').",\n";

 $insertQuery='INSERT into tbl_indxx_sl_value (indxx_id,code,indxx_value,date) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.number_format($newIndex_value,2,'.','').'","'.$datevalue2.'")';
		$this->db->query($insertQuery);	
	
	if($open){   
 if(   fwrite($open,$entry1.$entry2.$entry3.$entry4))
{
$this->log_info(log_file, "file writing done for ".$row['name']);
}}
		
		}
		 file_put_contents('../files/backup/postOPENSLdata'.date("Y-m-d-H-i-s").'.json', json_encode($final_array));
		}
		
		
		$this->saveProcess(2);
		
	
	}
	function checkivchange()
	{
	
	//	echo "deepak";
				if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));

	
	 if($_GET['log_file'])
			define("log_file", $_GET['log_file']);
				$this->log_info(log_file, "in check  Index Value change");
				
					
		$liveindexes=$this->db->getResult("SELECT id,name,code  FROM tbl_indxx WHERE status='1' and usersignoff='1' and dbusersignoff='1' and submitted='1'",true);
	//$this->pr($liveindexes,true);
	
	
	
	$indxxvaluesarray=array();
	
	if(!empty($liveindexes)){
	foreach($liveindexes as $key=>$value)
	{
		
		$indxxvaluesarray[$key]=$value;
		
		$liveindexvalues=$this->db->getResult("SELECT  date,indxx_value from tbl_indxx_value where tbl_indxx_value.indxx_id='".$value['id']."'order by date desc limit 0,2",true);
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
		$value1=$indxx['values'][0]['indxx_value'];
		$value2=$indxx['values'][1]['indxx_value'];
		$diff=100*(($value1-$value2)/$value2);
//echo $indxx['code']."=>".$diff;
//echo "<br>";
	
	if($diff>=5 || $diff<=-5)
	{
	$str.= $indxx['name']."(".$indxx['code'].") " .$diff."%<br/>";
		$this->log_info(log_file, "Change in Index Value ".$indxx['name']."(".$indxx['code'].") " .$diff."%");
	
	}
	
		
		}
		}
		}
		
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
			 
			//$emailsids.=',dbajpai@indxx.com';
			
			$msg='Hi <br>
			Index Value Change Notification <br/>
			'.$str." <br>Thanks <br>";
					
					// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			// Additional headers
			//$headers .= 'To: '.$dbuser['name'].' <'.$dbuser['email'].'>'. "\r\n";
$headers .= 'From: Indexing <indexing@indxx.com>' . "\r\n"."CC: indexing@indxx.com". "\r\n";
					//echo $emailsids;
						if(mail( $emailsids,"Index Values Change Notification",$msg,$headers))
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
		//$this->Redirect2("index.php?module=checkpvchange&date=" .date. "&log_file=" . basename(log_file),"","");	
		
	}
	
		function checkpvchange()
	{
		
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
		
		
		
		
		
	$str.= $indxx['ticker']."(".$indxx['values'][0]['isin'].") -".$indxx['values'][0]['curr']." " .$diff."% : ";
		$this->log_info(log_file, "Ticker Price value change ".$indxx['ticker']."(".$indxx['values'][0]['isin'].") -".$indxx['values'][0]['curr']." " .$diff."%") ." ";
	
	
	$indxxes=$this->db->getResult("SELECT name,code FROM `tbl_indxx` where id in (select indxx_id from tbl_indxx_ticker where ticker='".$indxx['ticker']."')",true);
		foreach($indxxes as $index)
		{
		$str.=$index['name']."(".$index['code']."), ";
		}
	$str.="<br>";
	
	}
	if($diff==0)
	{
		$str.= $indxx['ticker']."(".$indxx['values'][0]['isin'].") -".$indxx['values'][0]['curr']." " .$diff."% : ";
		$indxxes=$this->db->getResult("SELECT name,code FROM `tbl_indxx` where id in (select indxx_id from tbl_indxx_ticker where ticker='".$indxx['ticker']."')",true);
		foreach($indxxes as $index)
		{
		$str.=$index['name']."(".$index['code']."), ";
		}
	$str.="<br>";
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
			 
			//$emailsids.=',dbajpai@indxx.com';
			
			$msg='Hi <br>
			Local Price Change Notification <br/>
			'.$str." <br>Thanks <br>";
					
					// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			// Additional headers
			//$headers .= 'To: '.$dbuser['name'].' <'.$dbuser['email'].'>'. "\r\n";
$headers .= 'From: Indexing <indexing@indxx.com>' . "\r\n"."CC: indexing@indxx.com". "\r\n";
					//echo $emailsids;
					//echo $msg;
				//	exit;
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
		$this->update_process("Closing",date,"1");
		$url="publishcsixls.php?date=" .date. "&log_file=" . basename(log_file);
/*echo '<script>document.location.href="http://97.74.65.118/icai2/publishcsixls.php";</script>';
*/		//$this->Redirect("index.php?module=calcftpclose","","");		*/
	$link="<script type='text/javascript'>
window.open('".$url."');  
</script>";
echo $link;	
//		$this->Redirect2("../multicurrency2/db_backup.php?date=" .date. "&log_file=" . basename(log_file),"","");	
	}
}