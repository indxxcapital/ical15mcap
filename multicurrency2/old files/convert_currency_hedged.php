<pre><?php
date_default_timezone_set("Asia/Kolkata"); 
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
include("function.php");
$date=date("Y-m-d",strtotime(date("Y-m-d"))-86400);
//$date='2015-01-16';
$final_price_array=array();
$indexarray=array();
$emailsids='';
$dbuseremailsids='';
$indxx=selectrow(array('id','name','code','curr'),'tbl_indxx',array("currency_hedged"=>1));
if(!empty($indxx))
{
foreach($indxx as $key=>$index)
{
//print_r($index);
//exit;
$query="Select date from tbl_final_price where indxx_id='".$index['id']."' order by date desc limit 0,1";
$res=mysql_query($query);
if(mysql_num_rows($res)==0)
{


//echo "default conversion ";

}else{

//print_r(mysql_fetch_assoc($res));
$resdate=mysql_fetch_assoc($res);
 $lastConversionDate=$resdate['date'];
//exit;


//echo "SELECT it.isin,it.price,it.localprice,(select price from tbl_prices_local_curr pf where pf.isin=it.isin  and pf.date='".$date."') as localpricetoday  FROM `tbl_final_price` it where it.indxx_id='".$index['id']."' and it.date='".$lastConversionDate."'";


 $pricequery=mysql_query("SELECT it.isin,it.price,it.localprice,(select price from tbl_prices_local_curr pf where pf.isin=it.isin  and pf.date='".$date."') as localpricetoday  FROM `tbl_final_price` it where it.indxx_id='".$index['id']."' and it.date='".$lastConversionDate."'");
if(mysql_num_rows($pricequery))
{
while($priceRow=mysql_fetch_assoc($pricequery))
{
//print_r($priceRow);
//exit;

if($priceRow['localprice'] && $priceRow['localpricetoday'] )
{

$change=($priceRow['localpricetoday']-$priceRow['localprice'])/$priceRow['localprice'];
$final_price_array[$index['id']][$priceRow['isin']]['price']=$priceRow['price']*(1+$change);
//$final_price_array[$index['id']][$priceRow['isin']]['oldusd']=$priceRow['price'];
//$final_price_array[$index['id']][$priceRow['isin']]['oldlocal']=$priceRow['localprice'];
//$final_price_array[$index['id']][$priceRow['isin']]['change']=$change;


$final_price_array[$index['id']][$priceRow['isin']]['localprice']=$priceRow['localpricetoday'];
}

}
}
//echo "currency headged";

}
}
//print_r($final_price_array);
//exit;
if(!empty($final_price_array)){
	
	foreach($final_price_array as $index_key=>$security)
	{
		if(!empty($security))
		{
		foreach($security as $security_key=>$prices)
		{
		$fpquery="INSERT into tbl_final_price  (indxx_id,isin,date,price,localprice,currencyfactor) values ('".$index_key."','".$security_key."','".$date."','".$prices['price']."','".$prices['localprice']."','0') ";
mysql_query($fpquery);

		}
		}
		
	
	}
	

}
}

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);

echo 'Page generated in '.$total_time.' seconds. ';
saveProcess(2);
mysql_close();
webopen("http://192.169.255.12/icai2/index.php?module=calcindxxclosing");
/*echo '<script>document.location.href="http://97.74.65.118/icai2/index.php?module=calcindxxclosing";</script>';
*/

?>