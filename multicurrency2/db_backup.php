<?php 
include("function.php");


if($_GET['log_file'])
			define("log_file", get_logs_folder().$_GET['log_file']);
				log_info(log_file, "db backup file after closing .");
exec("C:/xampp/mysql/bin/mysqldump --opt -hlocalhost -uadmin_icai14marketcap -pReset930$$ admin_icai14marketcap > C:/xampp/htdocs/marketcap/files/db-backup/closing_backup_admin_icai14_".date("Y-m-d",strtotime($_GET['date'])).".sql");


	 
	 log_info(" Closing Process Finished  ");
	 
	 

?>