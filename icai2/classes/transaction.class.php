<?php

class Transaction extends Application{

	function __construct()
	{
		parent::__construct();
		//echo "hello";
		//$this->checkUserSession();
	}
	
	
	function index()
	{
		if($_GET['log_file'])
		define("log_file",get_logs_folder().$_GET['log_file']);
		if($_GET['date'])
		define("date",$_GET['date']);
		else
		define("date",date("Y-m-d"));
		log_info("In Calc Delsit For Upcomimg  ");
		$datevalue=date;
		$arr = array();
		$id = array();
		$price_array = array();
		$count=0;
		$count1=0;
		//echo $datevalue;
		$query_date=$this->db->getResult("select indxx_id from tbl_replacement where date ='".$datevalue."'");
		
		 $query_price =$this->db->getResult("select isin,price from tbl_price where date='".$datevalue."'") or die("err".mysql_error());
			//$this->pr($query_price,true);
			foreach($query_price as $row1 => $p)
			{
					//$this->pr($p,true);
					$price_array[$p['isin']] = $p['price'];
			} 
		
		if(!empty($query_date))
		{
			//$this->pr($query_date,true);
			foreach($query_date as $row => $col)
			{
				$ticker_query = $this->db->getResult("select isin from tbl_indxx_ticker where indxx_id='".$col['indxx_id']."'");
				
				
				
				foreach($ticker_query as $row2 => $t)
				{
					//$available_data = "select * from tbl_final_price_temp where indxx_id='".$col['indxx_id']."' and isin='".$t['isin']."' and date ='".$datevalue."'";
					//$data=$this->db->getResult($available_data);
					//echo "insert into tbl_final_price_temp (indxx_id,price,localprice,isin,date) values ('".$col['indxx_id']."','".$price_array[$t['isin']]."','".$price_array[$t['isin']]."','".$t['isin']."','".$datevalue."')";
						//echo "<Br/>";
						$final_query = $this->db->query("insert into tbl_final_price_temp (indxx_id,price,localprice,isin,date) values ('".$col['indxx_id']."','".$price_array[$t['isin']]."','".$price_array[$t['isin']]."','".$t['isin']."','".$datevalue."')") or die("error".mysql_error());
						$count++;
					
				}
				
			}
			/* $query_price =$this->db->getResult("select isin,price from tbl_price where date='".$datevalue."'") or die("err".mysql_error());
			//$this->pr($query_price,true);
			foreach($query_price as $row1 => $p)
			{
					//$this->pr($p,true);
					$price_array[$p['isin']] = $p['price'];
			} */
			//$this->pr($price_array,true);
		} 
		
		else{
			echo "No transaction";
			//exit;
		}
		
		echo "Inserted".$count."records in tbl_final_price_temp<br/>";
		//echo "Updated".$count1."records in tbl_final_price_temp";
		
		//exit;
	}
	
	function goliveIndex($indexes)
	{
		if(!empty($indexes))
		{
			foreach($indexes as $indxx)
			{
			$lastrundatearray=$this->db->getResult("SELECT date FROM `tbl_indxx_value_temp` where indxx_id='".$indxx."' order by date desc",false,1);
			//$this->pr($lastrundate,true);
			$lastrundate=$lastrundatearray['date'];
			$indxx_data=$this->getUpcomingIndex($indxx,$lastrundate);
			//$this->pr($indxx_data);
			$this->checkandupdatelive($indxx_data,$lastrundate);
			}

		}
	//exit;
	}
	
	function runindex($indexes)
	{
	$lastrundatearray=$this->db->getResult("SELECT date FROM `tbl_system_task_complete` where name='Closing' and status='1' order by date desc",false,1);
	//$this->pr($lastrundate,true);
	$lastrundate=$lastrundatearray['date'];
	$datevalue=$lastrundate;
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
	
	
}?>