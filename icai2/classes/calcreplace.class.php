<?php

class Calcreplace extends Application{

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
log_info("In CalcReplace for Live  ");
		
	
	 $datevalue2=date;	
	/*if($_SESSION['currentPriorityIndex']==0)
		
	{	
	*/
	$indxxs=	$this->db->getResult("select id,indxx_id from tbl_replace_runnindex_req where startdate='".$datevalue2."' and adminapprove='1' and dbapprove='1'",true);
	//$this->pr($indxxs,true);
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
	
		$indxx_value=$this->db->getResult("select tbl_indxx_value.* from tbl_indxx_value where indxx_id='".$indxx['indxx_id']."' order by date desc ",false,1);	
			//$this->pr($indxx_value,true);
			if(!empty($indxx_value))
			{
			$final_array[$indxx['id']][$indxx['indxx_id']]['index_value']=$indxx_value;
			$datevalue=$indxx_value['date'];
			//$final_array[$indxx['id']][$indxx['indxx_id']]['date']=$indxx_value['date'];
			}
			
				//$query="SELECT  it.id,it.name,it.isin,it.ticker,curr,divcurr,sedol,cusip,countryname,(select price from tbl_final_price fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$indxx['indxx_id']."') as calcprice,(select localprice from tbl_final_price fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$indxx['indxx_id']."') as localprice,(select currencyfactor from tbl_final_price fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$indxx['indxx_id']."') as currencyfactor,(select share from tbl_share sh where sh.isin=it.isin  and sh.indxx_id='".$indxx['indxx_id']."') as calcshare FROM `tbl_indxx_ticker` it where it.indxx_id='".$indxx['indxx_id']."'";			
	
	 $query="SELECT  it.id, it.name, it.isin, it.ticker, 
							 sh.share as calcshare ,price as calcprice,localprice,currencyfactor
							FROM `tbl_indxx_ticker` it 
							left join tbl_final_price fp on fp.isin=it.isin
							left join tbl_share sh on sh.isin=it.isin where it.indxx_id='" . $indxx['indxx_id'] . "' 
							 and sh.indxx_id='" . $indxx['indxx_id'] . "' and fp.indxx_id='".$indxx['indxx_id']."' and fp.date='".$datevalue."'";

		$indxxprices=	$this->db->getResult($query,true);
		
		//echo $query2="select price,localprice,currencyfactor,isin from tbl_final_price where indxx_id='".$indxx['indxx_id']."' and date='".$datevalue."'";
		//$indxxprices2=	$this->db->getResult($query2,true);
		
		//	$this->pr($indxxprices,true);
			
	$final_array[$indxx['id']][$indxx['indxx_id']]['olddata']=$indxxprices;

	//echo 	$datevalue;
	
	$oldsecurity=	$this->db->getResult("select security_id from tbl_replace_runnsecurity where req_id='".$indxx['id']."' and  indxx_id='".$indxx['indxx_id']."' ",true);
	
