<?php

class Transactionprocess extends Application{

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

		log_info("In Process Transaction Data");
	//	echo "SELECT tbl_indxx.*,tbl_replacement.indxx_id,tbl_replacement.date FROM tbl_replacement left join tbl_indxx on tbl_indxx.id=tbl_replacement.indxx_id where tbl_replacement.date='".date."'";

		$getIndexes=$this->db->getResult("SELECT tbl_indxx.*,tbl_replacement.indxx_id,tbl_replacement.date FROM tbl_replacement left join tbl_indxx on tbl_indxx.id=tbl_replacement.indxx_id where tbl_replacement.date='".date."'");
		
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
			
		foreach($getIndexes as $key=>$indexDetails)
		{
			if(!empty($indexDetails))
			{
			$lastDayInvestmentValue=$this->getLastInvestmentValue($indexDetails['id']);
			$lastDayIndexValue=$this->getLastIndexValue($indexDetails['id']);
			
			if($lastDayIndexValue!=0 && $lastDayInvestmentValue!=0)
			{
			
				$tickersArray=$this->getNewTickersforIndex($indexDetails['id'],date,$indexDetails['code']);
				
				echo "Completed ".$indexDetails['code'];
			}
		}
			
		}
		
	
		
		
	}
	
	$this->Redirect2("index.php?module=rebalancenew&log_file=".basename(log_file)."&date=".date);
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
	
		
	function getNewTickersforIndex($indxx_id,$date,$indxx_code)
	{
	
	 $sql="SELECT id FROM tbl_replacement WHERE indxx_id='".$indxx_id."' and date='".$date."'";
		  
		  $get_options2 = $this->db->getResult($sql,true);
		  
		  $AllTickers=$this->geTickersforIndex($indxx_id);
		
		  //echo $totalTMcap;
		  //exit;
	
	$arrayfordeleted=array();
	$arrayforinserted=array();
	
	$array=array();
	
	//$this->pr(  $get_options2,true);
	if(!empty($get_options2))
	{
		foreach($get_options2 as $replacement){
	$query='select isin from tbl_replacement_delete where replacement_id="'.$replacement['id'].'" and indxx_id="'.$indxx_id.'"';
	$tickers=$this->db->getResult($query,true);
	
	
	//$this->pr($tickers,true);
	if(!empty($tickers))
	{
	foreach($tickers as $ticker)
	{
$ticker['isin']=	trim($ticker['isin']);
	$arrayfordeleted[]=$ticker['isin'];
	//echo "select * from tbl_indxx_ticker where isin like '%".$ticker['isin']."%' and indxx_id='".$indxx_id."'";
//exit;	
	mysql_query("delete from tbl_indxx_ticker where isin like '%".$ticker['isin']."%' and indxx_id='".$indxx_id."'");
	mysql_query("delete from tbl_share where isin like '%".$ticker['isin']."%' and indxx_id='".$indxx_id."'");
	mysql_query("delete from tbl_final_price where isin  like '%".$ticker['isin']."%' and indxx_id='".$indxx_id."' and date='".$date."'");
	mysql_query("delete from tbl_weights where isin like '%".$ticker['isin']."%' and indxx_id='".$indxx_id."' and date='".$date."'");
	}
	}
		
		
		$query2='select isin,ticker from tbl_replacement_add where replacement_id="'.$replacement['id'].'" and indxx_id="'.$indxx_id.'"';
	$tickers2=$this->db->getResult($query2,true);
	
	if(!empty($tickers2))
	{
	foreach($tickers2 as $ticker)
	{
		
		
			  
	$lastDayInvestmentValue=$this->getLastInvestmentValue($indxx_id);
	$IndexDetails=$this->db->getResult("select * from tbl_indxx_value where indxx_id='".$indxx_id."' and date='".$date."'");
	//$IndexDetails=$this->db->getResult("select * from tbl_indxx_value where indxx_id='".$indxx_id."' and date='2015-02-11'");
	$oldDivisor=$IndexDetails['olddivisor'];
	$IndexValue=$IndexDetails['indxx_value'];
	$newMarketCap=0;
	$newDivisor=0;	
	$newIndexValue=0;
		  
		
		
		
	$weight=0;
	$weightsArray=$this->getTransactionWeightsForIsin($indxx_id,$date,$ticker['isin']);
	//$weightsArray=$this->getTransactionWeightsForIsin($indxx_id,"2015-02-11",$ticker['isin']);
	if(!empty($weightsArray) && $weightsArray[$ticker['isin']])
	{
		$weight=$weightsArray[$ticker['isin']];
	}
	else
	{
		echo "Weight not available for isin ".$ticker['isin'];
		exit;	
	}
	$tickercurr="";
	$price=0;
	$shares=0;
	
	$tickercurrArray=$this->getCurrencyForIsin($ticker['isin']);
	if(!empty($tickercurrArray) && $tickercurrArray[$ticker['isin']]!="")
	{	
		$tickercurr=$tickercurrArray[$ticker['isin']];
	}
	else
	{
		echo "Ticker Currency not available for isin ".$ticker['isin'];
		exit;
		//$tickercurr="USD";	
	}
	
	$this->checkandconvertpriceForIndex($indxx_id,$ticker['isin'],$tickercurr);
	
	//$priceArray=$this->getLastDayPrice($ticker['isin'],"2015-02-11");
	$priceArray=$this->getLastDayPrice($ticker['isin'],$date);
	if(!empty($priceArray) && $priceArray[$ticker['isin']]!="")
	{	
		$price=$priceArray[$ticker['isin']];
	}
	else
	{
		echo "Price Not Available for isin ".$ticker['isin'];
		exit;	
	}
	
	if($price!=0 && $weight!=0)
	{
		$shares=number_format(($lastDayInvestmentValue*$weight)/$price,15,'.','');
		
		if($shares!=0)
		{
			mysql_query("insert into tbl_share set isin='".$ticker['isin']."',share='".$shares."', indxx_id='".$indxx_id."', date='".date."'");
			mysql_query("insert into   tbl_weights set code='".$indxx_code."',date='".$date."',share='".$shares."',price='".$price."',weight='".$weight."',isin='".$ticker['isin']."',indxx_id='".$indxx_id."'");
			
		}
		
			mysql_query("insert into   tbl_indxx_ticker set ticker='".$ticker['ticker']."',isin='".$ticker['isin']."', indxx_id='".$indxx_id."', weight='".$weight."',curr='".$tickercurr."', divcurr='".$tickercurr."', countryname='United States'");
		
		
	}
	
	 	$query = "SELECT it.id, it.name, it.isin, it.ticker, it.curr, it.sedol, it.cusip, it.countryname, fp.localprice, 
						fp.currencyfactor, fp.price as calcprice, sh.share as calcshare 
						FROM `tbl_indxx_ticker` it left join tbl_final_price fp on fp.isin=it.isin 
						left join tbl_share sh on sh.isin=it.isin 
						where it.indxx_id='" . $indxx_id . "' and fp.indxx_id='" .$indxx_id . "' and sh.indxx_id='" .$indxx_id . "' 
						 and fp.date='" . $date . "'";
		
	
						 
		/*$query = "SELECT it.id, it.name, it.isin, it.ticker, it.curr, it.sedol, it.cusip, it.countryname, fp.localprice, 
						fp.currencyfactor, fp.price as calcprice, sh.share as calcshare 
						FROM `tbl_indxx_ticker` it left join tbl_final_price fp on fp.isin=it.isin 
						left join tbl_share sh on sh.isin=it.isin 
						where it.indxx_id='" . $indxx_id . "' and fp.indxx_id='" .$indxx_id . "' and sh.indxx_id='" .$indxx_id . "' 
						 and fp.date='2015-02-11'";*/
		
		
			$indxxprices=	$this->db->getResult($query,true);
			if(!empty($indxxprices))
			{
			
			foreach($indxxprices as $key=>$indexTickers)
			{
				if(!empty($indexTickers))
				$newMarketCap=$newMarketCap+($indexTickers['calcprice']*$indexTickers['calcshare']);	
			}
			}
			if($oldDivisor!=0&&$IndexValue!=""&&$IndexValue!=0.00&&$newMarketCap!=""&&$newMarketCap!=0)
			{
				$newDivisor=$oldDivisor-(($lastDayInvestmentValue-$newMarketCap)/$IndexValue);
				$newDivisor=number_format($newDivisor,12,'.','');
				$newIndexValue=$newMarketCap/$newDivisor;
				
				mysql_query("update tbl_indxx_value set market_value='".$newMarketCap."',indxx_value='".$newIndexValue."',olddivisor='".$newDivisor."', newdivisor='".$newDivisor."' where indxx_id='".$indxx_id."' and date='".$date."'");	
				//mysql_query("update tbl_indxx_value set market_value='".$newMarketCap."',indxx_value='".$newIndexValue."',olddivisor='".$newDivisor."', newdivisor='".$newDivisor."' where indxx_id='".$indxx_id."' and date='2015-02-11'");
				
				
			}
	
	}
	}
		
		
		}
		
		}
	
	
	}






