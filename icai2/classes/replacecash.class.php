<?php

class Replacecash extends Application{

	function __construct()
	{
		
		parent::__construct();
	
	}
	
	function index(){


	
				if($_GET['log_file'])
define("log_file",get_logs_folder().$_GET['log_file']);
if($_GET['date'])
define("date",$_GET['date']);
else
define("date",date("Y-m-d"));
log_info("In Replace Cash ");
		

	 $datevalue=date;
	 $indexdata=$this->db->getResult("select tbl_cash_index_temp.* from tbl_cash_index_temp where 1=1 and status='1' and db_approve='1'  and dateStart='".date."' ",true);
	//$backupIndex= 
	 	
	 if(!empty($indexdata))
	 {
		
		
	foreach($indexdata as $index)
	{
		
		  $indexvalues=$this->db->getResult("select tbl_cash_indxx_value_temp.* from tbl_cash_indxx_value_temp where indxx_id='".$index['id']."' order by date desc ",false,1);
		//
	
	 $oldindexdata=$this->db->getResult("select tbl_cash_index.* from tbl_cash_index where code='".$index['code']."' ",false,1);
	
	if(!empty($oldindexdata))
	{
		 $insertShareQuery="update  tbl_cash_index set ticker='".$index['ticker']."',isin='".$index['isin']."' where code='".$index['code']."' ";
	// $this->pr(  $oldindexdata,true);
	$this->db->query($insertShareQuery);
	}else{
		$this->db->query("insert into tbl_cash_index set name='".mysql_real_escape_string($index['name'])."',code='".mysql_real_escape_string($index['code'])."',ticker='".mysql_real_escape_string($index['ticker'])."',isin='".mysql_real_escape_string($index['isin'])."',zone='".mysql_real_escape_string($index['zone'])."',client_id='".mysql_real_escape_string($index['client_id'])."',base_value='".mysql_real_escape_string($index['base_value'])."' ,dateStart='".mysql_real_escape_string($index['dateStart'])."',status='1',db_approve='1'");
		
	$insert_id=mysql_insert_id();	
		
		$this->db->query("INSERT into tbl_cash_indxx_value set  indxx_id='".$insert_id."',code='".mysql_real_escape_string($index['code'])."',date='". $indexvalues['date']."',indxx_value='".mysql_real_escape_string($indexvalues['indxx_value'])."'");
		

	
	$this->db->query($insertShareQuery);
	}
	
	
//	echo "delete from tbl_cash_index_temp where indxx_id='".$index['id']."'";
	
		$this->db->query("delete from tbl_cash_index_temp where id='".$index['id']."'");
	
		$this->db->query("delete from tbl_cash_indxx_value_temp where code='".$index['code']."'");
	
	
	}
	 }
	 	$this->saveProcess(1);
		$this->Redirect2("index.php?module=calcftpca&log_file=".basename(log_file)."&date=".date,"","");	
	//$this->Redirect("index.php?module=calcdelisttemp","","");
	 
	 
	}
}