	$final_array[$indxx['id']][$indxx['indxx_id']]['replacesecurity']=$oldsecurity;
	$newsecurities=	$this->db->getResult("select name, 	isin,ticker,curr,divcurr,sedol,cusip,countryname from tbl_runnsecurities_replaced where req_id='".$indxx['id']."' and  indxx_id='".$indxx['indxx_id']."' ",true);
	
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
	$countnewSeurities= count($indxx_array['newsecurity']);		
	if($countnewSeurities)
	{if(!empty($indxx_array['replacesecurity']))
	{
		$tempmarketcap=0;
		$TempWeight=0;
		
	foreach($indxx_array['replacesecurity'] as $replaceSecurity)
	{
	//$this->pr($replaceSecurity);
			foreach($indxx_array['olddata'] as  $oldsecuritykey=>$oldsecurity)
			{
				if($oldsecurity['id']==$replaceSecurity['security_id'])
				{
				//echo "Got It.<br>";
				$tempmarketcap+=$oldsecurity['calcshare']*$oldsecurity['calcprice'];
				//echo $oldsecuritykey;
				
				 $deleteSecurityQuery='Delete from tbl_indxx_ticker where id="'.$oldsecurity['id'].'"'; 
				$this->db->query($deleteSecurityQuery);	
				//echo "<br>";
				 $deletepriceQuery='Delete from tbl_final_price where indxx_id="'.$id.'" and  isin ="'.$oldsecurity['isin'].'" and date ="'.$indxx_array['index_value']['date'].'" '; 
				$this->db->query($deletepriceQuery);	
			//	echo "<br>";
				
				$deleteshareQuery='Delete from tbl_share where indxx_id="'.$id.'" and  isin ="'.$oldsecurity['isin'].'" '; 
				//echo "<br>";
				
				$this->db->query($deleteshareQuery);	
				
				$deleteWeightsQuery='delete from tbl_weights where indxx_id="'.$id.'" and  isin ="'.$oldsecurity['isin'].'" and date="'.$indxx_array['index_value']['date'].'"'; 
				//echo "<br>";
				
				$this->db->query($deleteWeightsQuery);	
				$deleteWeightsopenQuery='delete from tbl_weights_open where indxx_id="'.$id.'" and  isin ="'.$oldsecurity['isin'].'" and date="'.$indxx_array['index_value']['date'].'"'; 
				//echo "<br>";
				
				$this->db->query($deleteWeightsopenQuery);	
				
				unset($final_array[$id]['olddata'][$oldsecuritykey]);
				
				}
			
			}
	
	}
//	echo $tempmarketcap;
if($tempmarketcap)
{
$TempWeight=$tempmarketcap/$countnewSeurities;
}
if(!empty($indxx_array['newsecurity']))
{foreach($indxx_array['newsecurity'] as $newsecuritykey=> $newsecurity)
{
$share=$TempWeight/$newsecurity['calcprice'];
	$final_array[$id]['newsecurity'][$newsecuritykey]['calcshare']=$share;
	$newsecurity['calcshare']=$share;
	
	$final_array[$id]['olddata'][]=$newsecurity;
	
//	$this->pr($newsecurity);
	
	
	
	$insertTicker='Insert into tbl_indxx_ticker set name="'.$newsecurity['name'].'", isin="'.$newsecurity['isin'].'", ticker="'.$newsecurity['ticker'].'", curr="'.$newsecurity['curr'].'", divcurr="'.$newsecurity['divcurr'].'",countryname="'.$newsecurity['countryname'].'",cusip="'.$newsecurity['cusip'].'",sedol="'.$newsecurity['sedol'].'",indxx_id="'.$id.'", status="1",weight="0"'; 
	
	$this->db->query($insertTicker);	
			//	echo "<br>";
	
	
	
$insertPrice='Insert into tbl_final_price set date="'.$indxx_array['index_value']['date'].'", isin="'.$newsecurity['isin'].'", localprice="'.$newsecurity['localprice'].'", price="'.$newsecurity['calcprice'].'", currencyfactor="'.$newsecurity['currencyfactor'].'",indxx_id="'.$id.'"'; 
	
	$this->db->query($insertPrice);
	
			//	echo "<br>";
	
		$insertshare='Insert into tbl_share set date="'.$indxx_array['index_value']['date'].'", isin="'.$newsecurity['isin'].'", share="'.$newsecurity['calcshare'].'",indxx_id="'.$id.'"'; 
	
	$this->db->query($insertshare);	
		
		$insertweights='Insert into tbl_weights set date="'.$indxx_array['index_value']['date'].'", isin="'.$newsecurity['isin'].'", share="'.$newsecurity['calcshare'].'",code="'.$indxx_array['index_value']['code'].'",price="'.$newsecurity['calcprice'].'",weight="'.$TempWeight.'",indxx_id="'.$id.'"'; 
	
	$this->db->query($insertweights);	
		$insertweightsopen='Insert into tbl_weights_open set date="'.$indxx_array['index_value']['date'].'", isin="'.$newsecurity['isin'].'", share="'.$newsecurity['calcshare'].'",code="'.$indxx_array['index_value']['code'].'",price="'.$newsecurity['calcprice'].'",weight="'.$TempWeight.'",indxx_id="'.$id.'"'; 
	
	$this->db->query($insertweightsopen);	
		
			//	echo "<br>";
	
}
}
	
	}
	}
	
//	$this->pr($indxx_array,true);
	/*echo $indxx_array['index_value']['indxx_value'];
	echo "<br>";
	$olddivisor= $indxx_array['index_value']['newdivisor'];
	
	$newMarketCap=0;
	foreach ( $indxx_array['olddata'] as $newsecurities)
	{
	$newMarketCap+=$newsecurities['calcshare']*$newsecurities['calcprice'];
	}
	echo 	$newMarketCap/$olddivisor;*/
	
	
	
	}
	}
	}
	}
	
	$this->saveProcess(1);
	
		//$this->Redirect("index.php?module=calcindxxopening","","");	
	
//$this->pr($final_array,true);
	
		
	//}
		$this->Redirect("index.php?module=calccapub&log_file=".basename(log_file)."&date=".date,"","");
	
	}
}?>