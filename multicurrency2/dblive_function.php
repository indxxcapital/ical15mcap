<?php 
include("dbconfig_dblive.php");
function checkandupdateindex_live($indxxArray,$date){

if(!empty($indxxArray))
{
foreach($indxxArray as $indxx)
{
//print_r($indxx);
$indxx_id=0;

$index_res=mysql_query("Select id from tbl_indxx where code='".$indxx['code']."'");
if(mysql_num_rows($index_res)>0)
{$row=mysql_fetch_assoc($index_res);
$indxx_id=$row['id'];
mysql_query("delete from tbl_indxx_ticker where indxx_id='".$indxx_id."'");
mysql_query("delete from tbl_share where indxx_id='".$indxx_id."'");
mysql_query("delete from tbl_final_price where indxx_id='".$indxx_id."' and date >='".$date."'");
mysql_query("delete from tbl_indxx_value_open where indxx_id='".$indxx_id."' and date >='".$date."'");
mysql_query("delete from tbl_indxx_value where indxx_id='".$indxx_id."' and date >='".$date."'");
mysql_query("delete from tbl_weights where indxx_id='".$indxx_id."' and date >='".$date."'");
mysql_query("delete from tbl_weights_open where indxx_id='".$indxx_id."' and date >='".$date."'");
echo "Data Removed for indxx-".$indxx['code']."<br>";

mysql_query("update  tbl_indxx set name='".$indxx['name']."',code='".$indxx['code']."',investmentammount='".$indxx['investmentammount']."',	divpvalue='".$indxx['divpvalue']."',indexvalue='".$indxx['indexvalue']."',	divisor='".$indxx['divisor']."',type='".$indxx['type']."',cash_adjust='".$indxx['cash_adjust']."',curr='".$indxx['curr']."',status='".$indxx['status']."',dateAdded='".$indxx['dateAdded']."',lastupdated='".$indxx['lastupdated']."',dateStart='".$indxx['dateStart']."',usersignoff='".$indxx['usersignoff']."',dbusersignoff='".$indxx['dbusersignoff']."',submitted='".$indxx['submitted']."',	finalsignoff='".$indxx['finalsignoff']."',runindex='".$indxx['runindex']."',addtype='".$indxx['addtype']."',zone='".$indxx['zone']."',calcdate='".$indxx['calcdate']."',rebalance='".$indxx['rebalance']."',client_id='".$indxx['client_id']."',	display_currency='".$indxx['display_currency']."',ireturn='".$indxx['ireturn']."',	ica='".$indxx['ica']."',	recalc='".$indxx['recalc']."',div_type='".$indxx['div_type']."',currency_hedged='".$indxx['currency_hedged']."',priority='".$indxx['priority']."' where id='".$indxx_id."'");

}
else{
mysql_query("insert into tbl_indxx set name='".$indxx['name']."',code='".$indxx['code']."',investmentammount='".$indxx['investmentammount']."',	divpvalue='".$indxx['divpvalue']."',indexvalue='".$indxx['indexvalue']."',	divisor='".$indxx['divisor']."',type='".$indxx['type']."',cash_adjust='".$indxx['cash_adjust']."',curr='".$indxx['curr']."',status='".$indxx['status']."',dateAdded='".$indxx['dateAdded']."',lastupdated='".$indxx['lastupdated']."',dateStart='".$indxx['dateStart']."',usersignoff='".$indxx['usersignoff']."',dbusersignoff='".$indxx['dbusersignoff']."',submitted='".$indxx['submitted']."',	finalsignoff='".$indxx['finalsignoff']."',runindex='".$indxx['runindex']."',addtype='".$indxx['addtype']."',zone='".$indxx['zone']."',calcdate='".$indxx['calcdate']."',rebalance='".$indxx['rebalance']."',client_id='".$indxx['client_id']."',	display_currency='".$indxx['display_currency']."',ireturn='".$indxx['ireturn']."',	ica='".$indxx['ica']."',	recalc='".$indxx['recalc']."',div_type='".$indxx['div_type']."',currency_hedged='".$indxx['currency_hedged']."',priority='".$indxx['priority']."'");
$indxx_id=mysql_insert_id();
echo "new Index Id generated for indxx-".$indxx['code']."<br>";
}

if(!empty($indxx['tickers']))
{
	$ticker_inser_array=array();
foreach($indxx['tickers'] as $ticker)
{
$ticker_inser_array[]="('".mysql_real_escape_string($ticker['name'])."','".mysql_real_escape_string($ticker['isin'])."','".mysql_real_escape_string($ticker['ticker'])."','".mysql_real_escape_string($ticker['weight'])."','".mysql_real_escape_string($ticker['curr'])."','".mysql_real_escape_string($ticker['divcurr'])."','".mysql_real_escape_string($ticker['sedol'])."','".mysql_real_escape_string($ticker['cusip'])."','".mysql_real_escape_string($ticker['countryname'])."','".$indxx_id."','1')";
}

$Tickerquery="insert into tbl_indxx_ticker (name,isin,ticker,weight,curr,divcurr,sedol,cusip,countryname,indxx_id,status) values ".implode(",",$ticker_inser_array).";";
mysql_query($Tickerquery);
echo "Tickers Inserted for indxx-".$indxx['code']."<br>";
}

if(!empty($indxx['shares']))
{
	$share_insert_array=array();
foreach($indxx['shares'] as $share)
{
	$share_insert_array[]="('".$indxx_id."','".$share['isin']."','".$share['date']."','".$share['share']."')";

}

$shareQuery="insert into tbl_share (indxx_id,isin,date,share) values ".implode(",",$share_insert_array).";";
mysql_query($shareQuery);
echo "Shares Inserted for indxx-".$indxx['code']."<br>";
}


if(!empty($indxx['weights']))
{
	$weights_insert_array=array();
foreach($indxx['weights'] as $weights)
{
	$weights_insert_array[]="('".$indxx_id."','".$indxx['code']."','".$weights['isin']."','".$weights['date']."','".$weights['share']."','".$weights['price']."','".$weights['weight']."')";

}

$weightsQuery="insert into tbl_weights (indxx_id,code,isin,date,share,price,weight) values ".implode(",",$weights_insert_array).";";
mysql_query($weightsQuery);
echo "Closing Weights Inserted for indxx-".$indxx['code']."<br>";
}
if(!empty($indxx['weights_open']))
{
	$weights_open_insert_array=array();
foreach($indxx['weights_open'] as $weights)
{
	$weights_insert_array[]="('".$indxx_id."','".$indxx['code']."','".$weights['isin']."','".$weights['date']."','".$weights['share']."','".$weights['price']."','".$weights['weight']."')";

}

$weightsQuery="insert into tbl_weights_open (indxx_id,code,isin,date,share,price,weight) values ".implode(",",$weights_insert_array).";";
mysql_query($weightsQuery);
echo "Opening Weights Inserted for indxx-".$indxx['code']."<br>";
}

if(!empty($indxx['final_price']))
{
	$final_price_array=array();
foreach($indxx['final_price'] as $final_price)
{
	$final_price_array[]="('".$indxx_id."','".$final_price['isin']."','".$final_price['date']."','".$final_price['price']."','".$final_price['currencyfactor']."','".$final_price['localprice']."')";

}

$final_priceQuery="insert into tbl_final_price (indxx_id,isin,date,price,currencyfactor,localprice) values ".implode(",",$final_price_array).";";
mysql_query($final_priceQuery);
echo "Final Price Inserted for indxx-".$indxx['code']."<br>";
}

if(!empty($indxx['indxx_value']))
{
	$indxx_value_array=array();
foreach($indxx['indxx_value'] as $indxx_value)
{
	$indxx_value_array[]="('".$indxx_id."','".$indxx_value['code']."','".$indxx_value['market_value']."','".$indxx_value['indxx_value']."','".$indxx_value['date']."','".$indxx_value['olddivisor']."','".$indxx_value['newdivisor']."')";

}

$indxx_valueQuery="insert into tbl_indxx_value (indxx_id,code,market_value,indxx_value,date,olddivisor,newdivisor) values ".implode(",",$indxx_value_array).";";
mysql_query($indxx_valueQuery);
echo "Index value  Inserted for indxx-".$indxx['code']."<br>";
}

if(!empty($indxx['indxx_value_open']))
{
	$indxx_value_open_array=array();
foreach($indxx['indxx_value_open'] as $indxx_value)
{
	$indxx_value_open_array[]="('".$indxx_id."','".$indxx_value['code']."','".$indxx_value['market_value']."','".$indxx_value['indxx_value']."','".$indxx_value['date']."','".$indxx_value['olddivisor']."','".$indxx_value['newdivisor']."')";

}

$indxx_value_openQuery="insert into tbl_indxx_value_open (indxx_id,code,market_value,indxx_value,date,olddivisor,newdivisor) values ".implode(",",$indxx_value_open_array).";";
mysql_query($indxx_value_openQuery);
echo "index value open Inserted for indxx-".$indxx['code']."<br>";
}

if(!empty($indxx['users']))
{
	$indxx_users_array=array();
foreach($indxx['users'] as $user)
{
	$indxx_users_array[]="('".$indxx_id."','".$user['user_id']."')";

}

$indxx_users_Query="insert into tbl_assign_index (indxx_id,user_id) values ".implode(",",$indxx_users_array).";";
mysql_query($indxx_users_Query);
echo "index Users Inserted for indxx-".$indxx['code']."<br>";
}


}


}

}