function checkandconvertpriceForIndex($indxx,$isin,$tickerCurrency){
//$this->pr($indexes,true);
//$lastrundatearray=$this->db->getResult("SELECT date FROM `tbl_system_task_complete` where name='Closing' and status='1' order by date desc",false,1);

$lastrundatearray=$this->db->getResult("SELECT date FROM `tbl_system_task_complete` where name='Closing' order by date desc",false,1);

//$this->pr($lastrundatearray,true);
$lastrundate=$lastrundatearray['date'];
if(!empty($lastrundate))
{
	
$prices=$this->getPricesForIndex($lastrundate);
$currPrices=$this->getcurrPricesForIndex($lastrundate);


$final_price_array	=	array();
	$indexarray			=	array();
	
	$index_query =	mysql_query("SELECT id, name, code, curr, currency_hedged FROM `tbl_indxx` 
								WHERE `status` = '1' AND `submitted` = '1'  and id='".$indxx."'");
	
	if (!($err_code = mysql_errno()))
	{
		while(false != ($index = mysql_fetch_assoc($index_query)))
		{
			$index_id = $index['id'];
		
				$row = 0;
				
					
					
					if(!in_array($isin,array_keys($prices)))
						{$p=$this->getLastPrice($isin);
							if($p)
							{	$prices[$isin]=$p;
							}else{
									$prices[$isin]['price']=0;
									echo "Price 0 for isin".$isin;
									exit;
									mail($_SESSION['User']['email'],"price 0 of ticker ".$isin,"Price of Input Ticker ".$isin." is Zero");
							}
						}
					
					$currencyPrice = 0;
					log_info("	Processing security isin = " .$isin);
						
					/*
					 * Check if got the right currency for the security from Bloomberg.
					 * If not, raise alert and disable this index.
					 */
					if($prices[$isin]['curr'] != $tickerCurrency)
					{
						echo ("	Currency mismatch for index=" .$indxx. "[localcurrency="
								.$prices[$isin]['curr']. "][ticker_curr=" .$tickerCurrency. "]");
	//mail($_SESSION['User']['email'],"currency mismatch icalC 1.4","Currency mismatch for Ticker=" .$priceRow['ticker']. "[localcurrency=" 
									//.$prices[$isin]['curr']. "][ticker_curr=" .$priceRow['ticker_currency']. "]");
						$indexarray[$index_id] = $isin;
						break;
					}
					else
					{
						$currencyPrice = 1;
						
						
						
						$final_price_array[$index_id][$row]['price'] = $prices[$isin]['price'];

						if($index['curr'] && ($index['curr'] != $prices[$isin]['curr']))
						{
							$cfactor_code = $index['curr'].$prices[$isin]['curr'];
//echo $index['curr'].$prices[$priceRow['ticker']]['curr'];
								$cfactor=$currPrices[strtoupper($index['curr'].$prices[$isin]['curr'])]['price'];
							if(!$cfactor){
							$newCurrPrice= $this->getPriceforCurrency5($index['curr'],$prices[$isin]['curr'],$lastrundate);
							if(!empty($newCurrPrice))
							{$currPrices[$index['curr'].$prices[$isin]['curr']]=$newCurrPrice;
						$cfactor=$currPrices[$index['curr'].$prices[$isin]['curr']]['price'];
						}else{
							$indexarray[$index_id] = $isin;
							break;
						}
							
							}
							$currencyPrice=$cfactor;
							$final_price_array[$index_id][$row]['price'] = $prices[$isin]['price']/$cfactor;

							if($prices[$isin]['curr']=="KWd")
                                $final_price_array[$index_id][$row]['price'] /= 1000;
							elseif(strcmp($cfactor_code,strtoupper($cfactor_code)))
								$final_price_array[$index_id][$row]['price'] /= 100;
						}
					
						$final_price_array[$index_id][$row]['isin'] = $isin;
						$final_price_array[$index_id][$row]['localprice'] = $prices[$isin]['price'];
						$final_price_array[$index_id][$row]['currencyfactor'] = $currencyPrice;
					}
					$row++;
				
				/* Free the security table for this index */
				mysql_free_result($res);
			
		}

		/* Remove duplicates from the array */
		$indexarray = array_unique($indexarray);
			
		/* Send email for faulty indexes and de-activate the same. */
		foreach($indexarray as $keyindex => $valueindex)
		{
			//send_index_deactivation_mail($keyindex, $valueindex, "UPCOMING");
			
			/* De-activate this index */
			echo "Index Id :".$keyindex ." deactivated";
			//mail($_SESSION['User']['email']," Upcoming Index Deactivated","Price Conversion failed ,Index Id :".$keyindex ." deactivated");
	
			unset($final_price_array[$keyindex]);
			
				
			
		}

//$this->pr($final_price_array,true);

		/* Update tbl_final_price table for rest of the indexes */
		if(!empty($final_price_array))
		{
			foreach($final_price_array as $indxx_id => $ival)
			{
				if(!empty($ival))
				{
					$query="INSERT into tbl_final_price
									(indxx_id, isin, date, price, localprice, currencyfactor) values";
				$array_price_value=array();
					foreach($ival as $tempKey=>$ivalue)
					{
						$array_price_value[]="('" . $indxx_id . "','" . $ivalue['isin'] . "','" . $lastrundate. "',
									 '" . $ivalue['price'] . "','" . $ivalue['localprice'] . "', '" . $ivalue['currencyfactor'] . "')";
						
					}
					
					
					 $query.=implode(",",$array_price_value).";";
					//exit;
					mysql_query($query);
		
						
				}
				unset($final_price_array[$indxx_id]);
			}
			unset($final_price_array);
		}
		mysql_free_result($index_query);
	}
		






	

	
}

//exit;
}


