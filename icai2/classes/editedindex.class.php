<?php

class Editedindex extends Application{

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
function index(){
	
	$this->_baseTemplate="inner-template";
	$this->_bodyTemplate="editedindex/index";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
//	$this->addfield();

		$this->smarty->assign('pagetitle','Index');
		$this->smarty->assign('bredcrumssubtitle','Index');
	

		$error="";
		
		if(!empty($_POST))
		{
		//
		if($_POST['runindex'] && !empty($_POST['checkboxid']))
		{
		
		$error=$this->checkformarketCap($_POST['checkboxid']);
	if(!$error)
{$this->checkandconvertprice($_POST['checkboxid']);

$this->runindex($_POST['checkboxid']);
$this->calcMarketCap($_POST['checkboxid']);
}
		//$this->pr($_POST,true);
		}elseif($_POST['golive'] && !empty($_POST['checkboxid'])){
		
	$this->goliveIndex($_POST['checkboxid']);
		
		}else{
		
		$this->setMessage("Invalid Request!","error");
		}
		}
		

		//$this->pr($_POST,true);
	$usertempindexes=$this->db->getResult("select distinct(indxx_id) as indxx from tbl_assign_index_temp where user_id='".$_SESSION['User']['id']."' ",true);
	$_SESSION['IndexTemp']=array();
		$this->setUserTempIndexSessionData($usertempindexes);
	
//$this->pr($_SESSION,true);
$indexdata=array();
if($_SESSION['User']['type']=='2' )
{
	if(!empty($_SESSION['IndexTemp']))
{$ids=$ids = join(',',$_SESSION['IndexTemp']); 
//$this->pr($_SESSION,true);
$indexdata=$this->db->getResult("select tbl_indxx_temp.*,tbl_ca_client.name as clientname,( select count(id) from tbl_indxx_ticker_temp where indxx_id=tbl_indxx_temp.id) as total_ticker from tbl_indxx_temp left join tbl_ca_client on tbl_ca_client.id=tbl_indxx_temp.client_id where tbl_indxx_temp.id in (".$ids .") and recalc='1'   ",true);
}
}
else{	


	$indexdata=$this->db->getResult("select tbl_indxx_temp.*,tbl_ca_client.name as clientname,( select count(id) from tbl_indxx_ticker_temp where indxx_id=tbl_indxx_temp.id) as total_ticker from tbl_indxx_temp left join tbl_ca_client on tbl_ca_client.id=tbl_indxx_temp.client_id  where 1=1  and recalc='1' ",true);
}
if($error)
$this->setMessage("Market Cap is not avaliavble as of Pre Closing date of following Tickers <br>".$error,"error");

		$this->smarty->assign("indexdata",$indexdata);

		//$this->pr($_SESSION);
		 $this->show();
	
}

function calcMarketCap($indexes){
	
	$array=array();
//$flag=false;
if(!empty($indexes))
{
foreach($indexes as $indxx)
{
	
		$indxxdata=$this->db->getResult("select code,calcdate from tbl_indxx_temp  where status='1' and id='".$indxx."'",false);	
		$date=$indxxdata['calcdate'];
$mcap=$this->getMarketCapforDate($date);
//echo "<br>";
$tickers=$this->db->getResult("select isin,ticker from tbl_indxx_ticker_temp  where indxx_id='".$indxx."'",true);
if(!empty($tickers))
{
foreach($tickers as $isin)
{
	

//$text.=$isin['ticker'].",".$isin['isin']."<br>";
$this->db->query("insert into tbl_marketcap_close_temp(indxx_id,isin,date,marketcap) values ('".$indxx."','".$isin['isin']."','".$date."','".$mcap[$isin['isin']]."')");


}
}

}
}
//echo $text;


}




function checkformarketCap($indexes){
$text='';
$array=array();
$flag=false;
if(!empty($indexes))
{
foreach($indexes as $indxx)
{
	
		$indxxdata=$this->db->getResult("select code,calcdate from tbl_indxx_temp  where status='1' and id='".$indxx."'",false);	
		$date=$indxxdata['calcdate'];
$mcap=$this->getMarketCapforDate($date);

//echo "<br>";
$tickers=$this->db->getResult("select isin,ticker from tbl_indxx_ticker_temp  where indxx_id='".$indxx."'",true);
if(!empty($tickers))
{
foreach($tickers as $isin)
{
	
	
if(!isset($mcap[$isin['isin']]))
{
//$text.=$isin['ticker'].",".$isin['isin']."<br>";

$array[$isin['isin']]=$isin['ticker'];
$flag=true;
}
}
}

}
}
//echo $text;
if($flag){
foreach($array as $key=>$ticker)
{
$text.=$ticker.",".$key."<br>";
}
return $text;
}
}




function runindex($indexes)
{
//$lastrundatearray=$this->db->getResult("SELECT date FROM `tbl_system_task_complete` where name='Closing' and status='1' order by date desc",false,1);
//$this->pr($lastrundate,true);
//$lastrundate=$lastrundatearray['date'];
//$datevalue=$lastrundate;
if(!empty($indexes))
{
foreach($indexes as $indxx){
	$indxxs=$this->db->getResult("select tbl_indxx_temp.* from tbl_indxx_temp  where status='1' and usersignoff='1' and dbusersignoff='1' and submitted='1' and id='".$indxx."'",true);	
//$this->pr($indxxs,true);

$final_array=array();
		
		if(!empty($indxxs))
		{
			foreach($indxxs as $row)
			{
	//$this->pr($indxx);
					
			$lastrundate=$row['calcdate'];
$datevalue=$lastrundate;		
					
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
							and fp.indxx_id='" . $row['id'] . "'  and fp.date='" . $lastrundate. "'";			
		
		
			$indxxprices=	$this->db->getResult($query,true);	
		//$this->pr($indxxprices,true);
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
			$datevalue= $closeIndxx['calcdate'];
			
			$file="../files/ca-output_upcomming/pre-closing-".$closeIndxx['code']."-".$closeIndxx['dateStart']."-".$datevalue.".txt";

			$open=fopen($file,"w+");

			$entry1='Date'.",";
			$entry1.=$datevalue.",\n";
			$entry1.='Index value'.",";
			$entry3='Effective Date'.",";
			$entry3.='Ticker'.",";
			$entry3.='Name'.",";
			$entry3.='Isin'.",";
			$entry3.='Sedol'.",";
			$entry3.='Cusip'.",";
			$entry3.='Country'.",";
			$entry3.='Index shares'.",";
			$entry3.='Weight'.",";
			$entry3.='Price'.",";
			$entry3.='Currency'.",";
			$entry3.='Currency factor'.",";
			$entry4='';
			
			
			//$this->pr($closeIndxx);
			$oldindexvalue=$closeIndxx['index_value']['indxx_value'];
			$newindexvalue=0;
			$oldDivisor=$closeIndxx['index_value']['newdivisor'];
			$newDivisor=$oldDivisor;
			$marketValue=0;
			$sumofDividendes=0;
			$shareinsertArray=array();
			$weightinsertArray=array();
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
			
			$weightinsertArray[]="('".$closeIndxx['id']."','".$closeIndxx['code']."','".$datevalue."','".$closeprices['calcprice']."','".$closeprices['calcshare']."','".$weightValue."','".$closeprices['isin']."')";
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

if(!empty($weightinsertArray)){
		$this->db->query("insert into tbl_weights_temp (indxx_id,code,date,share,price,weight,isin) values ".implode(",",$weightinsertArray).";");
		$this->db->query("insert into tbl_weights_open_temp (indxx_id,code,date,share,price,weight,isin) values ".implode(",",$weightinsertArray).";");
		}

		
	 $insertQuery='INSERT into tbl_indxx_value_temp (indxx_id,code,market_value,indxx_value,date,olddivisor,newdivisor) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.$marketValue.'","'.$newindexvalue.'","'.$datevalue.'","'.$oldDivisor.'","'.$newDivisor.'")';
		$this->db->query($insertQuery);	
		$insertQuery='INSERT into tbl_indxx_value_open_temp (indxx_id,code,market_value,indxx_value,date,olddivisor,newdivisor) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.$marketValue.'","'.$newindexvalue.'","'.$datevalue.'","'.$oldDivisor.'","'.$newDivisor.'")';
		$this->db->query($insertQuery);	
		
		if($open){   
 if(   fwrite($open,$entry1.$entry2.$entry3.$entry4))
{
//echo "update tbl_indxx_temp set runindex='1',finalsignoff='1' where tbl_indxx_temp.id='".$indxxKey."'";
//exit;
	$query=$this->db->Query("update tbl_indxx_temp set runindex='1',finalsignoff='1' where tbl_indxx_temp.id='".$indxxKey."'");
	
	        fclose($open);

 $filetext= "file Written for ".$closeIndxx['code']."<br>";

}
}  
}else{
mail($_SESSION['User']['email'],"Run Index not  done","Runindex not done for ".$closeIndxx['code']);

}
		
		unset($final_array[$indxxKey]);
		}
	}
}
}
}  //end of function runindex



