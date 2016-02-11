<pre>
<?php
include("core/function.php");

$query="Select name, id from tbl_indxx where 1=1";
$res=mysql_query($query);
$final_array=array();
while($row=mysql_fetch_assoc($res))
{
	$tickerCount=mysql_fetch_assoc(mysql_query("select count(*) as totalTickers from tbl_indxx_ticker where indxx_id='".$row['id']."'"));
	$final_array[$row['name']]['ticker']=$tickerCount['totalTickers'];
	$tickerCount2=mysql_fetch_assoc(mysql_query("select count(*) as totalshares from tbl_share where indxx_id='".$row['id']."'"));
	$final_array[$row['name']]['share']=$tickerCount2['totalshares'];
}

print_r($final_array);
?>