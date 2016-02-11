<pre>
<?php

include("function.php");

/* Enable error capturing in log files and display the same in browser */
error_reporting(2);
//set_error_handler("error_handler", E_ALL);

/* Execution time for the script. Must be defined based on performance and load. */
ini_set('max_execution_time', 60 * 60);
ini_set("memory_limit", "1024M");

if (!$_GET['DBNAME'])
{
echo "invalid file name";
exit;

}
$command ='';
ini_set("display_errors", 0);
	
	$restore_file = "C:/xampp/htdocs/marketcap/files/db-backup/"  .$_GET['DBNAME'];
 	$command = "C:/xampp/mysql/bin/mysql -u" .$db_user. " -p" .$db_password.  " " .$db_name. "  <  " .$restore_file;
	

	



echo $command . "<br>";
//exit;
//exec($command, $output);
//print_r($output);
//shell_exec($command);

   $res=0;
system($command, $res);
if ($res)
{
	echo "Error[code = " .$res. "] while taking DB restore. Exiting process";
	return false;
}
else
{
	echo "Database restored";
	sava_database_file($_GET['DBNAME']);
	return true;
}  


?>