function getLastDayPrice($isin,$date){
	
$security_values = mysql_query("SELECT price from tbl_final_price where isin ='" .$isin."' and date='".$date."'");
								
								$array=array();
if(mysql_num_rows($security_values)>0)
{
while($row=mysql_fetch_assoc($security_values))
{
$array[$isin]=$row['price'];

}

}
	return $array;	
	
}


function getPricesForIndex($date){

$security_values = mysql_query("SELECT ticker,price, isin,curr from tbl_prices_local_curr where date ='" .$date. 
								"'");
								
								$array=array();
if(mysql_num_rows($security_values)>0)
{
while($row=mysql_fetch_assoc($security_values))
{
$array[$row['isin']]=$row;

}

}
	return $array;							
}





function getcurrPricesForIndex($date){
	
$security_values = mysql_query("SELECT currencyticker,price,currency from tbl_curr_prices where date ='" .$date. 
								"'");
								
								$array=array();
if(mysql_num_rows($security_values)>0)
{
while($row=mysql_fetch_assoc($security_values))
{
$array[str_replace("  Curncy",'',$row['currencyticker'])]=$row;

}

}

if(!empty($array))
{
	foreach($array as $key=>$value)
	{
	
$var=	str_split($key,3);
//print_r($var);

if(!array_key_exists($var[1].$var[0],$array))
{$array[$var[1].$var[0]]['price']=1/$value['price'];
$array[$var[1].$var[0]]['currency']=$var[0];
$array[$var[1].$var[0]]['currencyticker']=$value['currencyticker'];
}	}
}
//exit;
	return $array;							
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



function getTransactionWeightsForIsin($indxx_id,$date,$isin)
{
	
		 $sql="SELECT weight FROM tbl_weights_transaction WHERE indxx_id='".$indxx_id."' and date='".$date."' and ticker='".$isin."'";
		  
		  $get_options = $this->db->getResult($sql,true);
	//$this->pr($get_options,true);
	if ($get_options > 0) {
		foreach($get_options as $value) {
		$array[$isin] = $value['weight'];
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