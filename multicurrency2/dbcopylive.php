<pre><?php
ini_set('max_execution_time', 60 * 60);
ini_set("memory_limit", "1024M");
mysql_connect("localhost:3306","admin_icai14","Indxxb3930@db");
mysql_select_db("admin_icai14backup");

$indxx_array=array();
$indxx_res=mysql_query("select * from tbl_indxx");
if($indxx_res && mysql_num_rows($indxx_res)>0)
{
while($indxx=mysql_fetch_assoc($indxx_res))
{
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
	$final_price_res=mysql_query("select * from tbl_final_price where indxx_id='".$indxx['id']."'");
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
	$indxx_value_res=mysql_query("select * from tbl_indxx_value where indxx_id='".$indxx['id']."'");
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
    $indxx_value_open_res=mysql_query("select * from tbl_indxx_value_open where indxx_id='".$indxx['id']."'");
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

}

}

print_r($indxx_array);

?>