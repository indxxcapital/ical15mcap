<?php

class Transactiondata extends Application{

	function __construct()
	{
		parent::__construct();
		$this->checkUserSession();
		
$this->addCss('assets/data-tables/DT_bootstrap.css');
$this->addJs('assets/bootstrap/bootstrap.min.js');
$this->addJs('assets/nicescroll/jquery.nicescroll.min.js');
$this->addJs('assets/data-tables/jquery.dataTables.js');
$this->addJs('assets/data-tables/DT_bootstrap.js');
$this->addJs('js/flaty.js');
	}
	
	
	function index()
	{
		//if($_GET['log_file'])
		//define("log_file",get_logs_folder().$_GET['log_file']);
		if($_GET['date'])
		define("date",$_GET['date']);
		else
		define("date",date("Y-m-d"));

		$datevalue=date;
		$price_array=array();

		log_info("In Prepare Transaction Data");
		
		$getIndexes=$this->db->getResult("SELECT tbl_indxx.*,tbl_replacement.indxx_id,tbl_replacement.date FROM tbl_replacement left join tbl_indxx on tbl_indxx.id=tbl_replacement.indxx_id where tbl_replacement.date='".date."'");
		
		$query_price =$this->db->getResult("select isin,price from tbl_price where date='".$datevalue."'") or die("err".mysql_error());
			foreach($query_price as $row1 => $p)
			{
					$price_array[$p['isin']] = $p['price'];
			} 
		
		print_r($price_array);
		exit;
		
		foreach($getIndexes as $key=>$indexDetails)
		{
			$lastDayInvestmentValue=$this->getLastInvestmentValue($indexDetails['id']);
			$lastDayIndexValue=$this->getLastIndexValue($indexDetails['id']);
			
			if($lastDayIndexValue!=0 && $lastDayInvestmentValue!=0)
			{
				
				$ticker_inser_array=array();
				$price_inser_array=array();
				
				$this->db->Query("insert into tbl_indxx_temp set id='".$indexDetails['id']."',name='".$indexDetails['name']."',code='".$indexDetails['code']."',investmentammount='".$lastDayInvestmentValue."',	divpvalue='".$indexDetails['divpvalue']."',indexvalue='".$lastDayIndexValue."',	divisor='".$indexDetails['divisor']."',type='".$indexDetails['type']."',cash_adjust='".$indexDetails['cash_adjust']."',curr='".$indexDetails['curr']."',status='1',dateAdded='".$indexDetails['dateAdded']."',lastupdated='".$indexDetails['lastupdated']."',dateStart='".date("Y-m-d",strtotime(date)+86400)."',usersignoff='1',dbusersignoff='1',submitted='1',	finalsignoff='0',runindex='0',addtype='".$indexDetails['addtype']."',zone='".$indexDetails['zone']."',calcdate='".$indexDetails['calcdate']."',rebalance='".$indexDetails['rebalance']."',client_id='".$indexDetails['client_id']."',	display_currency='".$indexDetails['display_currency']."',ireturn='".$indexDetails['ireturn']."',	ica='".$indexDetails['ica']."',	recalc='".$indexDetails['recalc']."',div_type='".$indexDetails['div_type']."',currency_hedged='".$indexDetails['currency_hedged']."',priority='".$indexDetails['priority']."'");
				
				//$newTempID=mysql_insert_id();
				
				//$newTempID='1';
			
				$tickersArray=$this->geTickersforIndex($indexDetails['id']);
				
				foreach($tickersArray as $key=>$ticker)
				{
					$weightsArray=$this->getTransactionWeights($indexDetails['id'],date);
					
					if(!empty($weightsArray[$ticker['isin']]) && $weightsArray[$ticker['isin']]!="")
					$weight=$weightsArray[$ticker['isin']];
					else
					$weight=0;
					
					$ticker_inser_array[]="('".mysql_real_escape_string($ticker['id'])."','".mysql_real_escape_string($ticker['cmpname'])."','".mysql_real_escape_string($ticker['isin'])."','".mysql_real_escape_string($ticker['ticker'])."','".mysql_real_escape_string($weight)."','".mysql_real_escape_string($ticker['curr'])."','".mysql_real_escape_string($ticker['divcurr'])."','".mysql_real_escape_string($ticker['sedol'])."','".mysql_real_escape_string($ticker['cusip'])."','".mysql_real_escape_string($ticker['countryname'])."','".$indexDetails['id']."','1')";
					
					$price_inser_array[]="('".$indexDetails['id']."','".mysql_real_escape_string($ticker['isin'])."','".$datevalue."','".$price_array[$ticker['isin']]."','1','".$price_array[$ticker['isin']]."')";	
					
				
				}
				
				$Tickerquery="insert into tbl_indxx_ticker_temp (id,name,isin,ticker,weight,curr,divcurr,sedol,cusip,countryname,indxx_id,status) values ".implode(",",$ticker_inser_array).";";
$this->db->Query($Tickerquery);

				$Pricequery="insert into tbl_final_price_temp (indxx_id,isin,date,price,currencyfactor,localprice) values ".implode(",",$price_inser_array).";";
$this->db->Query($Pricequery);

$this->getNewTickersforIndex($indexDetails['id'],date);

//echo "Completed ".$indexDetails['code'];
			}
			
			
		}
		
		$TempIndexes=$this->db->getResult("select id from tbl_indxx_temp where 1=1",false);
		$this->pr($TempIndexes,true)
		
	}
	
