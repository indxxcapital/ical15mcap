<?php

class Calcdelist extends Application{

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
log_info("In CalcDelist for Live  ");
	/*	if($_SESSION['currentPriorityIndex']==0)
		
	{	*/
	 $datevalue2=date;	
	//exit;
	
	
	$indxxs=	$this->db->getResult("select id,indxx_id from tbl_delist_runnindex_req where startdate='".$datevalue2."' and adminapprove='1' ",true);
	//$this->pr($indxxs,true);
		$final_array=array();
	
	
	if(!empty($indxxs))	
	{
	foreach ($indxxs as $indxx)
	{
	
		$indxx_value=$this->db->getResult("select tbl_indxx_value.* from tbl_indxx_value where indxx_id='".$indxx['indxx_id']."' order by date desc ",false,1);	
			//$this->pr($indxx_value,true);
			if(!empty($indxx_value))
			{
			$final_array[$indxx['indxx_id']]['index_value']=$indxx_value;
			$datevalue=$indxx_value['date'];
			}
			
				//$query="SELECT  it.id,it.name,it.isin,it.ticker,curr,divcurr,(select price from tbl_final_price fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$indxx['indxx_id']."') as calcprice,(select localprice from tbl_final_price fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$indxx['indxx_id']."') as localprice,(select currencyfactor from tbl_final_price fp where fp.isin=it.isin  and fp.date='".$datevalue."' and fp.indxx_id='".$indxx['indxx_id']."') as currencyfactor,(select share from tbl_share sh where sh.isin=it.isin  and sh.indxx_id='".$indxx['indxx_id']."') as calcshare FROM `tbl_indxx_ticker` it where it.indxx_id='".$indxx['indxx_id']."'";	
				
				$query="SELECT  it.id,it.name,it.isin,it.ticker,it.curr,it.divcurr,fp.price as calcprice,fp.localprice as localprice, sh.share as calcshare FROM `tbl_indxx_ticker` it
left join tbl_final_price fp on fp.isin=it.isin
left join tbl_share sh on sh.isin=it.isin
 where it.indxx_id='".$indxx['indxx_id']."'
 and fp.indxx_id='".$indxx['indxx_id']."'
 and sh.indxx_id='".$indxx['indxx_id']."'
 and fp.date='".$datevalue."' 
  ";		
		
		$indxxprices=	$this->db->getResult($query,true);
	//		$this->pr($indxxprices,true);
			
	$final_array[$indxx['indxx_id']]['olddata']=$indxxprices;

	//echo 	$datevalue;
	
	$oldsecurity=	$this->db->getResult("select security_id from tbl_delist_runnsecurity where req_id='".$indxx['id']."' and  indxx_id='".$indxx['indxx_id']."' ",true);
	//$this->pr($oldsecurity,true);

	$final_array[$indxx['indxx_id']]['removesecurity']=$oldsecurity;
	
	}
	}
	
	
	//$this->pr($final_array,true);
	
	if(!empty($final_array))
	{
	foreach($final_array as $id=>$indxx_array)
	{
		
		$tempMarketCap=0;
		
		
	if(!empty($indxx_array['removesecurity']) && !empty($indxx_array['olddata']))
	{
		foreach($indxx_array['removesecurity'] as $removedSecurtity)
		{
		//$this->pr($removedSecurtity);
		
		if(!empty($removedSecurtity))
		{
				foreach($indxx_array['olddata'] as $oldsecuritykey=>$oldsecuriti)
				{
					if($oldsecuriti['isin']==$removedSecurtity['security_id'])
					{
			//		echo "got it<br>";
				//echo $oldsecuritykey;
				
					$tempMarketCap+=$oldsecuriti['calcshare']*$oldsecuriti['calcprice'];
				
				
				
				 $deleteSecurityQuery='Delete from tbl_indxx_ticker where isin="'.$oldsecuriti['isin'].'"'; 
			$this->db->query($deleteSecurityQuery);	
				//echo "<br>";
				 $deletepriceQuery='Delete from tbl_final_price where indxx_id="'.$id.'" and  isin ="'.$oldsecuriti['isin'].'" and date ="'.$indxx_array['index_value']['date'].'" '; 
			$this->db->query($deletepriceQuery);	
				//echo "<br>";
				
		$deleteshareQuery='Delete from tbl_share where indxx_id="'.$id.'" and  isin ="'.$oldsecuriti['isin'].'" '; 
			//	echo "<br>";
				
				$this->db->query($deleteshareQuery);	
				
				
			$deleteshareQuery='Delete from tbl_weights_open where indxx_id="'.$id.'" and  isin ="'.$oldsecuriti['isin'].'" and date ="'.$indxx_array['index_value']['date'].'"  '; 
			//	echo "<br>";
				
				$this->db->query($deleteshareQuery);	
				
					$deleteshareQuery='Delete from tbl_weights where indxx_id="'.$id.'" and  isin ="'.$oldsecuriti['isin'].'" and date ="'.$indxx_array['index_value']['date'].'"  '; 
			//	echo "<br>";
				
				$this->db->query($deleteshareQuery);
					
					$deleteshareQuery='Delete from tbl_marketcap_close where indxx_id="'.$id.'" and  isin ="'.$oldsecuriti['isin'].'" and date ="'.$indxx_array['index_value']['date'].'"  '; 
			//	echo "<br>";
				
				$this->db->query($deleteshareQuery);
				
				
				unset($final_array[$id]['olddata'][$oldsecuritykey]);
				
					}
				
				}
		}
		
		
		}
	}
	
	
	//echo $tempMarketCap;
	//echo "<br>";
	
	
	//echo $id;
	
	
//$this->pr($final_array,true);
	
	if($tempMarketCap)
	{
		$newDivisor=0;
		$newDivisor=$indxx_array['index_value']['olddivisor'];
		
		$newDivisor=$newDivisor-($tempMarketCap/$indxx_array['index_value']['indxx_value']);
		
	$updateQuery='update tbl_indxx_value set newdivisor="'.$newDivisor.'" where  date="'.$indxx_array['index_value']['date'].'" and indxx_id="'.$id.'"';
		
$this->db->query($updateQuery);	
		
		
		
	}
	
	/*if($tempMarketCap)
	{
		$newDivisor=0;
		$newDivisor=$indxx_array['index_value']['newdivisor'];
		
	$newDivisor=$newDivisor-($tempMarketCap/$indxx_array['index_value']['indxx_value']);

echo $indxx_array['index_value']['indxx_value'];
echo "<br>";
$marketCap=0;
foreach($final_array as $newindxx)
{
	foreach($newindxx['olddata'] as $newSecurity){
	
		$marketCap+=$newSecurity['calcshare']*$newSecurity['calcprice'];
	}
}
echo $marketCap/$newDivisor;




	}
	*/
	
	}
//	echo $tempMarketCap;
	
	}
	
	$this->saveProcess(1);
	
	///$this->pr($final_array,true);
	
	//}
	$this->Redirect("index.php?module=calcdelisttemp&log_file=".basename(log_file)."&date=".date,"","");	
	
	
	}
}?>