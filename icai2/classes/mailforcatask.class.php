<?php

class Mailforcatask extends Application{

	function __construct()
	{
		parent::__construct();
		//$this->checkUserSession();
	}
	
	function index(){
		
				$indxxclose=mysql_query("select status from tbl_system_task_complete where name='CA' and date='".date('Y-m-d')."'");
				if(mysql_fetch_array($indxxclose)!='1'){
				 $headers  .= 'MIME-Version: 1.0' . "\r\n";
               $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

			  mail("it@indxx.com,ical@indxx.com","CA task error","CA task not completed for date:".date('Y-m-d'), $headers);
			  }
	      }
}