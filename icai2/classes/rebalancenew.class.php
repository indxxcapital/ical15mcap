<?php

class Rebalancenew extends Application{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function index()
	{
		
		if($_GET['log_file'])
		define("log_file",get_logs_folder().$_GET['log_file']);
		if($_GET['date'])
		define("date",$_GET['date']);
		else
		define("date",date("Y-m-d"));
		
		
		$PublishDate=date("Y-m-d",strtotime(date)+86400);
		
		if(date("D",strtotime($PublishDate))=="Sat")
		{
			$PublishDate=date("Y-m-d",strtotime(date)+(3*86400));
		}
		
		
		$datevalue=date;
		$price_array=array();
		$mCap_array=array();

		log_info("In Prepare Transaction Data");
		
		$rebalanceDatesArray=$this->getRebalanceDatesForT();
		
		if(in_array($datevalue,$rebalanceDatesArray))
		
		{	
	
		$getIndexes=$this->db->getResult("SELECT * FROM tbl_indxx where 1=1");
		
		$query_price =$this->db->getResult("select isin,price from tbl_price where date='".$datevalue."'");
		if(!empty($query_price))
		{
			foreach($query_price as $row1 => $p)
			{
					$price_array[$p['isin']] = $p['price'];
			} 
		}
			
		$mcap_query =$this->db->getResult("select isin,mcap from tbl_marketcap where date='".$datevalue."'");
		if(!empty($mcap_query))
		{
			foreach($mcap_query as $row1 => $p)
			{
					$mCap_array[$p['isin']] = $p['mcap'];
			} 
		}
		
		$startDate=date("Y-m-d",strtotime(date)+86400);
		
		if(date("D",strtotime($startDate))=="Sat")
		{
			$startDate=date("Y-m-d",strtotime(date)+(3*86400));
		}
		
	
		
		if(!empty($getIndexes))
		{
			$this->db->Query("truncate tbl_indxx_temp");
			$this->db->Query("truncate tbl_indxx_ticker_temp");
			$this->db->Query("truncate tbl_indxx_value_temp");			
			$this->db->Query("truncate tbl_indxx_value_open_temp");			
			$this->db->Query("truncate tbl_final_price_temp");
			
		foreach($getIndexes as $key=>$indexDetails)
		{
			if(!empty($indexDetails))
			{
			$lastDayInvestmentValue=$this->getLastInvestmentValue($indexDetails['id']);
			$lastDayIndexValue=$this->getLastIndexValue($indexDetails['id']);
			
			if($lastDayIndexValue!=0 && $lastDayInvestmentValue!=0)
			{
				
				$ticker_inser_array=array();
				$price_inser_array=array();
				
				$this->db->Query("insert into tbl_indxx_temp set id='".$indexDetails['id']."',name='".$indexDetails['name']."',code='".$indexDetails['code']."',investmentammount='".$lastDayInvestmentValue."',	divpvalue='".$indexDetails['divpvalue']."',indexvalue='".$lastDayIndexValue."',	divisor='".$indexDetails['divisor']."',type='".$indexDetails['type']."',cash_adjust='".$indexDetails['cash_adjust']."',curr='".$indexDetails['curr']."',status='1',dateAdded='".$indexDetails['dateAdded']."',lastupdated='".$indexDetails['lastupdated']."',dateStart='".$startDate."',usersignoff='1',dbusersignoff='1',submitted='1',	finalsignoff='0',runindex='0',addtype='".$indexDetails['addtype']."',zone='".$indexDetails['zone']."',calcdate='".$indexDetails['calcdate']."',rebalance='".$indexDetails['rebalance']."',client_id='".$indexDetails['client_id']."',	display_currency='".$indexDetails['display_currency']."',ireturn='".$indexDetails['ireturn']."',	ica='".$indexDetails['ica']."',	recalc='".$indexDetails['recalc']."',div_type='".$indexDetails['div_type']."',currency_hedged='".$indexDetails['currency_hedged']."',priority='".$indexDetails['priority']."'");
				
				//$newTempID=mysql_insert_id();
				
				//$newTempID='1';
			
				$tickersArray=$this->geTickersforIndex($indexDetails['id']);
				$totalMcap=0;
				
				$weightsArray=$this->getTransactionWeights($indexDetails['id'],date);
				
				
					if(!empty($tickersArray))
					{
						foreach($tickersArray as $key=>$tickerDetails)
						{
							$totalMcap=$totalMcap+$mCap_array[$tickerDetails['isin']];
						}
				
				
				
				foreach($tickersArray as $key2=>$tickerDetails2)
				{
						
					
					if(!empty($weightsArray[$tickerDetails2['isin']]) && $weightsArray[$tickerDetails2['isin']]!="")
					$weight=$weightsArray[$tickerDetails2['isin']];
					else
					$weight=0;
					
					/*if($mCap_array[$tickerDetails2['isin']]!="")
					$weight=number_format($mCap_array[$tickerDetails2['isin']]/$totalMcap,15,'.','');
					else
					$weight=0;*/
					
					$ticker_inser_array[]="('".mysql_real_escape_string($tickerDetails2['name'])."','".mysql_real_escape_string($tickerDetails2['isin'])."','".mysql_real_escape_string($tickerDetails2['ticker'])."','".mysql_real_escape_string($weight)."','".$tickerDetails2['curr']."','".$tickerDetails2['divcurr']."','".mysql_real_escape_string($tickerDetails2['sedol'])."','".mysql_real_escape_string($tickerDetails2['cusip'])."','United States','".$indexDetails['id']."','1')";
					
					}
				
				}
				
				
				
				$Tickerquery="insert into tbl_indxx_ticker_temp (name,isin,ticker,weight,curr,divcurr,sedol,cusip,countryname,indxx_id,status) values ".implode(",",$ticker_inser_array).";";
$this->db->Query($Tickerquery);

			
//echo "Completed ".$indexDetails['code'];
			}
		}
			
		}
		
	//	exit;
		$indexes=array();
		$TempIndexes=$this->db->getResult("select id from tbl_indxx_temp where 1=1",true);
		foreach($TempIndexes as $key=>$indxx)
		{
				$indexes[]=$indxx['id'];
		
		}
		
		//$this->pr($indexes,true);
		$this->checkandconvertprice($indexes);

		$this->runindex($indexes);
		
		$this->goliveIndex($indexes);
		
		
	}
	}
	//exit;
		$this->Redirect2("index.php?module=calccapubnew&log_file=".basename(log_file)."&date=".$PublishDate);
	}
	