function checkandconvertprice($indexes){
//$this->pr($indexes,true);
//$lastrundatearray=$this->db->getResult("SELECT date FROM `tbl_system_task_complete` where name='Closing' and status='1' order by date desc",false,1);
//$this->pr($lastrundate,true);
//$lastrundate=$lastrundatearray['date'];
if(!empty($indexes))
{
foreach($indexes as $indxx){
//echo $indxx;

$indxxdata=$this->db->getResult("select * from tbl_indxx_temp where id='".$indxx."'",false);
$lastrundate=$indxxdata['calcdate'];


//echo "select * from tbl_final_price_temp where date='".$lastrundate."' and indxx_id='".$indxx."'";
$result=$this->db->getResult("select * from tbl_final_price_temp where date='".$lastrundate."' and indxx_id='".$indxx."'");
if(!empty($result))
{
//$this->pr($result);
//echo "price already converted";
//	$this->getPrices($lastrundate);
}else{
$prices=$this->getPrices($lastrundate);
$currPrices=$this->getcurrPrices($lastrundate);
//$this->pr($currPrices);

$final_price_array	=	array();
	$indexarray			=	array();
	
	$index_query =	mysql_query("SELECT id, name, code, curr, currency_hedged FROM `tbl_indxx_temp` 
								WHERE `status` = '1' AND `submitted` = '1'  and id='".$indxx."'");
	
	if (!($err_code = mysql_errno()))
	{
		while(false != ($index = mysql_fetch_assoc($index_query)))
		{
			$index_id = $index['id'];
			//log_info("Processing upcoming index = " .$index_id);
				
			/* Check if given index is local currency hedged index or not. */
			$convert_flag = false;
			if($index['currency_hedged'] == 1)
			{
				/* TODO: Check this logic and why this table is used instead of tbl_indxx_ticker */
				if (false != ($res = mysql_query("Select date from tbl_final_price_temp 
													where indxx_id = '".$index_id."' order by date desc limit 0, 1")))
				{
					if(!mysql_num_rows($res))
						$convert_flag = true;
				}
				
				mysql_free_result($res);
			}
			else
			{
				$convert_flag = true;
			}
			
			
			if($convert_flag)
			{
				$res = mysql_query("SELECT it.isin, it.ticker, 
									it.curr as ticker_currency
									FROM tbl_indxx_ticker_temp it  
									where it.indxx_id='".$index_id."' ");

				log_info("	Securities in index = " .mysql_num_rows($res));
				
				
				$row = 0;
				while(false != ($priceRow = mysql_fetch_assoc($res)))
				{
					
					
					if(!in_array($priceRow['isin'],array_keys($prices)))
						{$p=$this->getLastPrice($priceRow['isin']);
							if($p)
							{	$prices[$priceRow['isin']]=$p;
							}else{
									$prices[$priceRow['isin']]['price']=0;
									mail($_SESSION['User']['email'],"price 0 of ticker ".$priceRow['ticker'],"Price of Input Ticker ".$priceRow['ticker']." is Zero");
							}
						}
					
					$currencyPrice = 0;
					log_info("	Processing security isin = " .$priceRow['isin']);
						
					/*
					 * Check if got the right currency for the security from Bloomberg.
					 * If not, raise alert and disable this index.
					 */
					if($prices[$priceRow['isin']]['curr'] != $priceRow['ticker_currency'])
					{
						mail_info("	Currency mismatch for index=" .$index_id. "[localcurrency="
								.$prices[$priceRow['isin']]['curr']. "][ticker_curr=" .$priceRow['ticker_currency']. "]");
	mail($_SESSION['User']['email'],"currency mismatch icalC 1.4","Currency mismatch for Ticker=" .$priceRow['ticker']. "[localcurrency=" 
									.$prices[$priceRow['isin']]['curr']. "][ticker_curr=" .$priceRow['ticker_currency']. "]");
						$indexarray[$index_id] = $priceRow['ticker'];
						break;
					}
					else
					{
						$currencyPrice = 1;
						
						
						
						$final_price_array[$index_id][$row]['price'] = $prices[$priceRow['isin']]['price'];

						if($index['curr'] && ($index['curr'] != $prices[$priceRow['isin']]['curr']))
						{
							$cfactor_code = $index['curr'].$prices[$priceRow['isin']]['curr'];
//echo $index['curr'].$prices[$priceRow['ticker']]['curr'];
								$cfactor=$currPrices[strtoupper($index['curr'].$prices[$priceRow['isin']]['curr'])]['price'];
							if(!$cfactor){
							$newCurrPrice= $this->getPriceforCurrency5($index['curr'],$prices[$priceRow['isin']]['curr'],$lastrundate);
							if(!empty($newCurrPrice))
							{$currPrices[$index['curr'].$prices[$priceRow['isin']]['curr']]=$newCurrPrice;
						$cfactor=$currPrices[$index['curr'].$prices[$priceRow['isin']]['curr']]['price'];
						}else{
							$indexarray[$index_id] = $priceRow['ticker'];
							break;
						}
							
							}
							$currencyPrice=$cfactor;
							$final_price_array[$index_id][$row]['price'] = $prices[$priceRow['isin']]['price']/$cfactor;

							if($prices[$priceRow['isin']]['curr']=="KWd")
                                $final_price_array[$index_id][$row]['price'] /= 1000;
							elseif(strcmp($cfactor_code,strtoupper($cfactor_code)))
								$final_price_array[$index_id][$row]['price'] /= 100;
						}
					
						$final_price_array[$index_id][$row]['isin'] = $priceRow['isin'];
						$final_price_array[$index_id][$row]['localprice'] = $prices[$priceRow['isin']]['price'];
						$final_price_array[$index_id][$row]['currencyfactor'] = $currencyPrice;
					}
					$row++;
				}
				/* Free the security table for this index */
				mysql_free_result($res);
			}
		}

		/* Remove duplicates from the array */
		$indexarray = array_unique($indexarray);
			
		/* Send email for faulty indexes and de-activate the same. */
		foreach($indexarray as $keyindex => $valueindex)
		{
			//send_index_deactivation_mail($keyindex, $valueindex, "UPCOMING");
			
			/* De-activate this index */
			
			mail($_SESSION['User']['email']," Upcoming Index Deactivated","Price Conversion failed ,Index Id :".$keyindex ." deactivated");
	
			unset($final_price_array[$keyindex]);
			mysql_query("update tbl_indxx_temp set status = '0' where id = '" . $keyindex . "'");
				
			
		}

		/* Update tbl_final_price table for rest of the indexes */
		if(!empty($final_price_array))
		{
			foreach($final_price_array as $indxx_id => $ival)
			{
				if(!empty($ival))
				{
					$query="INSERT into tbl_final_price_temp
									(indxx_id, isin, date, price, localprice, currencyfactor) values";
				$array_price_value=array();
					foreach($ival as $tempKey=>$ivalue)
					{
						$array_price_value[]="('" . $indxx_id . "','" . $ivalue['isin'] . "','" . $lastrundate. "',
									 '" . $ivalue['price'] . "','" . $ivalue['localprice'] . "', '" . $ivalue['currencyfactor'] . "')";
						/*$fpquery="INSERT into tbl_final_price_temp
									(indxx_id, isin, date, price, localprice, currencyfactor) values
									('" . $indxx_id . "','" . $ivalue['isin'] . "','" . date . "',
									 '" . $ivalue['price'] . "','" . $ivalue['localprice'] . "', '" . $ivalue['currencyfactor'] . "')";
						mysql_query($fpquery);
		
						if (($err_code = mysql_errno()))
						{
							log_error("Unable to update converted prices for upcoming index = " . $indxx_id .
										". MYSQL error code = " . $err_code . ". ");
							mail_exit(__FILE__, __LINE__);
						}*/
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

}	
	
}

//exit;
}

function getPrices($date){
	
mysql_query("delete from tbl_prices_local_curr where price not REGEXP '^[0-9\.]+$' or price='0' or price='0.00'");	

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

function getcurrPrices($date){
mysql_query("delete from tbl_curr_prices where price not REGEXP '^[0-9\.]+$' or price='0' or price='0.00' ");	

//echo "SELECT currencyticker,price,currency from tbl_curr_prices where date ='" .date. 
								"'";
								
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

function getLastPrice($ticker)
{
	$security_price = mysql_query("SELECT price, isin,ticker,curr,date from tbl_prices_local_curr where isin ='" .$ticker. 
								"'order by date desc limit 0,1");
if(mysql_num_rows($security_price)>0)
{
	$row=mysql_fetch_assoc($security_price);
	mail($_SESSION['User']['email'],"Old Price Used for Ticker.".$ticker,"Old Price Used for isin .".$ticker ." of date ".$row['date']);
	return $row;
}
	return 0;
}

function getPriceforCurrency5($index_currency,$ticker_currency,$date){
	mail($_SESSION['User']['email'],"Currency factor not found for ".strtoupper($index_currency.$ticker_currency),"Currency factor not found for ".strtoupper($index_currency.$ticker_currency)." for date ".$date." using Old Price");
	
	$query="SELECT currencyticker,price,currency  FROM `tbl_curr_prices` WHERE `currencyticker` LIKE '".strtoupper($index_currency.$ticker_currency)."%' order by date desc limit 0,1";
	
	$res=mysql_query($query);
if(mysql_num_rows($res)>0)
{
$row=mysql_fetch_assoc($res);
if($row['price'])
{
	//echo 1/$row['price'];
	return $row;
}else
{
return	$this->getPriceforCurrency6($ticker_currency,$index_currency,$date);
	
//echo "Price Not Available for Currency Ticker ".$ticker." of date.".$date."<br>" ;
//exit;
}
}
else
{
return	$this->getPriceforCurrency6($ticker_currency,$index_currency,$date);
}



}
function getPriceforCurrency6($index_currency,$ticker_currency,$date){

	 $query="SELECT currencyticker,price,currency  FROM `tbl_curr_prices` WHERE `currencyticker` LIKE '".strtoupper($index_currency.$ticker_currency)."%' order by date desc limit 0,1";
	$res=mysql_query($query);
if(mysql_num_rows($res)>0)
{
$row=mysql_fetch_assoc($res);
if($row['price'])
{
	//echo 1/$row['price'];
 $row['price']= 1/$row['price'];
 return $row;
}else
{
mail($_SESSION['User']['email'],"Currency factor not found for ".strtoupper($index_currency.$ticker_currency),"Currency factor not found for ".strtoupper($index_currency.$ticker_currency));
return NULL;	

//echo "Price Not Available for Currency Ticker ".$ticker." of date.".$date."<br>" ;
//exit;
}
}
else
{
mail($_SESSION['User']['email'],"Currency factor not found for ".strtoupper($index_currency.$ticker_currency),"Currency factor not found for ".strtoupper($index_currency.$ticker_currency));
return NULL;
}



}


}