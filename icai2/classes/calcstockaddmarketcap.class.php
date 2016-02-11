<?php

class Calcstockaddmarketcap extends Application{

	function __construct()
	{
		parent::__construct();
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
$datevalue2=date;
log_info("In CalcReplace for Live  ");



$indxxs=	$this->db->getResult("select id,indxx_id from tbl_replace_runnindex_req where startdate='".$datevalue2."' and adminapprove='1' and dbapprove='1'",true);
//	$this->pr($indxxs,true);
		$final_array=array();
	
	
	if(!empty($indxxs))	
	{
	foreach ($indxxs as $indxx)
	{
	
	
	$indxx_data=$this->db->getResult("select tbl_indxx.* from tbl_indxx where id='".$indxx['indxx_id']."'");	
	if(!empty($indxx_data))
	{
		$final_array[$indxx['id']][$indxx['indxx_id']]['details']=$indxx_data;
	
	}
	
//echo "select tbl_indxx_value.* from tbl_indxx_value where indxx_id='".$indxx['indxx_id']."' order by date desc ";
		$indxx_value=$this->db->getResult("select tbl_indxx_value.* from tbl_indxx_value where indxx_id='".$indxx['indxx_id']."' order by date desc ",false,1);	
		//	$this->pr($indxx_value,true);
			if(!empty($indxx_value))
			{
			$final_array[$indxx['id']][$indxx['indxx_id']]['index_value']=$indxx_value;
			$datevalue=$indxx_value['date'];
			//$final_array[$indxx['id']][$indxx['indxx_id']]['date']=$indxx_value['date'];
			}
			//echo "select sum(marketcap) as total_marketcap from tbl_marketcap_close where indxx_id='".$indxx['id']."' and  date='".$datevalue."' ";
				$marketcap=	$this->db->getResult("select sum(marketcap) as total_marketcap from tbl_marketcap_close where indxx_id='".$indxx['id']."' and  date='".$datevalue."' ",false);	
	$final_array[$indxx['id']][$indxx['indxx_id']]['totalMarketcap']=$marketcap['total_marketcap'];
	 /*echo $query="SELECT  it.id, it.name, it.isin, it.ticker, 
							 sh.share as calcshare ,price as calcprice,localprice,currencyfactor
							FROM `tbl_indxx_ticker` it 
							left join tbl_final_price fp on fp.isin=it.isin
							left join tbl_share sh on sh.isin=it.isin where it.indxx_id='" . $indxx['indxx_id'] . "' 
							 and sh.indxx_id='" . $indxx['indxx_id'] . "' and fp.indxx_id='".$indxx['indxx_id']."' and fp.date='".$datevalue."'";

		$indxxprices=	$this->db->getResult($query,true);*/
		
		//echo $query2="select price,localprice,currencyfactor,isin from tbl_final_price where indxx_id='".$indxx['indxx_id']."' and date='".$datevalue."'";
		//$indxxprices2=	$this->db->getResult($query2,true);
		
		//	$this->pr($indxxprices,true);
			
	/*$final_array[$indxx['id']][$indxx['indxx_id']]['olddata']=$indxxprices;*/

	//echo 	$datevalue;
	
	
	$newsecurities=	$this->db->getResult("select * from tbl_runnsecurities_replaced where req_id='".$indxx['id']."' and  indxx_id='".$indxx['indxx_id']."' ",true);
	
	if(!empty($newsecurities))
	{
		foreach ($newsecurities as $key=>$security)
		{
		$prices=$this->getSecurtyPrices($security['isin'],$security['curr'],$indxx_data['curr'],$datevalue);
		$newsecurities[$key]['calcprice']=$prices['calcprice'];
		$newsecurities[$key]['localprice']=$prices['localprice'];
		$newsecurities[$key]['currencyfactor']=$prices['currencyfactor'];
		}
	}
	
	
	
	
	
	$final_array[$indxx['id']][$indxx['indxx_id']]['newsecurity']=$newsecurities;
	
	
	//$newsecurity=	$this->db->getResult("select indxx_id from tbl_replace_tempindex_req where startdate='".$date."' and adminapprove='1' and dbapprove='1'",true);
	}
	}
	
//$this->pr($final_array,true);


	if (!empty($final_array))
	{
	//
		
	foreach($final_array as $rid=> $request_array)
	{
				if(!empty($request_array))
		{
foreach($request_array as $id=>$indxx_array)			
{
	
	if(!empty($indxx_array['newsecurity']) && !empty($indxx_array['index_value']))
	{
		foreach($indxx_array['newsecurity'] as $newsecurity)
		{
		$indxx_array['totalMarketcap']+=$newsecurity['value'];
		}
		
		
		}
		if(!empty($indxx_array['newsecurity']) && !empty($indxx_array['index_value']))
	{
		foreach($indxx_array['newsecurity'] as $newsecurity)
		{
			$weight=$newsecurity['value']/$indxx_array['totalMarketcap'];
			
			
			
		   $share=	$indxx_array['index_value']['market_value']*$weight/$newsecurity['calcprice'];
		//exit;
		//echo "<br>";
		if($share){
			
			$insertTicker='Insert into tbl_indxx_ticker set name="'.$newsecurity['name'].'", isin="'.$newsecurity['isin'].'", ticker="'.$newsecurity['ticker'].'", curr="'.$newsecurity['curr'].'", divcurr="'.$newsecurity['divcurr'].'",countryname="'.$newsecurity['countryname'].'",cusip="'.$newsecurity['cusip'].'",sedol="'.$newsecurity['sedol'].'",indxx_id="'.$id.'", status="1",weight="'.$weight.'"'; 
	
	$this->db->query($insertTicker);	
			$insertPrice='Insert into tbl_final_price set date="'.$indxx_array['index_value']['date'].'", isin="'.$newsecurity['isin'].'", localprice="'.$newsecurity['localprice'].'", price="'.$newsecurity['calcprice'].'", currencyfactor="'.$newsecurity['currencyfactor'].'",indxx_id="'.$id.'"'; 
	
	$this->db->query($insertPrice);
	
	
		$insertshare='Insert into tbl_share set date="'.$indxx_array['index_value']['date'].'", isin="'.$newsecurity['isin'].'", share="'.$share.'",indxx_id="'.$id.'"'; 
	
	$this->db->query($insertshare);	
		$insertmarketcap='Insert into tbl_marketcap_close set date="'.$indxx_array['index_value']['date'].'", isin="'.$newsecurity['isin'].'", marketcap="'.$newsecurity['value'].'",indxx_id="'.$id.'"'; 
	
	$this->db->query($insertmarketcap);	
	
	
	$insertweights='Insert into tbl_weights set date="'.$indxx_array['index_value']['date'].'", isin="'.$newsecurity['isin'].'", share="'.$share.'",code="'.$indxx_array['index_value']['code'].'",price="'.$newsecurity['calcprice'].'",weight="'.$weight.'",indxx_id="'.$id.'"'; 
	
		$this->db->query($insertweights);
		
		$insertweightsopen='Insert into tbl_weights_open set date="'.$indxx_array['index_value']['date'].'", isin="'.$newsecurity['isin'].'", share="'.$share.'",code="'.$indxx_array['index_value']['code'].'",price="'.$newsecurity['calcprice'].'",weight="'.$weight.'",indxx_id="'.$id.'"'; 
	
	$this->db->query($insertweightsopen);	
			
			
			log_info("Security added  ".$newsecurity['name'].",".$newsecurity['isin'].",".$newsecurity['ticker']);
			
			
			}
		
		
		}
		
	
	
	
	}
	
	
	 $query="SELECT  sum(sh.share*price)  as total_market_cap
							FROM tbl_final_price fp
							left join tbl_share sh on sh.isin=fp.isin where 
							  sh.indxx_id='" .$id . "' and fp.indxx_id='".$id."' and fp.date='".$datevalue."'";

		$indxxprices=	$this->db->getResult($query,false,1);
	if(!empty($indxxprices))
	{
	$totalMarketCap=$indxxprices['total_market_cap'];
	
		//echo $totalMarketCap;
		//exit;
		 $newDivisor=$indxx_array['index_value']['olddivisor']-(($indxx_array['index_value']['market_value']-$totalMarketCap)/$indxx_array['index_value']['indxx_value']);
		
		 $updateQuery='update tbl_indxx_value set newdivisor="'.$newDivisor.'" where  date="'.$indxx_array['index_value']['date'].'" and indxx_id="'.$id.'"';
		//exit;
$this->db->query($updateQuery);	
		log_info("Divisor Updated for ".$indxx_array['index_value']['code'] ." for date ".$indxx_array['index_value']['date']);
			
		
	//$indxx_array
	}
	
	}
	
	
	}
	
	
	}
	
	
	
	
	
	
	
	
	}

$this->Redirect("index.php?module=calccapub&log_file=".basename(log_file)."&date=".date,"","");	
}
}