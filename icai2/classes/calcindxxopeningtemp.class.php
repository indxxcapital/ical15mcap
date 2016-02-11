<?php

class Calcindxxopeningtemp extends Application{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function index()
	{
		
//	$priorityArray=max($_SESSION['priority']);
		
	
		//exit;
		
		
	//	$this->pr($_SESSION,true);
		
		//$this->_baseTemplate="main-template";
		//$this->_bodyTemplate="404";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		
		define("log_file", $_GET['log_file']);
		
		$this->log_info(log_file, "Opening file generation process started for upcoming indexes.");
				
		if($_GET['id'])
		{
			$page=$_GET['id'];	
		}
		else
		{
			$page=0;	
		}
		
		$limit=1;
		
		// 
		$indxxs=mysql_query("select tbl_indxx_temp.* from tbl_indxx_temp  where status='1' and usersignoff='1' and dbusersignoff='1' and submitted='1'   ");	
	
		
		//$totalindexes=count($totalindxxs);
		//$this->pr($indxxs,true);
		$type="open";
		
		 $datevalue2=$this->_date;
//	exit;
	
	//	 $datevalue='';
//$datevalue2='2014-03-04';
//if(date('D')!="Mon")
// $datevalue=date("Y-m-d",strtotime($this->_date)-86400);
//else
// $datevalue=date("Y-m-d",strtotime($this->_date)-86400*3);

