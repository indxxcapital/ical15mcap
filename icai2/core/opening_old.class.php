<?php

class Opening extends Functions{
var $db='';
	
	function Opening($obj,$db)
	{
		$this->obj=$obj;
		$this->db=$db;
	}
	
	function calcindxxopening()
	{
		echo "In Function block";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		
		$type="open";
		
		if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));
		$this->save_process("Opening",date,"0");

		prepare_logfile();
		define("log_file", $this->get_opening_logs_file());
		//echo log_file;
		//exit;
		 $this->log_info(log_file, "Opening file generation process started for live indexes");
		 
		 $datevalue2=date;
		//$datevalue2="2014-08-06";
		//exit;
		if($_GET['id'])
		{
			$page=$_GET['id'];	
		}
		else
		{
			$page=0;	
		}
		
		$limit=1;
		
		
		//echo $_SESSION['currentPriority']['priority'];
		//exit;
		// and priority='".$_SESSION['currentPriority']['priority']."'
		
		$indxxs=mysql_query("select tbl_indxx.* from tbl_indxx  where status='1' and usersignoff='1' and dbusersignoff='1' and submitted='1'  ");
		
		
		//exit;
		$final_array=array();
		
		
		
		while(false != ($row = mysql_fetch_assoc($indxxs)))
		{
			
			
			 $this->log_info(log_file, "Preparing data for index : ".$row['code']);
			
	//$this->pr($indxx);
			
		//	if($row['id']==31)
		//{
				
			$final_array[$row['id']]=$row;
			

			$client=$this->db->getResult("select tbl_ca_client.ftpusername from tbl_ca_client where id='".$row['client_id']."'",false,1);	
		//	
		$final_array[$row['id']]['client']=$client['ftpusername'];
			
			$indxx_value=$this->db->getResult("select tbl_indxx_value.* from tbl_indxx_value where indxx_id='".$row['id']."' order by date desc ",false,1);	
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
			//$query="SELECT  it.id,it.name,it.isin,it.ticker,curr,divcurr,curr,sedol,cusip,countryname,(select price from tbl_final_price fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$row['id']."') as calcprice,(select localprice from tbl_final_price fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$row['id']."') as localprice,(select currencyfactor from tbl_final_price fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$row['id']."') as currencyfactor,(select share from tbl_share sh where sh.isin=it.isin  and sh.indxx_id='".$row['id']."') as calcshare FROM `tbl_indxx_ticker` it where it.indxx_id='".$row['id']."'";			
		$query = "SELECT it.id, it.name, it.isin, it.ticker, it.curr, it.sedol, it.cusip, it.countryname, fp.localprice, 
						fp.currencyfactor, fp.price as calcprice, sh.share as calcshare 
						FROM `tbl_indxx_ticker` it left join tbl_final_price fp on fp.isin=it.isin 
						left join tbl_share sh on sh.isin=it.isin 
						where it.indxx_id='" . $row['id'] . "' and fp.indxx_id='" .$row['id'] . "' and sh.indxx_id='" .$row['id'] . "' 
						 and fp.date='" . $datevalue . "'";
		
		
			$indxxprices=	$this->db->getResult($query,true);
			//$this->pr($indxxprices,true);	
			if(!empty($indxxprices))
			{
			foreach($indxxprices as $key=> $indxxprice)
			{
				
					$indxx_dp_value=$this->db->getResult("select tbl_dividend_ph.* from tbl_dividend_ph where indxx_id='".$row['id']."' and ticker_id ='".$indxxprice['id']."' ",true);	
				if(!empty($indxx_dp_value))
			{
			foreach($indxx_dp_value as $dpvalue)
			{	$final_array[$row['id']]['divpvalue']+=$dpvalue['share']*$dpvalue['dividend'];
			}}
		$ca_query="select identifier,action_id,id,mnemonic,field_id,company_name,ann_date,eff_date,amd_date,currency from tbl_ca cat where  eff_date='".$datevalue2."' and identifier='".$indxxprice['ticker']."'  and status='1'  and action_id not in (select ca_action_id from tbl_ignore_index where ca_action_id=cat.action_id)";
			
			$cas=$this->db->getResult($ca_query,true);	
			
			
			//echo "<br>";
			//$caflag=false;
			
			//$this->pr($cas);
			if(!empty($cas))
			{
			foreach($cas as $cakey=> $ca)
			{
			
			
			$ca_value_query="Select field_name,field_value,field_id from tbl_ca_values_user_edited where ca_id='".$ca['id']."'  and ca_action_id='".$ca['action_id']."' and indxx_id='".$row['id']."' ";
			$ca_values=$this->db->getResult($ca_value_query,true);	
//$this->pr($ca_values);
		if(empty($ca_values)){
			$ca_value_query="Select field_name,field_value,field_id from tbl_ca_values where ca_id='".$ca['id']."'  and ca_action_id='".$ca['action_id']."' ";
			$ca_values=$this->db->getResult($ca_value_query,true);	
		}
//			if($row['ireturn']==1 && $ca['mnemonic'] )
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
		unset($indxxprices);
		
		//$this->pr($indxxprices);	
			
					
		}
		mysql_free_result($indxxs);
		
	//	$this->pr($final_array,true);
			if($type="open"){
	
		$client_folder = "../files/backup/";
			if (!file_exists($client_folder))
			mkdir($client_folder, 0777, true);
	  //file_put_contents('../files/backup/preopendata'.date("Y-m-d-H-i-s").time().'.json', json_encode($final_array));
	 // exit;
	 			 $this->log_info(log_file, "Pre open data created for today  ");

	 
	if(!empty($final_array))
	{
		foreach($final_array as $indxxKey=> $closeIndxx)
		{
			
						 $this->log_info(log_file, "Processing data  for index : ".$closeIndxx['code']);

			
			//$this->pr($closeIndxx,true);
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
				
				//$this->pr($closeprices);
			$divisorImpact=0;
			$oldisin='';
			$priceAdjfactor=1;
			$shareAdjfactor=1;
			$base_price=$closeprices['calcprice'];
			//$local_adj_factor=0;
			//$this->pr($closeprices);	
			$userAdjfactor=$this->get_user_ca_adj_factor($closeIndxx['id'],$closeprices['id']);
			if($userAdjfactor)
			{///echo $userAdjfactor;
			//echo "<br>";
					 $this->log_info(log_file, "User adjustment factor in index : ".$closeIndxx['code']);
			
			
						$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=($closeprices['calcshare']*$userAdjfactor);
						$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=($closeprices['calcprice']/$userAdjfactor);
						
					 	$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$closeprices['calcprice']);
					//echo "<br>";						
					 	$shareAdjfactor=$shareAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcshare']/$closeprices['calcshare']);
					//echo "<br>";
					//exit;
			}
			
			
			
			if(!empty($closeprices['ca']))
			{
				foreach($closeprices['ca'] as $ca_key=> $ca_actions)
				{
					
					
					 $this->log_info(log_file, "Processing Corporate action for : ".$closeprices['ticker']);
					
					
					$final_array[$indxxKey]['values'][$securityKey]['ca'][$ca_key]['ca_values']=	$this->getCa($ca_actions['id'],$ca_actions['action_id']);
					
					
				
					
					if($ca_actions['mnemonic']=='STOCK_SPLT' && $closeprices['calcprice']!=0)
					{
						
						 $this->log_info(log_file, "In STOCK_SPLT for Ticker : ".$closeprices['ticker']);
						$adjfactor=	$this->getAdjFactorforSplit($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
						
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
					{	 $this->log_info(log_file, "In DVD_STOCK for Ticker : ".$closeprices['ticker']);
			
						$adjfactor=	$this->getAdjFactorforDvdStock($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
						if($adjfactor)
						{$adjfactor=($adjfactor/100)+1;
						$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=($closeprices['calcshare']*$adjfactor);
						$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=($closeprices['calcprice']/$adjfactor);
						
						$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$closeprices['calcprice']);						
						$shareAdjfactor=$shareAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcshare']/$closeprices['calcshare']);			 $this->log_info(log_file, "AdjFactor found  of ticker : ".$closeprices['ticker']." = ".$adjfactor);
						}else{
						$this->log_info(log_file, "Adj Factor not found for ticker : ".$closeprices['ticker']);
						}
					}
					
					if($ca_actions['mnemonic']=='SPIN' && $closeprices['calcprice']!=0)
					{$this->log_info(log_file, "In SPIN for Ticker : ".$closeprices['ticker']);
			
					$adjfactorSpin=	$this->getAdjFactorforSpin($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
					
					if($adjfactorSpin){
					
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
						
						$cp_ratio=	$this->getcpratio($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
						$cp_adj=	$this->getAdjFactorforSpin($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
					
					if($cp_ratio && $cp_adj){
					$offerpricesArray=	$this->getOfferPrices($ca_actions['id'],$ca_actions['action_id'],$ca_actions['currency'],$final_array[$indxxKey]['curr'],$closeIndxx['index_value']['date'],$indxxKey);	
					$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=((1+$cp_ratio)*$closeprices['calcshare']);
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
						
					$ca_prices= $this->getCaPrices2($ca_actions['id'],$ca_actions['action_id'],$ca_actions['currency'],$final_array[$indxxKey]['curr'],$closeIndxx['index_value']['date'],$closeIndxx['div_type'],$indxxKey);	
//$this->pr($ca_prices);
					
					
					
					if($closeIndxx['ireturn']==2 && $ca_prices['CP_DVD_TYP']!='1001')
					{
						//$this->pr($ca_prices);
						//$this->pr($closeprices);
						
					
							$this->log_info(log_file, "In dividend place holder for Ticker : ".$closeprices['ticker']);
//$this->db->query('INSERT into tbl_dividend_ph (date,indxx_id, ticker_id,share,dividend) values ("'.$datevalue2.'","'.$closeIndxx['id'].'","'.$closeprices['id'].'","'.$closeprices['calcshare'].'","'.$ca_prices['ca_price_index_currency'].'")');
					
					$final_array[$indxxKey]['divpvalue']+=($closeprices['calcshare']*$ca_prices['ca_price_index_currency']);
						$local_adj_factor=0;
						
					$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=$base_price-$ca_prices['ca_price_index_currency'];
										
					$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$base_price);
				//	$newDivisor=$oldDivisor-(($closeprices['calcshare']*$ca_prices['ca_price_index_currency'])/$oldindexvalue);
				//	$divisorImpact+=$newDivisor-$oldDivisor;
					
					$local_adj_factor=($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$base_price);
				//echo "<br>";
				 	$base_price=$local_adj_factor*$base_price;
						
					//$final_array[$indxxKey]['dividendmarketcap']+=($closeprices['calcshare']*$ca_prices['ca_price_index_currency']);
					
					}elseif($divisorAdjustinStock)
					{
					//	echo $closeprices['calcprice']-$ca_prices['ca_price_in_ca_currency'];
					//	exit;
						$this->log_info(log_file, "In Divisor adjust in stock for Ticker : ".$closeprices['ticker']);
					 $newfactor=($closeprices['calcprice']-$ca_prices['ca_price_index_currency'])/$closeprices['calcprice'];
					$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=$closeprices['calcshare']/$newfactor;
					$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=$closeprices['calcprice']*$newfactor;
					$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$closeprices['calcprice']);						
					$shareAdjfactor=$shareAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcshare']/$closeprices['calcshare']);
					
					}
					elseif($ca_prices['CP_DVD_TYP']=='1001')
					{
					//$adjfactorforcash=$ca_prices['CP_ADJ'];
					$this->log_info(log_file, "In Special Cash in stock for Ticker : ".$closeprices['ticker']);
					$adjfactorforcash=($closeprices['calcprice']-$ca_prices['ca_price_index_currency'])/$closeprices['calcprice'];
					
					$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=$closeprices['calcshare']/$adjfactorforcash;
					$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=$closeprices['calcprice']*$adjfactorforcash;
					
					$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$closeprices['calcprice']);						
					$shareAdjfactor=$shareAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcshare']/$closeprices['calcshare']);
					
					}else{
						$this->log_info(log_file, "In Dividend adjust in divisor for Ticker : ".$closeprices['ticker']);
						$local_adj_factor=0;
						
					$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=$base_price-$ca_prices['ca_price_index_currency'];
										
					$priceAdjfactor=$priceAdjfactor*($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$base_price);
					$newDivisor=$oldDivisor-(($closeprices['calcshare']*$ca_prices['ca_price_index_currency'])/$oldindexvalue);
					$divisorImpact+=$newDivisor-$oldDivisor;
					
					$local_adj_factor=($final_array[$indxxKey]['values'][$securityKey]['newcalcprice']/$base_price);
				//echo "<br>";
				 	$base_price=$local_adj_factor*$base_price;
				//		echo "<br>";
					}
					
					
					
					
					}
					
					
					if($ca_actions['mnemonic']=='CHG_ID')
					{
						$this->log_info(log_file, "In Change ISIN Ticker : ".$closeprices['ticker']);
						//$oldisin=$final_array[$indxxKey]['values'][$securityKey]['isin'];
						//$oldisin=	$this->getoldISIN($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
						$newisinArray=	$this->getnewISIN($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
						if(!empty($newisinArray))
						{
							if($newisinArray['CP_OLD_ISIN'])
							$oldisin=$newisinArray['CP_OLD_ISIN'];
							if($newisinArray['CP_NEW_ISIN'])	
							$newisin=$newisinArray['CP_NEW_ISIN'];
						
						if($newisinArray['CP_NEW_CUSIP'])
						{
							$this->log_info(log_file, "In Change Cusip Ticker : ".$closeprices['ticker']);
							
							$newtickerUpdateQuery='UPDATE  tbl_indxx_ticker  set cusip ="'.$newisinArray['CP_NEW_CUSIP'].'" where indxx_id="'.$indxxKey.'"  and isin="'.$final_array[$indxxKey]['values'][$securityKey]['isin'].'"'; 
				 $this->db->query($newtickerUpdateQuery);	
					$final_array[$indxxKey]['values'][$securityKey]['cusip']=$newisinArray['CP_NEW_CUSIP'];
						}
						
						if($newisinArray['CP_NEW_SEDOL'])
						{
							$this->log_info(log_file, "In Change Sedol Ticker : ".$closeprices['ticker']);
							
							$newtickerUpdateQuery='UPDATE  tbl_indxx_ticker  set sedol ="'.$newisinArray['CP_NEW_SEDOL'].'" where indxx_id="'.$indxxKey.'"  and isin="'.			$final_array[$indxxKey]['values'][$securityKey]['isin'].'"'; 
				 $this->db->query($newtickerUpdateQuery);	
					$final_array[$indxxKey]['values'][$securityKey]['sedol']=$newisinArray['CP_NEW_SEDOL'];
						}
						
						
						}
						//$this->pr($ca_actions);
						//$this->pr($newisin,true);
						
					}
					
					
					if($ca_actions['mnemonic']=='CHG_TKR')
					{
						$this->log_info(log_file, "In Change Ticker : ".$closeprices['ticker']);
						
						//$oldisin=	$this->getoldISIN($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
						 $newTicker=	$this->getnewTicker($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
					if($newTicker)
					{
						 
					
					
					
					
					$newtickerUpdateQuery='UPDATE  tbl_indxx_ticker  set ticker ="'.$newTicker.' Equity" where indxx_id="'.$indxxKey.'"  and isin="'.			$final_array[$indxxKey]['values'][$securityKey]['isin'].'"'; 
				 $this->db->query($newtickerUpdateQuery);	
					$final_array[$indxxKey]['values'][$securityKey]['ticker']=$newTicker;
					
					mail($this->getDBUsers(),"Ticker Change", " Ticker change has been done from ".$closeprices['ticker']." to ".$newTicker." Equity in index ".$closeIndxx." , Please update Request file on ".$this->siteconfig->base_url);
					
					
					
					
					
						$this->log_info(log_file, "Ticker change for Ticker : ".$closeprices['ticker'] ."to ".$newTicker);
					}
					}
					
					
					if($ca_actions['mnemonic']=='CHG_NAME')
					{
				$this->log_info(log_file, "In Change name Ticker : ".$closeprices['ticker']);
						$oldname=	$this->getoldName($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
						$newname=	$this->getnewName($ca_actions['id'],$ca_actions['action_id'],$indxxKey);
						 
						 if($newname)
						{ $nametickerUpdateQuery='UPDATE  tbl_indxx_ticker  set name ="'.$newname.'" where indxx_id="'.$indxxKey.'"  and isin="'.			$final_array[$indxxKey]['values'][$securityKey]['isin'].'"'; 
				 $this->db->query($nametickerUpdateQuery);	
					$final_array[$indxxKey]['values'][$securityKey]['name']=$newname;
						$this->log_info(log_file, "Name change for Ticker : ".$closeprices['ticker']);
						
						}
						}
					
					
				}
			}
		//	echo $closeprices['calcshare']."=>".strlen((string)$closeprices['calcshare']);
		//	echo "<br>";
			$final_array[$indxxKey]['values'][$securityKey]['newcalcprice']=$closeprices['calcprice']*$priceAdjfactor;
			$final_array[$indxxKey]['values'][$securityKey]['newlocalprice']=	$closeprices['localprice']*$priceAdjfactor;
			if($shareAdjfactor==1)
			{
			$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=$closeprices['calcshare'];
			}
			else{
			$final_array[$indxxKey]['values'][$securityKey]['newcalcshare']=number_format($closeprices['calcshare']*$shareAdjfactor,13,'.','');
			}
			if($final_array[$indxxKey]['values'][$securityKey]['newcalcshare']!=$closeprices['calcshare'])
			{
			$shareUpdateQuery='UPDATE  tbl_share  set share ="'.$final_array[$indxxKey]['values'][$securityKey]['newcalcshare'].'" where indxx_id="'.$indxxKey.'"  and isin="'.$closeprices['isin'].'"';
			$this->db->query($shareUpdateQuery);
				$this->log_info(log_file, "Share change for Ticker : ".$closeprices['ticker']);
			}
			
			
			if($oldisin!='' && $newisin!=''){
			//	echo $closeprices['isin']."=".$oldisin;
				
				
				if($closeprices['isin']==$oldisin)
				{
				  $isinUpdateQuery='UPDATE  tbl_share  set isin ="'.$newisin.'" where indxx_id="'.$indxxKey.'"  and isin="'.$closeprices['isin'].'"'; 				$this->db->query($isinUpdateQuery);	
				  $isintickerUpdateQuery='UPDATE  tbl_indxx_ticker  set isin ="'.$newisin.'" where indxx_id="'.$indxxKey.'"  and isin="'.$closeprices['isin'].'"'; 
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
		
	// file_put_contents('../files/backup/postopendata'.date("Y-m-d-H-i-s").time().'.json', json_encode($final_array));	
	}
			//exit;
			
			//$this->pr($final_array,true);
			
			if(!empty($final_array))
	{
		$this->log_error(log_file, "in Index calculation after data prepration ");
		
		foreach($final_array as $key=>$closeIndxx)
		{
			
			$this->log_error(log_file, "Calculating index  : ". $closeIndxx['code']);
			 $newindexvalue=0;
			 $newindexvalue2=0;
			
			if(!$closeIndxx['client'])
			$file="../files/ca-output/Opening-".$closeIndxx['code']."-".$datevalue2.".txt";
			else
			$file="../files/ca-output/".$closeIndxx['client']."/Opening-".$closeIndxx['code']."-".$datevalue2.".txt";
			
				$client_folder = "../files/ca-output/".$closeIndxx['client']."/";
			if (!file_exists($client_folder))
			mkdir($client_folder, 0777, true);
			
			$open=fopen($file,"w+");

			$entry1='Date'.",";
			$entry1.=date("Y-m-d",strtotime($datevalue2)).",\n";
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
			}	*/		

		}
		}
		//echo $closeIndxx['index_value']['divisor_impact'];
		//echo "<br>";
		 $newDivisorforindxx=$closeIndxx['index_value']['olddivisor']+$closeIndxx['index_value']['divisor_impact'];
		//echo "<br>";
	//	 $closeIndxx['index_value']['indxx_value']."<br>";
//		 $newMarketValue/$closeIndxx['index_value']['newdivisor'];


if($closeIndxx['divpvalue'])
{
	
	
	//	$insertQuery='update tbl_indxx set divpvalue="'.$closeIndxx['divpvalue'].'" where id="'.$closeIndxx['id'].'" ';
	//	$this->db->query($insertQuery);	
		
	$newMarketValue+=$closeIndxx['divpvalue'];
	$newindexvalue2=$newMarketValue/$newDivisorforindxx;
	 $newindexvalue=number_format((($newMarketValue)/$newDivisorforindxx),2,'.','');
}
else
 {$newindexvalue=number_format(($newMarketValue/$newDivisorforindxx),2,'.','');
$newindexvalue2= $newMarketValue/$newDivisorforindxx;
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
	 $this->db->query("insert into tbl_weights_open (isin,weight,price,share,date,code,indxx_id) values " .implode(",",$weightArray).";");
}
 $insertQuery='INSERT into tbl_indxx_value_open (indxx_id,code,market_value,indxx_value,date,olddivisor,newdivisor) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.$newMarketValue.'","'.$newindexvalue2.'","'.$datevalue2.'","'.$closeIndxx['index_value']['olddivisor'].'","'.$newDivisorforindxx.'")';
		$this->db->query($insertQuery);	
		
			$entry2=$newindexvalue.",\n";
			$entry2.="Divisor,".$newDivisorforindxx.",\n";
		$entry2.="Market Value,".$newMarketValue.",\n\n";
			
			//echo $entry1.$entry2.$entry3.$entry4;
			//exit;
			
if($open){   
 if(   fwrite($open,$entry1.$entry2.$entry3.$entry4))
{
	
	
//$insertlogQuery='INSERT into tbl_indxx_log (type,indxx_id,value) values ("0","'.$closeIndxx['id'].'","'.mysql_real_escape_string($entry1.$entry2.$entry3.$entry4).'")';
	//	$this->db->query($insertlogQuery);
	
 fclose($open);
$filetext= "file Written for ".$closeIndxx['code']."<br>";
$this->log_info(log_file, "Calculated index  : ". $closeIndxx['code'].",file is available on :".$file);
}
}  
		
		
		unset($final_array[$key]);
		}
				unset($final_array);
		
	}
			
		
		
		//echo $totalindexes."=>".$page;
		//exit;
		
			//echo "Redirect Next";	
			
			$this->saveProcess(1);
			
			//$this->Redirect2("index.php?module=calcindxxopeningtemp&date=" .$datevalue2. "&log_file=" . log_file, "", "");	
		
		
	}
	function calcindxxopeningtemp()
	{
		echo "in 2nd block";
//	$priorityArray=max($_SESSION['priority']);
		if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));
	
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
		
		 $datevalue2=date;
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
		//  file_put_contents('../files/backup/preopentempdata'.date("Y-m-d-H-i-s").'.json', json_encode($final_array));
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
				//  file_put_contents('../files/backup/postopentempdata'.date("Y-m-d-H-i-s").'.json', json_encode($final_array));

	}
	//	$this->pr($final_array,true);
	if(!empty($final_array))
	{
		$this->log_error(log_file, "in Index calculation after data prepration ");
		foreach($final_array as $key=>$closeIndxx)
		{
			$file="../files/ca-output_upcomming/Opening-".strtolower($closeIndxx['code'])."p-".$datevalue2.".txt";
			$newindexvalue=0;
			$newindexvalue2=0;
			$client_folder = "../files/ca-output_upcomming/";
			if (!file_exists($client_folder))
			mkdir($client_folder, 0777, true);
			
	$this->log_error(log_file, "Calculating index  : ". $closeIndxx['code']);
			$open=fopen($file,"w+");

			$entry1='Date'.",";
			$entry1.=$datevalue2.",\n";
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
	$newindexvalue2=$newMarketValue/$newDivisorforindxx;
	 $newindexvalue=number_format((($newMarketValue)/$newDivisorforindxx),2,'.','');
}
else
 {$newindexvalue=number_format(($newMarketValue/$newDivisorforindxx),2,'.','');
 $newindexvalue2=$newMarketValue/$newDivisorforindxx;
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


		  	$insertQuery='INSERT into tbl_indxx_value_open_temp (indxx_id,code,market_value,indxx_value,date,olddivisor,newdivisor) values ("'.$closeIndxx['id'].'","'.$closeIndxx['code'].'","'.$newMarketValue.'","'.$newindexvalue2.'","'.$datevalue2.'","'.$closeIndxx['index_value']['olddivisor'].'","'.$newDivisorforindxx.'")';
		$this->db->query($insertQuery);	
		
			$entry2=$newindexvalue.",\n";
				$entry2.="Divisor,".$newDivisorforindxx.",\n";
		$entry2.="Market Value,".$newMarketValue.",\n\n";
if($open){   
 if(   fwrite($open,$entry1.$entry2.$entry3.$entry4))
{
	
	
	
//$insertlogQuery='INSERT into tbl_indxx_temp_log (type,indxx_id,value) values ("0","'.$closeIndxx['id'].'","'.mysql_real_escape_string($entry1.$entry2.$entry3.$entry4).'")';
//		$this->db->query($insertlogQuery);
	
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
			
			//$this->Redirect2("index.php?module=compositopen&date=" .$datevalue2. "&log_file=" . log_file, "", "");	
	//$this->Redirect2("index.php?module=notifyforca","","");	
	}
	
	function compositopen()
	{
		echo "in function block 3";
		//echo $this->_date;

			if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));

		if($_GET['log_file'])
			define("log_file", $_GET['log_file']);
				$this->log_info(log_file, "In Composite Opening generattion ");
		
	$clientData=$this->db->getResult("select id,ftpusername from tbl_ca_client where status='1'");
	//$this->pr($clientData,true);
	
	///$date=date('Y-m-d',strtotime($this->_date)-86400);
	$date=date;
	
	//$date="2014-03-28";
	if(!empty($clientData))
	{
		foreach($clientData as $client)
		{
				$file="../files/ca-output/".$client['ftpusername']."/compositopening-".$date.".txt";
				
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
					
				$data=	$this->db->getResult("select market_value,indxx_value from tbl_indxx_value_open where indxx_id='".$index['id']."' and date='".$date."'");
				$entry1.=$index['name'].','.$index['code'].','.$data['market_value'].','.$data['indxx_value'].",\r\n";
				
				
				//$this->pr($data);
				}	
								
				}
				
				$open=fopen($file,"w+");
					if($open){   
 if(   fwrite($open,$entry1))
{
	echo "file Written Successfully ";
	$this->log_info(log_file, "composit file written for ". $client['ftpusername']);
	
	}
}	
				//$this->pr($indexes);
		
		}
	}
	$this->saveProcess(2);
	//$this->Redirect2("index.php?module=notifyforca&date=" .$datevalue2. "&log_file=" . log_file, "", "");	
	}
	function notifyforca()
		{	
			echo "in block 4";
			if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));
			define("log_file", $_GET['log_file']);
			$this->log_info(log_file, "In Notify for corporate action ");
			$datevalue2=$this->_date;
			if(date('D')=="Mon")
			{$text='<br>Please Change The Corporate Action Request File date range from  : '.date("Y-m-d",strtotime(date("Y-m-d"))-(7*86400)).'  to '.date("Y-m-d",strtotime(date("Y-m-d"))+(60*86400)).'<br>';
		
		$emailQueries='select email from tbl_ca_user where status="1" and type="1" union select email from tbl_database_users where status="1"';
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
			
			$msg='Hi <br>'.$text." <br>Thanks <br>";
					
					// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			// Additional headers
			//$headers .= 'To: '.$dbuser['name'].' <'.$dbuser['email'].'>'. "\r\n";
$headers .= 'From: Indexing <indexing@indxx.com>' . "\r\n"."CC: indexing@indxx.com". "\r\n";
					//echo $emailsids;
					/*	if(mail( $emailsids,"Corporate Action Weekly Update",$msg,$headers))
					{
						echo "Mail Send ";
						
						//echo "Mail sent to : ".$dbuser['name']."<br>";	
					}
					else
					{
						echo "Mail not sent";	
					}*/
					
			
			
		}
			}
			$this->saveProcess(1);
		//$this->Redirect2("index.php?module=calcftpopen&date=" .$datevalue2. "&log_file=" . log_file, "", "");	
		}
	
   function calcftpopen()
	{
		echo "in function block 5";
		if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));
	
	define("log_file", $_GET['log_file']);
			$this->log_info(log_file, "In Calc FTP Open  ");
//	$this->pr($_SESSION,true);
	$this->update_process("Opening",date,"1");	
	 $datevalue2=date;
/*	

// set up basic connection
$conn_id = ftp_connect("ftp.processdo.com");

// login with username and password
$login_result = ftp_login($conn_id, "icaitest@processdo.com", 'icaitest@2014');

$file2 = '../files2/ca-output/pga/Opening-IPJAS-'. $datevalue2.'.txt';
$remote_file2 = 'Opening-IPJAS-'. $datevalue2.'.txt';

// upload a file
if (ftp_put($conn_id, $remote_file2, $file2, FTP_ASCII)) {
 echo "successfully uploaded $file2\n";
} else {
 echo "There was a problem while uploading $file\n";
}
$file3 = '../files2/ca-output/pga/Opening-IPJAR-'. $datevalue2.'.txt';
$remote_file3 = 'Opening-IPJAR-'. $datevalue2.'.txt';

// upload a file
if (ftp_put($conn_id, $remote_file3, $file3, FTP_ASCII)) {
 echo "successfully uploaded $file3\n";
} else {
 echo "There was a problem while uploading $file\n";
}



// close the connection

	$this->saveProcess(1);
ftp_close($conn_id);
*/
	
				$this->log_info(log_file, "Opening Process Finished .");
				echo"at the end";
			//	$this->Redirect2("../multicurrency2/db_backup_open.php?date=" .date. "&log_file=" . basename(log_file),"","");	
				$url="../multicurrency2/db_backup_open.php?date=" .date. "&log_file=" . basename(log_file);
				$link="<script type='text/javascript'>
window.open('".$url."');  
</script>";
echo $link;
	}
	
}
?>