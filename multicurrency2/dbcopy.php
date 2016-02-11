<pre><?php
//error_reporting(E_ALL);
ini_set('max_execution_time', 60 * 60);
ini_set("memory_limit", "1024M");
  $mtime = microtime();
   $mtime = explode(" ",$mtime);
   $mtime = $mtime[1] + $mtime[0];
   $starttime = $mtime; 

//exec("database_db_copy.bat");
include("dbcopy_function.php");


$query=mysql_query("select * from tbl_backup");
if(mysql_num_rows($query)>0)
{
$row=mysql_fetch_assoc($query);
}
//print_r($row);
$from_date=$row['date'];

$statusfromCopy=array();
$query2=mysql_query("select name,status,date from tbl_system_task_complete order by id desc limit 0,1");
if(mysql_num_rows($query2)>0)
{
$statusfromCopy=mysql_fetch_assoc($query2);
}
//print_r($row);


//print_r(getLive_index($from_date));
$liveIndex=getLive_index($from_date);
$tempIndex=getupcomming_index($from_date);
//print_r($tempIndex);
//exit;
mysql_close();
include("dblive_function.php");

$statusfromlive=array();
$query3=mysql_query("select name,status,date from tbl_system_task_complete order by id desc limit 0,1");
if(mysql_num_rows($query3)>0)
{
$statusfromlive=mysql_fetch_assoc($query3);
}
$diff_array=array_diff_assoc($statusfromCopy,$statusfromlive);
if(empty($diff_array))
{checkandupdateindex_live($liveIndex,$from_date);
checkandupdateindex_temp($tempIndex,$from_date);
}else{
echo "Db Copy Operation Failed : System Task and user task Mismatch.";
}
   $mtime = microtime();
   $mtime = explode(" ",$mtime);
   $mtime = $mtime[1] + $mtime[0];
   $endtime = $mtime;
   $totaltime = ($endtime - $starttime);
   echo "This page was created in ".$totaltime." seconds"; 
?>