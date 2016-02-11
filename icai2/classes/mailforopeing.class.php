<?php

class Mailforopeing extends Application{

	function __construct()
	{
		parent::__construct();
		//$this->checkUserSession();
	}
	
	function index(){
		//echo "sanjay";
	   $flag=0;

	   $data='';
		$indxxid=mysql_query("select id,code from tbl_indxx");
		while($indxxidata=mysql_fetch_array($indxxid)){
		
			$indxxopen=mysql_query("select indxx_value_open from tbl_indxx_value where indxx_id=$indxxidata[0] and date='".date('Y-m-d')."'");
			//echo "select indxx_value from tbl_indxx_value_open where indxx_id=$indxxidata[0] and date='".date('Y-m-d')."'";
			//$indxxclose=mysql_query("select indxx_value from tbl_indxx_value where indxx_id=$indxxidata[0] and date=date('Y-m-d')");
			$indxxopendata=mysql_fetch_array($indxxopen);
			
			//$indxxclosedata=mysql_fetch_array($indxxclose);
			if($indxxopendata[0]==""){	
			$flag=1;
	        $data .=$indxxidata[1];
			$data .="<br>";
		    
		    }
			
			//if(	$indxxclosedata=="")
			//mail("ssnegi@indxx.com","closing value error","closing value not found for indxx=$indxxidata[0]");
		}
		
		
		if($flag==1)
		{
			    $headers  .= 'MIME-Version: 1.0' . "\r\n";
               $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

			mail("it@indxx.com,ical@indxx.com","opening value error","opening value not found for indxx=".$data, $headers);
		}
		$this->chkopeningcomplet();
		
	}
	
	function chkopeningcomplet(){
		
				$indxxclose=mysql_query("select status from tbl_system_task_complete where name='Opening' and date='".date('Y-m-d')."'");
				if(mysql_fetch_array($indxxclose)!='1'){
				 $headers  .= 'MIME-Version: 1.0' . "\r\n";
               $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

			  mail("it@indxx.com,ical@indxx.com","opening task error","opening task not completed for date:".date('Y-m-d'), $headers);
			 	
				}
	
	}
}