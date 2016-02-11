<?php 
include("dbconfig_dbcopy.php");
function getupcomming_index($from_date=''){
$temp_indxx_array=array();
$indxx_temp_res=mysql_query("select * from tbl_indxx_temp");
if($indxx_temp_res && mysql_num_rows($indxx_temp_res)>0)
{
	
	$moreQuery='';
	
	if($from_date)
	{
	$moreQuery.=" and date>='".$from_date."'";
	}
	
	
while($indxx_temp=mysql_fetch_assoc($indxx_temp_res))
{
$temp_indxx_array[$indxx_temp['id']]=$indxx_temp;
	$tickers_temp_res=mysql_query("select * from tbl_indxx_ticker_temp where indxx_id='".$indxx_temp['id']."'");
	if($tickers_temp_res && mysql_num_rows($tickers_temp_res)>0)
	{
		$tickers_temp_array=array();
		while($tickers_temp=mysql_fetch_assoc($tickers_temp_res))
		{
			$tickers_temp_array[]=$tickers_temp;
		}
		$temp_indxx_array[$indxx_temp['id']]['tickers']=$tickers_temp_array;
		unset($tickers_temp_array);
	}
	mysql_free_result($tickers_temp_res);
	$shares_temp_res=mysql_query("select * from tbl_share_temp where indxx_id='".$indxx_temp['id']."'");
	if($shares_temp_res && mysql_num_rows($shares_temp_res)>0)
	{
		$shares_temp_array=array();
		while($shares_temp=mysql_fetch_assoc($shares_temp_res))
		{
			$shares_temp_array[]=$shares_temp;
		}
		$temp_indxx_array[$indxx_temp['id']]['shares']=$shares_temp_array;
		unset($shares_temp_array);
	}
	mysql_free_result($shares_temp_res);
	
	$weights_res=mysql_query("select * from tbl_weights_temp where indxx_id='".$indxx['id']."'".$moreQuery);
	if($weights_res && mysql_num_rows($weights_res)>0)
	{
		$weights_array=array();
		while($weights=mysql_fetch_assoc($weights_res))
		{
			$weights_array[]=$weights;
		}
		$indxx_array[$indxx['id']]['weights']=$weights_array;
		unset($weights_array);
	}
		mysql_free_result($weights_res);
		$weights_open_res=mysql_query("select * from tbl_weights_open_temp where indxx_id='".$indxx['id']."'".$moreQuery);
	if($weights_open_res && mysql_num_rows($weights_open_res)>0)
	{
		$weights_open_array=array();
		while($weights_open=mysql_fetch_assoc($weights_open_res))
		{
			$weights_open_array[]=$weights_open;
		}
		$indxx_array[$indxx['id']]['weights_open']=$weights_open_array;
		unset($weights_open_array);
	}
		mysql_free_result($weights_open_res);
	
	$final_price_temp_res=mysql_query("select * from tbl_final_price_temp where indxx_id='".$indxx_temp['id']."'".$moreQuery);
	if($final_price_temp_res && mysql_num_rows($final_price_temp_res)>0)
	{
		$final_price_temp_array=array();
		while($final_price_temp=mysql_fetch_assoc($final_price_temp_res))
		{
			$final_price_temp_array[]=$final_price_temp;
		}
		$temp_indxx_array[$indxx_temp['id']]['final_price']=$final_price_temp_array;
		unset($final_price_temp_array);
	}
	mysql_free_result($final_price_temp_res);
	$indxx_value_temp_res=mysql_query("select * from tbl_indxx_value_temp where indxx_id='".$indxx_temp['id']."'".$moreQuery);
	if($indxx_value_temp_res && mysql_num_rows($indxx_value_temp_res)>0)
	{
		$indxx_value_temp_array=array();
		while($indxx_value_temp=mysql_fetch_assoc($indxx_value_temp_res))
		{
			$indxx_value_temp_array[]=$indxx_value_temp;
		}
		$temp_indxx_array[$indxx_temp['id']]['indxx_value']=$indxx_value_temp_array;
		unset($indxx_value_temp_array);
	}
	mysql_free_result($indxx_value_temp_res);
    $indxx_value_open_temp_res=mysql_query("select * from tbl_indxx_value_open_temp where indxx_id='".$indxx_temp['id']."'".$moreQuery);
	if($indxx_value_open_temp_res && mysql_num_rows($indxx_value_open_temp_res)>0)
	{
		$indxx_value_open_temp_array=array();
		while($indxx_value_open_temp=mysql_fetch_assoc($indxx_value_open_temp_res))
		{
			$indxx_value_open_temp_array[]=$indxx_value_open_temp;
		}
		$temp_indxx_array[$indxx_temp['id']]['indxx_value_open']=$indxx_value_open_temp_array;
		unset($indxx_value_open_temp_array);
	}
	mysql_free_result($indxx_value_open_temp_res);
  
   $indxx_users_res=mysql_query("select user_id from tbl_assign_index_temp where indxx_id='".$indxx_temp['id']."'");
	if($indxx_users_res && mysql_num_rows($indxx_users_res)>0)
	{
		$indxx_users_array=array();
		while($indxx_users=mysql_fetch_assoc($indxx_users_res))
		{
			$indxx_users_array[]=$indxx_users;
		}
		$temp_indxx_array[$indxx_temp['id']]['users']=$indxx_users_array;
		unset($indxx_value_open_temp_array);
	}
	mysql_free_result($indxx_users_res);

}
mysql_free_result($indxx_temp_res);

}

return $temp_indxx_array;
}
function getLive_index($from_date=''){




$indxx_array=array();
$indxx_res=mysql_query("select * from tbl_indxx");
if($indxx_res && mysql_num_rows($indxx_res)>0)
{
	$moreQuery='';
	
	if($from_date)
	{
	$moreQuery.=" and date>='".$from_date."'";
	}
	
	
while($indxx=mysql_fetch_assoc($indxx_res))
{
	
	
	//echo "select user_id from tbl_assign_index where indxx_id='".$indxx['id']."'".$moreQuery;
	//exit;
$indxx_array[$indxx['id']]=$indxx;
	$tickers_res=mysql_query("select * from tbl_indxx_ticker where indxx_id='".$indxx['id']."'");
	if($tickers_res && mysql_num_rows($tickers_res)>0)
	{
		$tickers_array=array();
		while($tickers=mysql_fetch_assoc($tickers_res))
		{
			$tickers_array[]=$tickers;
		}
		$indxx_array[$indxx['id']]['tickers']=$tickers_array;
		unset($tickers_array);
	}
	mysql_free_result($tickers_res);
	$shares_res=mysql_query("select * from tbl_share where indxx_id='".$indxx['id']."'");
	if($shares_res && mysql_num_rows($shares_res)>0)
	{
		$shares_array=array();
		while($shares=mysql_fetch_assoc($shares_res))
		{
			$shares_array[]=$shares;
		}
		$indxx_array[$indxx['id']]['shares']=$shares_array;
		unset($shares_array);
	}
		mysql_free_result($shares_res);
		
		$weights_res=mysql_query("select * from tbl_weights where indxx_id='".$indxx['id']."'".$moreQuery);
	if($weights_res && mysql_num_rows($weights_res)>0)
	{
		$weights_array=array();
		while($weights=mysql_fetch_assoc($weights_res))
		{
			$weights_array[]=$weights;
		}
		$indxx_array[$indxx['id']]['weights']=$weights_array;
		unset($weights_array);
	}
		mysql_free_result($weights_res);
		$weights_open_res=mysql_query("select * from tbl_weights_open where indxx_id='".$indxx['id']."'".$moreQuery);
	if($weights_open_res && mysql_num_rows($weights_open_res)>0)
	{
		$weights_open_array=array();
		while($weights_open=mysql_fetch_assoc($weights_open_res))
		{
			$weights_open_array[]=$weights_open;
		}
		$indxx_array[$indxx['id']]['weights_open']=$weights_open_array;
		unset($weights_open_array);
	}
		mysql_free_result($weights_open_res);
		
		
	$final_price_res=mysql_query("select * from tbl_final_price where indxx_id='".$indxx['id']."'" .$moreQuery);
	if($final_price_res && mysql_num_rows($final_price_res)>0)
	{
		$final_price_array=array();
		while($final_price=mysql_fetch_assoc($final_price_res))
		{
			$final_price_array[]=$final_price;
		}
		$indxx_array[$indxx['id']]['final_price']=$final_price_array;
		unset($final_price_array);
	}
		mysql_free_result($final_price_res);
	$indxx_value_res=mysql_query("select * from tbl_indxx_value where indxx_id='".$indxx['id']."'".$moreQuery);
	if($indxx_value_res && mysql_num_rows($indxx_value_res)>0)
	{
		$indxx_value_array=array();
		while($indxx_value=mysql_fetch_assoc($indxx_value_res))
		{
			$indxx_value_array[]=$indxx_value;
		}
		$indxx_array[$indxx['id']]['indxx_value']=$indxx_value_array;
		unset($indxx_value_array);
	}
	mysql_free_result($indxx_value_res);
    $indxx_value_open_res=mysql_query("select * from tbl_indxx_value_open where indxx_id='".$indxx['id']."'".$moreQuery);
	if($indxx_value_open_res && mysql_num_rows($indxx_value_open_res)>0)
	{
		$indxx_value_open_array=array();
		while($indxx_value_open=mysql_fetch_assoc($indxx_value_open_res))
		{
			$indxx_value_open_array[]=$indxx_value_open;
		}
		$indxx_array[$indxx['id']]['indxx_value_open']=$indxx_value_open_array;
		unset($indxx_value_open_array);
	}
	
	   $indxx_users_res=mysql_query("select user_id from tbl_assign_index where indxx_id='".$indxx['id']."'");
	if($indxx_users_res && mysql_num_rows($indxx_users_res)>0)
	{
		$indxx_users_array=array();
		while($indxx_users=mysql_fetch_assoc($indxx_users_res))
		{
			$indxx_users_array[]=$indxx_users;
		}
		$indxx_array[$indxx['id']]['users']=$indxx_users_array;
		unset($indxx_users_array);
	}
	mysql_free_result($indxx_users_res);
	
mysql_free_result($indxx_value_open_res);
}

}
mysql_free_result($indxx_res);
return $indxx_array;
}
//mysql_close();
?>