		//exit;
		//
		$final_array=array();
		
		
			while(false != ($row = mysql_fetch_assoc($indxxs)))
		{
	//$this->pr($indxx);
			
		//	if($row['id']==31)
		//{
			 $this->log_info(log_file, "Preparing data for index : ".$row['code']);
				
			$final_array[$row['id']]=$row;
			

			
			
			$indxx_value=$this->db->getResult("select tbl_indxx_value_temp.* from tbl_indxx_value_temp where indxx_id='".$row['id']."' order by date desc ",false,1);	
			//$this->pr($indxx_value,true);
			if(!empty($indxx_value))
			{
			$final_array[$row['id']]['index_value']=$indxx_value;
			$datevalue=$indxx_value['date'];
			}
			else{
			$final_array[$row['id']]['index_value']['market_value']=$row['investmentammount'];
			$final_array[$row['id']]['index_value']['olddivisor']=$row['divisor'];
			$final_array[$row['id']]['index_value']['newdivisor']=$row['divisor'];
			$final_array[$row['id']]['index_value']['indxx_value']=$row['investmentammount']/$row['divisor'];
		//	$final_array[$row['id']]['index_value']['date']='2014-01-10';
				//$datevalue="2014-01-10";
			}
			
			
			
			
			
			
			//echo $datevalue;
		//	exit;
			//$query="SELECT   it.id,it.name,it.isin,it.ticker,curr,divcurr,sedol,cusip,countryname,(select price from tbl_final_price_temp fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$row['id']."') as calcprice,(select localprice from tbl_final_price_temp fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$row['id']."') as localprice,(select currencyfactor from tbl_final_price_temp fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$row['id']."') as currencyfactor,(select share from tbl_share_temp sh where sh.isin=it.isin  and sh.indxx_id='".$row['id']."') as calcshare FROM `tbl_indxx_ticker_temp` it where it.indxx_id='".$row['id']."'";			
		
			$query = "SELECT it.id, it.name, it.isin, it.ticker, it.curr, it.sedol, it.cusip, it.countryname, fp.localprice, 
						fp.currencyfactor, fp.price as calcprice, sh.share as calcshare 
						FROM `tbl_indxx_ticker_temp` it left join tbl_final_price_temp fp on fp.isin=it.isin 
						left join tbl_share_temp sh on sh.isin=it.isin 
						where it.indxx_id='" . $row['id'] . "' and fp.indxx_id='" .$row['id'] . "' and sh.indxx_id='" .$row['id'] . "' 
						 and fp.date='" . $datevalue . "'";
		
			$indxxprices=	$this->db->getResult($query,true);
		//	$this->pr($row,true);	
			if(!empty($indxxprices) && !$row['ica'])
			{
			foreach($indxxprices as $key=> $indxxprice)
			{
				
					$indxx_dp_value=$this->db->getResult("select tbl_dividend_ph_temp.* from tbl_dividend_ph_temp where indxx_id='".$row['id']."' and ticker_id ='".$indxxprice['id']."' ",true);	
				if(!empty($indxx_dp_value))
			{
			foreach($indxx_dp_value as $dpvalue)
			{	$final_array[$row['id']]['divpvalue']+=$dpvalue['share']*$dpvalue['dividend'];
			}}
			$ca_query="select identifier,action_id,id,mnemonic,field_id,company_name,ann_date,eff_date,amd_date,currency from tbl_ca  cat where  eff_date='".$datevalue2."' and identifier='".$indxxprice['ticker']."'  and status='1'  and action_id not in (select ca_action_id from tbl_ignore_index_temp where ca_action_id=cat.action_id)";
			$cas=$this->db->getResult($ca_query,true);	
			
			if(!empty($cas))
			{
			foreach($cas as $cakey=> $ca)
			{
			
			
			
			
			$ca_value_query="Select field_name,field_value,field_id from tbl_ca_values_user_edited_temp where ca_id='".$ca['id']."'  and ca_action_id='".$ca['action_id']."' and indxx_id='".$row['id']."' ";
			$ca_values=$this->db->getResult($ca_value_query,true);	
//$this->pr($ca_values);
		if(empty($ca_values)){
			$ca_value_query="Select field_name,field_value,field_id from tbl_ca_values where ca_id='".$ca['id']."'  and ca_action_id='".$ca['action_id']."' ";
			$ca_values=$this->db->getResult($ca_value_query,true);	
		}
//		$this->pr($ca_values);

		
			$value=0;
			if(!empty($ca_values) )
			{
			foreach($ca_values as $ca_value)
			{
			if($ca_value['field_name']=='CP_DVD_TYP')
			{
			$value=$ca_value['field_value'];
			}
			}
			}
		//	echo $value;
		if($row['ireturn']==1 && $ca['mnemonic']=='DVD_CASH' && $value!=1001 )
			{
			$cas[$cakey]=array();
			}
			else{
			$cas[$cakey]['ca_values']=$ca_values;
			}
			
			}
			}
			$indxxprices[$key]['ca']=$cas;
			}
			}
			
			$final_array[$row['id']]['values']=$indxxprices;
		
		
		//$this->pr($indxxprices);	
			
					
			}	
		
		
	//	}
	mysql_free_result($indxxs);
//$this->pr($final_array,true);
	
if($type="open"){
	$client_folder = "../files/backup/";
			if (!file_exists($client_folder))
			mkdir($client_folder, 0777, true);
		  file_put_contents('../files/backup/preopentempdata'.date("Y-m-d-H-i-s").'.json', json_encode($final_array));
		 $this->log_info(log_file, "Pre open data created for today  ");
	if(!empty($final_array))
	{
		foreach($final_array as $indxxKey=> $closeIndxx)
		{
			 $this->log_info(log_file, "Processing data  for index : ".$closeIndxx['code']);
			
			$oldindexvalue=$closeIndxx['index_value']['indxx_value'];
			$newindexvalue=0;
		 $oldDivisor=$closeIndxx['index_value']['olddivisor'];
		//echo "<br>";
		$final_array[$indxxKey]['index_value']['divisor_impact']=0;
	//$newDivisor=$oldDivisor;
			$oldmarketValue=0;

			$newmarketValue=0;
			$sumofDividendes=0;
			$divisorAdjustinStock=$closeIndxx['cash_adjust'];
	///	exit;	
		
			
			
			foreach($closeIndxx['values'] as $securityKey=> $closeprices)
			{
				
				
				
			$divisorImpact=0;
			
			$priceAdjfactor=1;
			$shareAdjfactor=1;
			$base_price=$closeprices['calcprice'];
			
			$userAdjfactor=$this->get_user_ca_adj_factor($closeIndxx['id'],$closeprices['id']);
			if($userAdjfactor)
			{
				$this->log_info(log_file, "User adjustment factor in index : ".$closeIndxx['code']);
				
						$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=($closeprices['calcshare']*$userAdjfactor);
						$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=($closeprices['calcprice']/$userAdjfactor);
						
						$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$closeprices['calcprice']);						
						$shareAdjfactor=$shareAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcshare']/$closeprices['calcshare']);
			}
			
			
			//$this->pr($closeprices);	
			if(!empty($closeprices['ca']))
			{
				foreach($closeprices['ca'] as $ca_key=> $ca_actions)
				{
					
					$this->log_info(log_file, "Processing Corporate action for : ".$closeprices['ticker']);
					
					$final_array[$indxxKey]['values'][$securityKey]['ca'][$ca_key]['ca_values']=	$this->getCa($ca_actions['id'],$ca_actions['action_id']);
					if($ca_actions['mnemonic']=='STOCK_SPLT' && $closeprices['calcprice']!=0)
					{
							 $this->log_info(log_file, "In STOCK_SPLT for Ticker : ".$closeprices['ticker']);

						
						$adjfactor=	$this->getAdjFactorforSplit($ca_actions['id'],$ca_actions['action_id'],$indxxKey,1);
						if($adjfactor){
							$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=($closeprices['calcshare']*$adjfactor);
						$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=($closeprices['calcprice']/$adjfactor);
						
						$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$closeprices['calcprice']);						
						$shareAdjfactor=$shareAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcshare']/$closeprices['calcshare']);
					 $this->log_info(log_file, "AdjFactor found  of ticker : ".$closeprices['ticker']." = ".$adjfactor);
						}else{
						$this->log_info(log_file, "Adj Factor not found for ticker : ".$closeprices['ticker']);
						}
					}
						if($ca_actions['mnemonic']=='DVD_STOCK' && $closeprices['calcprice']!=0)
					{
			 $this->log_info(log_file, "In DVD_STOCK for Ticker : ".$closeprices['ticker']);
						$adjfactor=	$this->getAdjFactorforDvdStock($ca_actions['id'],$ca_actions['action_id'],$indxxKey,1);
						if($adjfactor)
						{$adjfactor=($adjfactor/100)+1;
						
						$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=($closeprices['calcshare']*$adjfactor);
						$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=($closeprices['calcprice']/$adjfactor);
						
						$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$closeprices['calcprice']);						
						$shareAdjfactor=$shareAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcshare']/$closeprices['calcshare']);						
						$this->log_info(log_file, "AdjFactor found  of ticker : ".$closeprices['ticker']." = ".$adjfactor);
						}else{
						$this->log_info(log_file, "Adj Factor not found for ticker : ".$closeprices['ticker']);
					}
					
					if($ca_actions['mnemonic']=='SPIN' && $closeprices['calcprice']!=0)
					{
			 $this->log_info(log_file, "In SPIN for Ticker : ".$closeprices['ticker']);
						$adjfactorSpin=	$this->getAdjFactorforSpin($ca_actions['id'],$ca_actions['action_id'],$indxxKey,1);
						//$adjfactor=($adjfactor/100)+1;
					if($adjfactorSpin)
					$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=($closeprices['calcshare']/$adjfactorSpin);
					$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=($closeprices['calcprice']*$adjfactorSpin);
					$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$closeprices['calcprice']);						
					$shareAdjfactor=$shareAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcshare']/$closeprices['calcshare']);
					 $this->log_info(log_file, "AdjFactor found  of ticker : ".$closeprices['ticker']." = ".$adjfactorSpin);
					}else{
						$this->log_info(log_file, "Adj Factor not found for ticker : ".$closeprices['ticker']);
						}	
		
					}
					
					
					
					
					if($ca_actions['mnemonic']=='RIGHTS_OFFER' && $closeprices['calcprice']!=0)
					{
						 $this->log_info(log_file, "In RIGHTS_OFFER for Ticker : ".$closeprices['ticker']);
				//	$oldDivisorTemp=$newDivisor;
						
						$cp_ratio=	$this->getcpratio($ca_actions['id'],$ca_actions['action_id'],$indxxKey,1);
						$cp_adj=	$this->getAdjFactorforSpin($ca_actions['id'],$ca_actions['action_id'],$indxxKey,1);
						if($cp_ratio && $cp_adj){
					$offerpricesArray=	$this->getOfferPrices($ca_actions['id'],$ca_actions['action_id'],$ca_actions['currency'],$final_array[$indxxKey]['curr'],$closeprices['index_value']['date'],$indxxKey,1);	
					$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=((1+$cp_ratio)*$closeprices['calcshare']*$cp_adj);
					$z=($closeprices['calcshare']*$closeprices['calcprice'])+($closeprices['calcshare']*$cp_ratio*$offerpricesArray['op_price_index_currency']);
		$c=$closeprices['calcshare']+($closeprices['calcshare']*$cp_ratio);
	// ($z/$c);
		$offerprices=$z/$c;
		
				$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=$offerprices;
				$newDivisor=$oldDivisor+((($final_array[$indxxKey]['values'][$securityKey]['newcalcshare']*$final_array[$indxxKey]['values'][$securityKey]['newcalcprice'])-($closeprices['calcshare']*$closeprices['calcprice']))/$oldindexvalue);
			
			
			
					$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$closeprices['calcprice']);						
					$shareAdjfactor=$shareAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcshare']/$closeprices['calcshare']);
			
					$divisorImpact+=$newDivisor-$oldDivisor;
		
						}
					else{
						$this->log_info(log_file, "Adj Factor not found for ticker : ".$closeprices['ticker']);
						}
					
					}
					if($ca_actions['mnemonic']=='DVD_CASH' && $closeprices['calcprice']!=0)
					{
						$this->log_info(log_file, "In DVD_CASH for Ticker : ".$closeprices['ticker']);
						
					$ca_prices= $this->getCaPrices2($ca_actions['id'],$ca_actions['action_id'],$ca_actions['currency'],$final_array[$indxxKey]['curr'],$closeIndxx['index_value']['date'],$closeIndxx['div_type'],$indxxKey,1);	
//$this->pr($ca_prices);
					
					if($closeIndxx['ireturn']==2 && $ca_prices['CP_DVD_TYP']!='1001')
					{
						$this->log_info(log_file, "In dividend place holder for Ticker : ".$closeprices['ticker']);
						$final_array[$indxxKey]['divpvalue']+=($closeprices['calcshare']*$ca_prices['ca_price_index_currency']);
						
//$this->db->query('INSERT into tbl_dividend_ph (date,indxx_id, ticker_id,share,dividend) values ("'.$datevalue2.'","'.$closeIndxx['id'].'","'.$closeprices['id'].'","'.$closeprices['calcshare'].'","'.$ca_prices['ca_price_index_currency'].'")');
						$local_adj_factor=0;
					$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=$base_price-$ca_prices['ca_price_index_currency'];
					$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$base_price);
				//	$newDivisor=$oldDivisor-(($closeprices['calcshare']*$ca_prices['ca_price_index_currency'])/$oldindexvalue);
				//	$divisorImpact+=$newDivisor-$oldDivisor;
					
					$local_adj_factor=($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$base_price);
				//echo "<br>";
				 $base_price=$local_adj_factor*$base_price;
						
				//	$final_array[$indxxKey]['dividendmarketcap']+=($closeprices['calcshare']*$ca_prices['ca_price_index_currency']);
					
					}elseif($divisorAdjustinStock)
					{
							$this->log_info(log_file, "In Divisor adjust in stock for Ticker : ".$closeprices['ticker']);
					$newfactor=($closeprices['calcprice']-$ca_prices['ca_price_index_currency'])/$closeprices['calcprice'];
					$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=$closeprices['calcshare']/$newfactor;
					$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=$closeprices['calcprice']*$newfactor;
					$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$closeprices['calcprice']);						
					$shareAdjfactor=$shareAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcshare']/$closeprices['calcshare']);
					
					}
					elseif($ca_prices['CP_DVD_TYP']=='1001')
					{$this->log_info(log_file, "In Special Cash in stock for Ticker : ".$closeprices['ticker']);
						
					//	$this->pr($closeprices,true);
				//$adjfactorforcash=$ca_prices['CP_ADJ'];
					$adjfactorforcash=($closeprices['calcprice']-$ca_prices['ca_price_index_currency'])/$closeprices['calcprice'];
					
					$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=$closeprices['calcshare']/$adjfactorforcash;
					$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=$closeprices['calcprice']*$adjfactorforcash;
					
					$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$closeprices['calcprice']);						
					$shareAdjfactor=$shareAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcshare']/$closeprices['calcshare']);
					
					}else{
						$this->log_info(log_file, "In Dividend adjust in divisor for Ticker : ".$closeprices['ticker']);
					$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=$base_price-$ca_prices['ca_price_index_currency'];
										
					$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$base_price);
					$newDivisor=$oldDivisor-(($closeprices['calcshare']*$ca_prices['ca_price_index_currency'])/$oldindexvalue);
					$divisorImpact+=$newDivisor-$oldDivisor;
					
