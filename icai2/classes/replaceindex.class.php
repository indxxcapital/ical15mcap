<?php

class Replaceindex extends Application{

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

log_info("In Replace Index (Go-live) of index ");

	 $datevalue=date;
//exit;

	$indexdata=$this->db->getResult("select id from tbl_indxx_temp where 1=1 and status='1' and usersignoff='1' and dbusersignoff='1' and submitted='1' and finalsignoff='1'  and dateStart='".$datevalue."' ",true);
	$indxxes=array();	
		//$this->pr($indexdata,true);
if(!empty($indexdata))
{
	foreach($indexdata as $indxx_id)
	{
	$indxxes[]=$indxx_id['id'];
	}
	
	}
if(!empty($indxxes))
$this->goliveIndex($indxxes);	
	
			
	$this->saveProcess(1);
	////
	$this->Redirect2("index.php?module=replacecash&log_file=".basename(log_file)."&date=".date,"","");	
	
	//$this->Redirect("index.php?module=calcindxxopening","","");	
	
	
	
	}
	
}?>