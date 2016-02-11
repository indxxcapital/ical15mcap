<?php

class Calcftpca extends Application{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function index()
	{
	
if($_GET['log_file'])
define("log_file",get_logs_folder().$_GET['log_file']);
if($_GET['date'])
define("date",$_GET['date']);
else
define("date",date("Y-m-d"));

log_info("In Replace Cash Index (Go-live) of index ");
	 $datevalue2=date;
$this->update_process("CA",date("Y-m-d"),"1");	
/*
// set up basic connection
$conn_id = ftp_connect("ftp.processdo.com");

// login with username and password
$login_result = ftp_login($conn_id, "icaitest@processdo.com", 'icaitest@2014');


$file2 = '../files2/ca-output/pga/ca-IPJAS-'. $datevalue2.'.txt';
$remote_file2 = 'ca-IPJAS-'. $datevalue2.'.txt';

// upload a file
if (ftp_put($conn_id, $remote_file2, $file2, FTP_ASCII)) {
 echo "successfully uploaded $file2\n";
} else {
 echo "There was a problem while uploading $file\n";
}
$file3 = '../files2/ca-output/pga/ca-IPJAR-'. $datevalue2.'.txt';
$remote_file3 = 'ca-IPJAR-'. $datevalue2.'.txt';

// upload a file
if (ftp_put($conn_id, $remote_file3, $file3, FTP_ASCII)) {
 echo "successfully uploaded $file3\n";
} else {
 echo "There was a problem while uploading $file\n";
}



// close the connection



$this->saveProcess();
ftp_close($conn_id);
*/
	
		//$this->Redirect("index.php?module=checkivchange","","");	
	}
}
?>