	function getRebalanceDatesForT()
	{
		 	$sql="SELECT date FROM tbl_rebalance_dates WHERE 1=1";
		  
		  $get_options = $this->db->getResult($sql,true);
		  
		  if ($get_options > 0) {
		foreach($get_options as $value) {
		$array[] = $value['date'];
		}
		  }
		  
		  return $array;	
	}
	
	
	function geTickersforIndex($indxx_id)
	{
			 $sql="SELECT * FROM tbl_indxx_ticker WHERE indxx_id='".$indxx_id."'";
		  
		  $get_options = $this->db->getResult($sql,true);
	
	
return $get_options;
	}
	
	function geIsinsforIndex($indxx_id,$date)
	{
		$sql="SELECT * FROM tbl_indxx_ticker WHERE indxx_id='".$indxx_id."'";
		  
		  $get_options = $this->db->getResult($sql,true);
		  
		  if ($get_options > 0) {
		foreach($get_options as $value) {
		$array[$value['isin']] = $value;
		}
	}
	
return $array;
	}
	
	function getNewTickersforIndex($indxx_id,$date)
	{
	$arrayfordeleted=array();
	$arrayforinserted=array();
	$NewWeightsArray=array();
	$totalTickers=array();
	
	
	
	$totalTickers=$this->geIsinsforIndex($indxx_id,$date);
	
	
	
	$sql="SELECT id FROM tbl_replacement WHERE indxx_id='".$indxx_id."' and date='".$date."'";
		  
	$get_options2 = $this->db->getResult($sql,true);
	
	
	$array=array();
	
	//$this->pr(  $get_options2,true);
	if(!empty($get_options2))
	{
		foreach($get_options2 as $replacement){
	$query='select ticker,isin from tbl_replacement_delete where replacement_id="'.$replacement['id'].'" and indxx_id="'.$indxx_id.'"';
	$tickers=$this->db->getResult($query,true);
	
	
	//$this->pr($tickers);
	if(!empty($tickers))
	{
	foreach($tickers as $ticker)
	{
	
	//$arrayfordeleted[$ticker['isin']]=$ticker['isin'];
	if(array_key_exists($ticker['isin'],$totalTickers))
	{	
		unset($totalTickers[$ticker['isin']]);
	}
	}
	}
	
	
	//$this->pr($arrayfordeleted);
		
		$query2='select isin,ticker from tbl_replacement_add where replacement_id="'.$replacement['id'].'" and indxx_id="'.$indxx_id.'"';
	$tickers2=$this->db->getResult($query2,true);
	
	if(!empty($tickers2))
	{
	foreach($tickers2 as $ticker)
	{
	
		$array[$ticker['isin']]['name']='';
		$array[$ticker['isin']]['isin']=$ticker['isin'];
		$array[$ticker['isin']]['ticker']=$ticker['ticker'];
		$array[$ticker['isin']]['curr']='';
		$array[$ticker['isin']]['divcurr']='';
		$array[$ticker['isin']]['sedol']='';
		$array[$ticker['isin']]['cusip']='';
		$array[$ticker['isin']]['countryname']='';
		$array[$ticker['isin']]['indxx_id']=$indxx_id;
		$array[$ticker['isin']]['status']='1';
		

	}
	$totalTickers=array_merge($totalTickers,$array);
	}
		
	//$this->pr($totalTickers);	
		}
		
		}
		
	
	
return $totalTickers;
	}

function getLastIndexValue($indxxid)
{
	 //$sql="SELECT value FROM tbl_indxx_value WHERE indxx_id='".$indxxid."' order by date desc limit 0,1";
	 
	 $sql="SELECT indxx_value FROM tbl_indxx_value WHERE indxx_id='".$indxxid."' order by date desc limit 0,1";
		  
	$get_options = $this->db->getResult($sql,false);
	
		  
	if ($get_options > 0) {
		$array=$get_options['indxx_value'];
	}
	else
	{
		$array=0;	
	}

return $array;	
}


function getLastInvestmentValue($indxxid)
{
	 //$sql="SELECT indxx_value FROM tbl_indxx_value WHERE indxx_id='".$indxxid."' order by date desc limit 0,1";
	 
	 $sql="SELECT market_value FROM tbl_indxx_value WHERE indxx_id='".$indxxid."' order by date desc limit 0,1";
		  
	$get_options = $this->db->getResult($sql,false);
	
		  
	if ($get_options > 0) {
		$array=$get_options['market_value'];
		//$array=$get_options['indxx_value'];
	}
	else
	{
		$array=0;	
	}

return $array;	
}	

function getTransactionWeights($indxx_id,$date)
{
		 $sql="SELECT ticker,weight FROM tbl_weights_transaction WHERE indxx_id='".$indxx_id."' and date='".$date."'";
		  
		  $get_options = $this->db->getResult($sql,true);
	//$this->pr($get_options,true);
	if ($get_options > 0) {
		foreach($get_options as $value) {
		$array[$value['ticker']] = $value['weight'];
		}
	}
	
return $array;
}	

function insetFinalPrice()
{
	$query_price =$this->db->getResult("select isin,price from tbl_price where date='2014-12-31'") or die("err".mysql_error());
			foreach($query_price as $row1 => $p)
			{
					$price_array[$p['isin']] = $p['price'];
			} 
			
	$query_share =$this->db->getResult("select * from tbl_weights_transaction where date='2014-12-31' and indxx_id ='55'") or die("err".mysql_error());
	//$this->pr($query_share,true);
			foreach($query_share as $row1 => $p)
			{
					mysql_query("insert into tbl_share set isin='".$p['ticker']."',indxx_id='".$p['indxx_id']."',share='".(100000*$p['weight'])/$price_array[$p['ticker']]."'");
			} 

	$indexes=$this->db->getResult("select * from tbl_indxx_ticker");
	foreach($indexes as $tickers)
	{
		//mysql_query("insert into tbl_final_price set isin='".$tickers['isin']."',indxx_id='".$tickers['indxx_id']."',price='".$price_array[$tickers['isin']]."',date='2014-12-31',localprice='".$price_array[$tickers['isin']]."',currencyfactor='1'");
	}
}

} // class ends here

?>