function checkandupdateindex_temp($indxxArray,$date){

if(!empty($indxxArray))
{
foreach($indxxArray as $indxx)
{
//print_r($indxx);
$indxx_id=0;

$index_res=mysql_query("Select id from tbl_indxx_temp where code='".$indxx['code']."'");
if(mysql_num_rows($index_res)>0)
{$row=mysql_fetch_assoc($index_res);
$indxx_id=$row['id'];
mysql_query("delete from tbl_indxx_ticker_temp where indxx_id='".$indxx_id."'");
mysql_query("delete from tbl_share_temp where indxx_id='".$indxx_id."'");
mysql_query("delete from tbl_final_price_temp where indxx_id='".$indxx_id."' and date >='".$date."'");
mysql_query("delete from tbl_indxx_value_open_temp where indxx_id='".$indxx_id."' and date >='".$date."'");
mysql_query("delete from tbl_indxx_value_temp where indxx_id='".$indxx_id."' and date >='".$date."'");
mysql_query("delete from tbl_weights where indxx_id='".$indxx_id."' and date >='".$date."'");
mysql_query("delete from tbl_weights_open where indxx_id='".$indxx_id."' and date >='".$date."'");
echo "Data Removed for indxx-".$indxx['code']."<br>";
mysql_query("update  tbl_indxx_temp set name='".$indxx['name']."',code='".$indxx['code']."',investmentammount='".$indxx['investmentammount']."',	divpvalue='".$indxx['divpvalue']."',indexvalue='".$indxx['indexvalue']."',	divisor='".$indxx['divisor']."',type='".$indxx['type']."',cash_adjust='".$indxx['cash_adjust']."',curr='".$indxx['curr']."',status='".$indxx['status']."',dateAdded='".$indxx['dateAdded']."',lastupdated='".$indxx['lastupdated']."',dateStart='".$indxx['dateStart']."',usersignoff='".$indxx['usersignoff']."',dbusersignoff='".$indxx['dbusersignoff']."',submitted='".$indxx['submitted']."',	finalsignoff='".$indxx['finalsignoff']."',runindex='".$indxx['runindex']."',addtype='".$indxx['addtype']."',zone='".$indxx['zone']."',calcdate='".$indxx['calcdate']."',rebalance='".$indxx['rebalance']."',client_id='".$indxx['client_id']."',	display_currency='".$indxx['display_currency']."',ireturn='".$indxx['ireturn']."',	ica='".$indxx['ica']."',	recalc='".$indxx['recalc']."',div_type='".$indxx['div_type']."',currency_hedged='".$indxx['currency_hedged']."',priority='".$indxx['priority']."' where id='".$indxx_id."'");

}
else{
mysql_query("insert into tbl_indxx_temp set name='".$indxx['name']."',code='".$indxx['code']."',investmentammount='".$indxx['investmentammount']."',	divpvalue='".$indxx['divpvalue']."',indexvalue='".$indxx['indexvalue']."',	divisor='".$indxx['divisor']."',type='".$indxx['type']."',cash_adjust='".$indxx['cash_adjust']."',curr='".$indxx['curr']."',status='".$indxx['status']."',dateAdded='".$indxx['dateAdded']."',lastupdated='".$indxx['lastupdated']."',dateStart='".$indxx['dateStart']."',usersignoff='".$indxx['usersignoff']."',dbusersignoff='".$indxx['dbusersignoff']."',submitted='".$indxx['submitted']."',	finalsignoff='".$indxx['finalsignoff']."',runindex='".$indxx['runindex']."',addtype='".$indxx['addtype']."',zone='".$indxx['zone']."',calcdate='".$indxx['calcdate']."',rebalance='".$indxx['rebalance']."',client_id='".$indxx['client_id']."',	display_currency='".$indxx['display_currency']."',ireturn='".$indxx['ireturn']."',	ica='".$indxx['ica']."',	recalc='".$indxx['recalc']."',div_type='".$indxx['div_type']."',currency_hedged='".$indxx['currency_hedged']."',priority='".$indxx['priority']."'");
$indxx_id=mysql_insert_id();
echo "new Index Id generated for indxx-".$indxx['code']."<br>";
}

if(!empty($indxx['tickers']))
{
	$ticker_inser_array=array();
foreach($indxx['tickers'] as $ticker)
{
$ticker_inser_array[]="('".mysql_real_escape_string($ticker['name'])."','".mysql_real_escape_string($ticker['isin'])."','".mysql_real_escape_string($ticker['ticker'])."','".mysql_real_escape_string($ticker['weight'])."','".mysql_real_escape_string($ticker['curr'])."','".mysql_real_escape_string($ticker['divcurr'])."','".mysql_real_escape_string($ticker['sedol'])."','".mysql_real_escape_string($ticker['cusip'])."','".mysql_real_escape_string($ticker['countryname'])."','".$indxx_id."','1')";
}

$Tickerquery="insert into tbl_indxx_ticker_temp (name,isin,ticker,weight,curr,divcurr,sedol,cusip,countryname,indxx_id,status) values ".implode(",",$ticker_inser_array).";";
mysql_query($Tickerquery);
echo "Tickers Inserted for temp indxx-".$indxx['code']."<br>";
}

if(!empty($indxx['shares']))
{
	$share_insert_array=array();
foreach($indxx['shares'] as $share)
{
	$share_insert_array[]="('".$indxx_id."','".$share['isin']."','".$share['date']."','".$share['share']."')";

}

$shareQuery="insert into tbl_share_temp (indxx_id,isin,date,share) values ".implode(",",$share_insert_array).";";
mysql_query($shareQuery);
echo "Share Inserted for temp indxx-".$indxx['code']."<br>";
}




if(!empty($indxx['weights']))
{
	$weights_insert_array=array();
foreach($indxx['weights'] as $weights)
{
	$weights_insert_array[]="('".$indxx_id."','".$indxx['code']."','".$weights['isin']."','".$weights['date']."','".$weights['share']."','".$weights['price']."','".$weights['weight']."')";

}

$weightsQuery="insert into tbl_weights_temp (indxx_id,code,isin,date,share,price,weight) values ".implode(",",$weights_insert_array).";";
mysql_query($weightsQuery);
echo "Closing Weights Inserted for indxx-".$indxx['code']."<br>";
}
if(!empty($indxx['weights_open']))
{
	$weights_open_insert_array=array();
foreach($indxx['weights_open'] as $weights)
{
	$weights_insert_array[]="('".$indxx_id."','".$indxx['code']."','".$weights['isin']."','".$weights['date']."','".$weights['share']."','".$weights['price']."','".$weights['weight']."')";

}

$weightsQuery="insert into tbl_weights_open_temp (indxx_id,code,isin,date,share,price,weight) values ".implode(",",$weights_insert_array).";";
mysql_query($weightsQuery);
echo "Opening Weights Inserted for indxx-".$indxx['code']."<br>";
}

if(!empty($indxx['final_price']))
{
	$final_price_array=array();
foreach($indxx['final_price'] as $final_price)
{
	$final_price_array[]="('".$indxx_id."','".$final_price['isin']."','".$final_price['date']."','".$final_price['price']."','".$final_price['currencyfactor']."','".$final_price['localprice']."')";

}

$final_priceQuery="insert into tbl_final_price_temp (indxx_id,isin,date,price,currencyfactor,localprice) values ".implode(",",$final_price_array).";";
mysql_query($final_priceQuery);
echo "Final Price Inserted for temp indxx-".$indxx['code']."<br>";
}

if(!empty($indxx['indxx_value']))
{
	$indxx_value_array=array();
foreach($indxx['indxx_value'] as $indxx_value)
{
	$indxx_value_array[]="('".$indxx_id."','".$indxx_value['code']."','".$indxx_value['market_value']."','".$indxx_value['indxx_value']."','".$indxx_value['date']."','".$indxx_value['olddivisor']."','".$indxx_value['newdivisor']."')";

}

$indxx_valueQuery="insert into tbl_indxx_value_temp (indxx_id,code,market_value,indxx_value,date,olddivisor,newdivisor) values ".implode(",",$indxx_value_array).";";
mysql_query($indxx_valueQuery);
echo "index value  Inserted for temp indxx-".$indxx['code']."<br>";
}

if(!empty($indxx['indxx_value_open']))
{
	$indxx_value_open_array=array();
foreach($indxx['indxx_value_open'] as $indxx_value)

{
	$indxx_value_open_array[]="('".$indxx_id."','".$indxx_value['code']."','".$indxx_value['market_value']."','".$indxx_value['indxx_value']."','".$indxx_value['date']."','".$indxx_value['olddivisor']."','".$indxx_value['newdivisor']."')";

}

$indxx_value_openQuery="insert into tbl_indxx_value_open_temp (indxx_id,code,market_value,indxx_value,date,olddivisor,newdivisor) values ".implode(",",$indxx_value_open_array).";";
mysql_query($indxx_value_openQuery);
echo "index value open Inserted for temp indxx-".$indxx['code']."<br>";
}
if(!empty($indxx['users']))
{
	$indxx_users_array=array();
foreach($indxx['users'] as $user)
{
	$indxx_users_array[]="('".$indxx_id."','".$user['user_id']."')";

}

$indxx_users_Query="insert into tbl_assign_index_temp (indxx_id,user_id) values ".implode(",",$indxx_users_array).";";
mysql_query($indxx_users_Query);
echo "index Users Inserted for indxx-".$indxx['code']."<br>";
}

}


}

}

?>