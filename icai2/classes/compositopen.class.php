<?php

class Compositopen extends Application{

	function __construct()
	{
		parent::__construct();
	
	}
	
	function index()
	{
		
		//echo $this->_date;

			if($_GET['date'])
			define("date", $_GET['date']);
			else
			define("date", date("Y-m-d"));

		if($_GET['log_file'])
			define("log_file", $_GET['log_file']);
				$this->log_info(log_file, "In Composite Opening generattion ");
		
	$clientData=$this->db->getResult("select id,ftpusername from tbl_ca_client where status='1'");
	//$this->pr($clientData,true);
	
	///$date=date('Y-m-d',strtotime($this->_date)-86400);
	$date=date;
	
	//$date="2014-03-28";
	if(!empty($clientData))
	{
		foreach($clientData as $client)
		{
				$file="../files/ca-output/".$client['ftpusername']."/compositopening-".$date.".txt";
				
				$client_folder = "../files/ca-output/".$client['ftpusername']."/";
			if (!file_exists($client_folder))
			mkdir($client_folder, 0777, true);



$this->log_info(log_file, "Preparing composit file for ". $client['ftpusername']);
			
				$entry1="Date".",".$date.",\r\n";
				$entry1.="Name,Code,Market Value,Index value,\r\n";
				
				$indexes=$this->db->getResult("select id,name,code from tbl_indxx where client_id='".$client['id']."'",true);
				if(!empty($indexes))
				{
				
				foreach($indexes as $index)
				{
					
				$data=	$this->db->getResult("select market_value,indxx_value from tbl_indxx_value_open where indxx_id='".$index['id']."' and date='".$date."'");
				$entry1.=$index['name'].','.$index['code'].','.$data['market_value'].','.$data['indxx_value'].",\r\n";
				
				
				//$this->pr($data);
				}	
								
				}
				
				$open=fopen($file,"w+");
					if($open){   
 if(   fwrite($open,$entry1))
{
	echo "file Written Successfully ";
	$this->log_info(log_file, "composit file written for ". $client['ftpusername']);
	
	}
}	
				//$this->pr($indexes);
		
		}
	}
	$this->saveProcess(2);
	$this->Redirect2("index.php?module=notifyforca&date=" .$datevalue2. "&log_file=" . log_file, "", "");	
	}
	
	
}
?>