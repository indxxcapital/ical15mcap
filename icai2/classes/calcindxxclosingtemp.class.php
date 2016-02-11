<?php

class Calcindxxclosingtemp extends Application{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function index()
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
			$entry1.='Index value'.",";
			$entry3='Effective Date'.",";
			$entry3.='Ticker'.",";
			$entry3.='Name'.",";
			$entry3.='Isin'.",";
			$entry3.='Sedol'.",";
			$entry3.='Cusip'.",";
			$entry3.='Country'.",";
			$entry3.='Index share'.",";
			$entry3.='Weight'.",";
			$entry3.='Price'.",";
			
			if($closeIndxx['display_currency'])
			{$entry3.='Currency'.",";
			$entry3.='Currency factor'.",";
			}$entry4='';
			
			
			//$this->pr($closeIndxx);
			$oldindexvalue=$closeIndxx['index_value']['indxx_value'];
			$newindexvalue=0;
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
	 $newindexvalue=number_format((($marketValue)/$newDivisor),2,'.','');
}
else
 {$newindexvalue=number_format(($marketValue/$newDivisor),2,'.','');
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
 
		
		
	$insertQuery='INSERT into tbl_indxx_value_temp (indxx_id,code,market_value,indxx_value,date,olddivisor,newdivisor) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.$marketValue.'","'.$newindexvalue.'","'.$datevalue.'","'.$oldDivisor.'","'.$newDivisor.'")';
		$this->db->query($insertQuery);	
		
		if($open){   
 if(   fwrite($open,$entry1.$entry2.$entry3.$entry4))
{    

$insertlogQuery='INSERT into tbl_indxx_log_temp (type,indxx_id,value) values ("1","'.$closeIndxx['id'].'","'.mysql_real_escape_string($entry1.$entry2.$entry3.$entry4).'")';
		$this->db->query($insertlogQuery);
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
	$this->Redirect2("index.php?module=compositclose&date=" .date. "&log_file=" . basename(log_file),"","");		
		
		
	}
		
		
   
} 


	
?>