	function geTickersforIndex($indxx_id)
	{
			 $sql="SELECT * FROM tbl_indxx_ticker WHERE indxx_id='".$indxx_id."'";
		  
		  $get_options = $this->db->getResult($sql,true);
	
	
return $get_options;
	}
	
	function getNewTickersforIndex($indxx_id,$date)
	{
	
	 $sql="SELECT id FROM tbl_replacement WHERE indxx_id='".$indxx_id."' and date='".$date."'";
		  
		  $get_options2 = $this->db->getResult($sql,true);
	$arrayfordeleted=array();
	$arrayforinserted=array();
	
	$array=array();
	
	//$this->pr(  $get_options2,true);
	if(!empty($get_options2))
	{
		foreach($get_options2 as $replacement){
	$query='select ticker_id from tbl_replacement_delete where replacement_id="'.$replacement['id'].'" and indxx_id="'.$indxx_id.'"';
	$tickers=$this->db->getResult($query,true);
	
	
	//$this->pr($tickers);
	if(!empty($tickers))
	{
	foreach($tickers as $ticker)
	{
	
	$arrayfordeleted[]=$ticker['ticker_id'];
	mysql_query("delete from tbl_indxx_ticker_temp where id='".$ticker['ticker_id']."' ");
	}
	}
		
		
		$query2='select isin,ticker from tbl_replacement_add where replacement_id="'.$replacement['id'].'" and indxx_id="'.$indxx_id.'"';
	$tickers2=$this->db->getResult($query2,true);
	
	if(!empty($tickers2))
	{
	foreach($tickers2 as $ticker)
	{
	
	$array[$ticker['ticker']]=$ticker['isin'];
		mysql_query("insert into   tbl_indxx_ticker_temp set ticker='".$ticker['ticker']."',isin='".$ticker['isin']."', indxx_id='".$indxx_id."' ");
	}
	}
		
		
		}
		
		}
		$deletedSecurtity='';
		if(!empty($arrayfordeleted))
		$deletedSecurtity=implode(',',$arrayfordeleted);
	//echo $deletedSecurtity;
	//exit;
	
	if($deletedSecurtity)
	$subQuery=' AND id not in ('.$deletedSecurtity.')';
	 $sql="SELECT ticker,isin FROM tbl_indxx_ticker_temp WHERE indxx_id='".$indxx_id."' ".$subQuery;
		  
		  $get_options = $this->db->getResult($sql,true);
	//$this->pr($get_options,true);
	if ($get_options > 0) {
		foreach($get_options as $value) {
		$array[$value['ticker']] = $value['isin'];
		}
	}
	

return $array;
	}

function getLastIndexValue($indxxid)
{
	 //$sql="SELECT value FROM tbl_indxx_value WHERE indxx_id='".$indxxid."' order by date desc limit 0,1";
	 
	 $sql="SELECT indxx_value FROM tbl_indxx_value WHERE indxx_id='".$indxxid."' order by date desc limit 0,1";
		  
	$get_options = $this->db->getResult($sql,false);
	
		  
	if ($get_options > 0) {
		$array=$get_options['value'];
	}
	else
	{
		$array=0;	
	}

return $array;	
}


function getLastInvestmentValue($indxxid)
{
	 $sql="SELECT indxx_value FROM tbl_indxx_value WHERE indxx_id='".$indxxid."' order by date desc limit 0,1";
	 
	 //$sql="SELECT market_value FROM tbl_indxx_value WHERE indxx_id='".$indxxid."' order by date desc limit 0,1";
		  
	$get_options = $this->db->getResult($sql,false);
	
		  
	if ($get_options > 0) {
		//$array=$get_options['market_value'];
		$array=$get_options['value'];
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



} // class ends here

?>