						$local_adj_factor=($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$base_price);
				 	$base_price=$local_adj_factor*$base_price;
					}
					
					
					
					
					}
					
					
					if($ca_actions['mnemonic']=='CHG_ID')
					{
						$this->log_info(log_file, "In Change ISIN Ticker : ".$closeprices['ticker']);
						
						$oldisin=	$this->getoldISIN($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
						$newisin=	$this->getnewISIN($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
						
					}
					if($ca_actions['mnemonic']=='CHG_NAME')
					{	$this->log_info(log_file, "In Change name Ticker : ".$closeprices['ticker']);
				
						$oldname=	$this->getoldName($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
						$newname=	$this->getnewName($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
						 $nametickerUpdateQuery='UPDATE  tbl_indxx_ticker_temp  set name ="'.$newname.'" where indxx_id="'.$indxxKey.'"  and isin="'.			$final_array[$indxxKey]['values'][$securityKey]['isin'].'"'; 
					 $this->db->query($nametickerUpdateQuery);	
					 $this->log_info(log_file, "Name change for Ticker : ".$closeprices['ticker']);
					$final_array[$indxxKey]['values'][$securityKey]['name']=$newname;
					}
					
					
				}
			}
			
			$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=$closeprices['calcprice']*$priceAdjfactor;
			$final_array[$indxxKey]['values'][$securityKey]['newlocalprice']=$closeprices['localprice']*$priceAdjfactor;
			$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=$closeprices['calcshare']*$shareAdjfactor;
			if($final_array[$indxxKey]['values'][$securityKey]['newcalcshare']!=$closeprices['calcshare'])
			{
			$shareUpdateQuery='UPDATE  tbl_share_temp  set share ="'.$final_array[$indxxKey]['values'][$securityKey]['newcalcshare'].'" where indxx_id="'.$indxxKey.'"  and isin="'.$closeprices['isin'].'"';
			$this->db->query($shareUpdateQuery);
			$this->log_info(log_file, "Share change for Ticker : ".$closeprices['ticker']);
			}
			
			
			if($oldisin!='' && $newisin!=''){
				if($closeprices['isin']==$oldisin)
				{
				  $isinUpdateQuery='UPDATE  tbl_share_temp  set isin ="'.$newisin.'" where indxx_id="'.$indxxKey.'"  and isin="'.$closeprices['isin'].'"'; 				
				  $isintickerUpdateQuery='UPDATE  tbl_indxx_ticker_temp  set isin ="'.$newisin.'" where indxx_id="'.$indxxKey.'"  and isin="'.$closeprices['isin'].'"'; 
			$this->db->query($isintickerUpdateQuery);	

				
				
				
					$final_array[$indxxKey]['values'][$securityKey]['isin']=$newisin;
		//			$final_array[$indxxKey]['values'][$securityKey]['newisin']=$newisin;
				
				$this->log_info(log_file, "ISIN change for Ticker : ".$closeprices['ticker']);
				}
				else{
				echo "Not Equal";
				$this->log_error(log_file, "ISIN Not change for Ticker : ".$closeprices['ticker']);
				}
			}
			
			
			 $final_array[$indxxKey]['index_value']['divisor_impact']+=	$divisorImpact;;
			 	//echo "<br>";	
			}
			
	$this->log_info(log_file, "Data Prepared for index : ".$closeIndxx['code']);
			}
		
		
		
		}
				  file_put_contents('../files/backup/postopentempdata'.date("Y-m-d-H-i-s").'.json', json_encode($final_array));

	}
	//	$this->pr($final_array,true);
	if(!empty($final_array))
	{
		$this->log_error(log_file, "in Index calculation after data prepration ");
		foreach($final_array as $key=>$closeIndxx)
		{
			$file="../files/ca-output_upcomming/Opening-".strtolower($closeIndxx['code'])."p-".$datevalue2.".txt";
			
			$client_folder = "../files/ca-output_upcomming/";
			if (!file_exists($client_folder))
			mkdir($client_folder, 0777, true);
			
	$this->log_error(log_file, "Calculating index  : ". $closeIndxx['code']);
			$open=fopen($file,"w+");

			$entry1='Date'.",";
			$entry1.=$datevalue2.",\n";
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
			}	$entry4='';
					
		
		
		
		
		$newMarketValue=0;
		if(!empty($closeIndxx))
		{
		foreach($closeIndxx['values'] as $security)
		{
		//$this->pr($security);
			
		$newMarketValue+=$security['newcalcprice']*$security['newcalcshare'];



			/*$entry4.= "\n".date("Ymd",strtotime($datevalue2)).",";
            $entry4.=  $security['ticker'].",";
            $entry4.= $security['name'].",";
            $entry4.=$security['isin'].","; 
			 $entry4.=$security['sedol'].",";;
            $entry4.=$security['cusip'].",";;
            $entry4.=$security['countryname'].",";
		    $entry4.=$security['newcalcshare'].",";
       		$entry4.=number_format($security['newlocalprice'],2,'.','').",";
			if($closeIndxx['display_currency'])
	     	{$entry4.=$security['curr'].",";
			$entry4.=number_format($security['currencyfactor'],6,'.','').",";
			}			
	*/

		}
		}
		//echo $closeIndxx['index_value']['divisor_impact'];
		//echo "<br>";
		 $newDivisorforindxx=$closeIndxx['index_value']['olddivisor']+$closeIndxx['index_value']['divisor_impact'];
		//echo "<br>";
	//	 $closeIndxx['index_value']['indxx_value']."<br>";
//		 $newMarketValue/$closeIndxx['index_value']['newdivisor'];
	//	  $newindexvalue=number_format(($newMarketValue/$newDivisorforindxx),4,'.','');


if($closeIndxx['divpvalue'])
{
	
	
		 // 	$insertQuery='update tbl_indxx_temp set divpvalue="'.$closeIndxx['divpvalue'].'" where id="'.$closeIndxx['id'].'" ';
	//	$this->db->query($insertQuery);	
	
	
	
	$newMarketValue+=$closeIndxx['divpvalue'];
	 $newindexvalue=number_format((($newMarketValue)/$newDivisorforindxx),2,'.','');
}
else
 {$newindexvalue=number_format(($newMarketValue/$newDivisorforindxx),2,'.','');
 }

$weightArray=array();
 foreach($closeIndxx['values'] as $security)
{
$entry4.= "\n".date("Ymd",strtotime($datevalue2)).",";
            $entry4.=  $security['ticker'].",";
            $entry4.= $security['name'].",";
            $entry4.=$security['isin'].","; 
			 $entry4.=$security['sedol'].",";;
            $entry4.=$security['cusip'].",";;
            $entry4.=$security['countryname'].",";
		    $entry4.=$security['newcalcshare'].",";
$weight=(($security['newcalcshare']*$security['newcalcprice'])/$newMarketValue);
			$entry4.=$weight.",";
       		$entry4.=number_format($security['newlocalprice'],2,'.','').",";
			if($closeIndxx['display_currency'])
	     	{$entry4.=$security['curr'].",";
			$entry4.=number_format($security['currencyfactor'],6,'.','').",";
			}
			$weightArray[]="('".$closeprices['isin']."','".$weight."','".$security['newcalcprice']."','".$security['newcalcshare']."','".$datevalue."','".$closeIndxx['code']."','".$closeIndxx['id']."')";
}
 if(!empty($weightArray))
{
	//echo "insert into tbl_weights (isin,weight,price,share,date,code,indxx_id) values " .implode(",",$weightArray).";";
	 $this->db->query("insert into tbl_weights_open_temp (isin,weight,price,share,date,code,indxx_id) values " .implode(",",$weightArray).";");
}


		  	$insertQuery='INSERT into tbl_indxx_value_open_temp (indxx_id,code,market_value,indxx_value,date,olddivisor,newdivisor) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.$newMarketValue.'","'.$newindexvalue.'","'.$datevalue2.'","'.$closeIndxx['index_value']['olddivisor'].'","'.$newDivisorforindxx.'")';
		$this->db->query($insertQuery);	
		
			$entry2=$newindexvalue.",\n";
				$entry2.="Divisor,".$newDivisorforindxx.",\n";
		$entry2.="Market Value,".$newMarketValue.",\n\n";
if($open){   
 if(   fwrite($open,$entry1.$entry2.$entry3.$entry4))
{
	
	
	
$insertlogQuery='INSERT into tbl_indxx_temp_log (type,indxx_id,value) values ("0","'.$closeIndxx['id'].'","'.mysql_real_escape_string($entry1.$entry2.$entry3.$entry4).'")';
		$this->db->query($insertlogQuery);
	
 fclose($open);
$filetext= "file Written for ".$closeIndxx['code']."<br>";
$this->log_info(log_file, "Calculated index  : ". $closeIndxx['code'].",file is available on :".$file);
}
}  
		
		unset($final_array[$key]);
		}
		
		unset($final_array);
	}
	
	
	
	
	
	
	


//$this->saveProcess(1);

	
			//echo "Redirect Next";	
			
			$this->saveProcess(1);
			
			$this->Redirect2("index.php?module=compositopen&date=" .$datevalue2. "&log_file=" . log_file, "", "");	
	//$this->Redirect2("index.php?module=notifyforca","","");	

}
   
} 
?>