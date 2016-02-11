<?php include("function.php");
//echo "deepak";
//exit;
error_reporting(2);
set_error_handler("error_handler",2);

//$start_time = get_time();

/* Execution time for the script. Must be defined based on performance and load. */
ini_set('max_execution_time', 60 * 60);
ini_set("memory_limit", "1024M");

/* Prepare logging mechanism */
prepare_logfile();

//date("Y-m-d")
/* Define date for fetching input files and manipulations */
if ($_GET['date'])
	define("file_date", $_GET['date']);
else
{
	define("file_date", date("Y-m-d",strtotime(date("Y-m-d"))-86400));
	//define("file_date", "2015-05-21");
}	
	
/*if ("Fri" == date("D", strtotime(file_date)))
	define("date", date("Y-m-d", strtotime("+3 day", strtotime(file_date))));
else*/
	//define("date", date("Y-m-d", strtotime("+1 day", strtotime(file_date))));
	define("date", date("Y-m-d", strtotime(file_date)));	
save_process("CA",date("Y-m-d"),"0");


define("log_file", get_logs_folder() . "ca_process_logs_" . date('Y-m-d_H-i-s', $_SERVER ['REQUEST_TIME']) . ".txt");
	
//date="2014-04-27";
define("ca_file", get_input_file("CA", file_date));
//echo ca_file;
//exit;
delete_old_ca();
//echo ca_file;
//exit;
read_ca_file();
echo "CA Process done";
//webopen("../icai2/index.php?module=calccapubnew&log_file=".basename(log_file)."&date=".date);
//webopen("../icai2/index.php?module=transactiondata&log_file=".basename(log_file)."&date=".date);
webopen("../icai2/index.php?module=transactionprocess&log_file=".basename(log_file)."&date=".date);
//echo date;

?>