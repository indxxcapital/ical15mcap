<?php 

class Calccapubnew extends Application{
		
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
log_info("In corporate action file publish for Live  ");
		
		//$this->pr($_SESSION);
		
		$indxx=	$this->db->getResult("select tbl_indxx.* from tbl_indxx  where status='1' and usersignoff='1' and dbusersignoff='1' and submitted='1' ",	true);
	//$this->pr($indxx);
	
	$clients=array();
	
		$array=array();
		
		if(!empty($indxx))
		{
		foreach($indxx as $ind)
		{
			
			$client=$this->db->getResult("select tbl_ca_client.ftpusername from tbl_ca_client where id='".$ind['client_id']."'",false,1);	
		//	
		//$final_array[$row['id']]['client']=$client['ftpusername'];
			
			
			
	//	$this->pr($ind);
		$indxxticker=	$this->db->getResult("select distinct(ticker) as indxxticker from tbl_indxx_ticker where indxx_id ='".$ind['id']."'",	true);
		
$entry='';
$entry1='';
$entry.='Index Name'.";";
$entry.=$ind['name'].";";		
$entry.="\n";		
$entry.='Security Ticker'.";";		
$entry.='Company Ticker'.";";	
$entry.='ISIN'.";";
$entry.='Action'.";";
$entry.='Ex Date'.";";		
$entry.='Amount'.";";		
$entry.='Currency;';		
$entry.='Further Details;';		
$entry.='Factor;';		
$entry.="\n";
$entry1.='Index Name'.";";		
$entry1.='Security Ticker'.";";		
$entry1.='Company Ticker'.";";	
$entry1.='ISIN'.";";
$entry1.='Action'.";";
$entry1.='Ex Date'.";";		
$entry1.='Amount'.";";		
$entry1.='Currency;';		
$entry1.='Further Details;';		
$entry1.='Factor;';		
$entry1.="\n";		
	$clients[$client['ftpusername']]['heading']=$entry1;
		//$clients[$client['ftpusername']].=$entry1;	
		//$clients[$client['ftpusername']]['value'].=$ind['name'].";";
		if(!empty($indxxticker))
		{
		foreach($indxxticker as $ticker)
		{
			$castr=$this->getCaStr3($ticker['indxxticker'],date);
			
			$entry.=$castr;
			$clients[$client['ftpusername']]['value'].=$this->getCaStr3($ticker['indxxticker'],date,$ind['name']);
	
		
		
		}
		}
		
		
		if (!file_exists("../files/ca-output/".$client['ftpusername']))
			mkdir("../files/ca-output/".$client['ftpusername'], 0777, true);
		
		if($client['ftpusername'])
		$file="../files/ca-output/".$client['ftpusername']."/ca-".$ind['code']."-".date.".txt";
		else
		$file="../files/ca-output/ca-".$ind['code']."-".date.".txt";
		
		
		
		
		
	//	$clients[$client['ftpusername']].=$entry1;
		
		
		
		
		
		
		$open=fopen($file,"w+");
	if($open){   
		if(   fwrite($open,$entry))
		{
			echo "file Written for ".$ind['code']."<br>";
			log_info("corporate action file published for index :".$ind['code']);
		}
	}
		
		
			//	$this->pr($ca_array);
		}
		}
		
		
		
		//$this->pr($clients,true);
		if(!empty($clients))
		{
		foreach($clients as $clientname=> $caclients)
		{
			
			if (!file_exists("../files/ca-output/".$clientname))
			mkdir("../files/ca-output/".$clientname, 0777, true);
			$file2="../files/ca-output/".$clientname."/composit-ca-".date.".txt";
			
		$open2=fopen($file2,"w+");
	if($open2){   
		if(   fwrite($open2,$caclients['heading'].$caclients['value']))
		{
			echo "file Written for Composit Index<br>";
		}
	}
		}
		}
		
		
		$this->saveProcess();
	//	$this->Redirect("index.php?module=checkcavalue&log_file=".basename(log_file)."&date=".date,"","");	
		}
		
		
}