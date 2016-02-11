<?php

class Calctest extends Application{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function index()
	{
	
		if($_GET['date'])
$datevalue=$_GET['date'];
else
	$datevalue=date("Y-m-d");

if(date("D",strtotime($datevalue))=="Fri")
 $livedate=date("Y-m-d",strtotime($datevalue)+86400*3);
else
 $livedate=date("Y-m-d",strtotime($datevalue)+86400);


$indxxs=$this->db->getResult("select tbl_indxx_temp.* from tbl_indxx_temp  where status='1' and usersignoff='1' and dbusersignoff='1' and submitted='1' and calcdate='".$datevalue."' or dateStart='".$livedate."'",true);	
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
							and fp.indxx_id='" . $row['id'] . "'  and fp.date='" . $datevalue . "'";			
		
		
			$indxxprices=	$this->db->getResult($query,true);	
		
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

$this->pr($final_array,true);


}
}