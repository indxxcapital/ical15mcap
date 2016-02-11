<?php

class Uploadmarketcap extends Application{

	function __construct()
	{
		parent::__construct();
		$this->checkUserSession();
			$this->addJs('assets/bootstrap/bootstrap.min.js');
			$this->addJs('assets/nicescroll/jquery.nicescroll.min.js');
			$this->addJs('assets/flot/jquery.flot.js');
			$this->addJs('assets/flot/jquery.flot.resize.js');
			$this->addJs('assets/flot/jquery.flot.pie.js');
			$this->addJs('assets/flot/jquery.flot.stack.js');
			$this->addJs('assets/flot/jquery.flot.crosshair.js');
			$this->addJs('assets/flot/jquery.flot.tooltip.min.js');
			$this->addJs('assets/sparkline/jquery.sparkline.min.js');
			$this->addJs('js/flaty.js');
		
	}
	
	function index()
	{
	
	//$this->pr($_SESSION,true);
		
		$this->_baseTemplate="inner-template";
		$this->_bodyTemplate="uploadmarketcap/index";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		$this->smarty->assign('pagetitle','Rebalance');
		$this->smarty->assign('bredcrumssubtitle','Rebalance');
		$this->addfield();
		$myids='';
		$_SESSION["new_added_temp_index"]=array();

		if(!empty($_SESSION['Index']))
		$myids=implode($_SESSION['Index']);
		
		if($myids)
		$indxxdata=$this->db->getResult("select name, code,id from tbl_indxx where 1=1 and ".$myids,true);
		else
		$indxxdata=$this->db->getResult("select name, code,id from tbl_indxx where 1=1",true);
		
		//$this->pr($indxxdata,true);
		$this->smarty->assign("indexdata",$indxxdata);
		$url_text="&submit=submit";
		$message="";
		//$this->pr($_POST,true);
		//exit;
		 if(!empty($_POST)){
		if(!empty($_POST['checkboxid']))
		{	
		
		foreach($_POST['checkboxid'] as $id)
		{
		$url_text.="&checkboxid[]=".$id;
		}
		
				
	$ids=implode(',',$_POST['checkboxid']);	
		//$this->pr($_POST,true);
		$newindxx=$this->db->getResult("select * from tbl_indxx where id in (".$ids.")",true);
		
		//$this->pr($newindxx,true);
		$i=0;
		
		if(!empty($newindxx))
		{
		foreach($newindxx as $oldindxx)
		{$i++;
		
		$oldTempData=$this->db->getResult("select * from tbl_indxx_temp where code='".$oldindxx['code']."' and dateStart='".date("Y-m-d",strtotime($_POST['startDate']))."'");
		if(!empty($oldTempData))
		{
		$this->db->query("delete from tbl_indxx_temp where id='".$oldTempData['id']."'");
		
		$this->db->query("delete from tbl_final_price_temp where indxx_id='".$oldTempData['id']."'");
		$this->db->query("delete from tbl_indxx_ticker_temp where indxx_id='".$oldTempData['id']."'");
		$this->db->query("delete from tbl_assign_index_temp where indxx_id='".$oldTempData['id']."'");
		
		$message.="Duplicate  Request for ".$oldTempData['code'].", Old Data Removed.<br>";
		}
			
		$indxx_id=0;
		$this->db->query("insert into tbl_indxx_temp(name,code,type,cash_adjust,curr,status,usersignoff,dbusersignoff,submitted,addtype,client_id,display_currency,ireturn,ica,recalc,div_type,currency_hedged,dateStart,calcdate) values ('".$oldindxx['name']."','".$oldindxx['code']."','".$oldindxx['type']."','".$oldindxx['cash_adjust']."','".$oldindxx['curr']."','".$oldindxx['status']."','".$oldindxx['usersignoff']."','".$oldindxx['dbusersignoff']."','".$oldindxx['submitted']."','".$oldindxx['addtype']."','".$oldindxx['client_id']."','".$oldindxx['display_currency']."','".$oldindxx['ireturn']."','".$oldindxx['ica']."','1','".$oldindxx['div_type']."','".$oldindxx['currency_hedged']."','".date("Y-m-d",strtotime($_POST['startDate']))."','".date("Y-m-d",strtotime($_POST['startDate_'.$oldindxx['id']]))."')");
		
		
		$indxx_id= mysql_insert_id();
		$_SESSION["new_added_temp_index"][]=$indxx_id;
		$this->db->query("insert into tbl_assign_index_temp (user_id, indxx_id,status) values('".$_SESSION['User']['id']."','".$indxx_id."','1');");
		
//	echo "insert into tbl_indxx_ticker_temp (name,ticker,isin,curr,divcurr,sedol,cusip,countryname,status,indxx_id)  select name,ticker,isin,curr,divcurr,sedol,cusip,countryname,status,'".$indxx_id."' from tbl_indxx_ticker where indxx_id='".$oldindxx['id']."' ";
		$this->db->query("insert into tbl_indxx_ticker_temp (name,ticker,isin,curr,divcurr,sedol,cusip,countryname,status,indxx_id)  select name,ticker,isin,curr,divcurr,sedol,cusip,countryname,status,'".$indxx_id."' from tbl_indxx_ticker where indxx_id='".$oldindxx['id']."' ");
		//$this->db->query("insert into tbl_final_price_temp(indxx_id,isin,date,price,currencyfactor,localprice) select '".$indxx_id."',isin,date,price,currencyfactor,localprice from tbl_final_price where indxx_id='".$oldindxx['id']."' ");
		
		}
		}		
		
		//exit;
		//echo "index.php?module=rebalancing&event=download".$url_text;
		//exit;
		$this->Redirect("index.php?module=rebalancing&event=download",$i." index with security and converted prices Added successfully.<br>  Please download  securities and update share/ weights to runindex.<br>".$message,"success");
		
		}
			 }
				
		$this->show();
				
	}
	
	function uploadfield2()
	{
		 $this->validData[]=array("feild_label" =>"Input marketcap sheet",
		 							"feild_code" =>"inputfile",
								 "feild_type" =>"file",
								 "is_required" =>"1",
								
								 );
		
	$this->getValidFeilds();
	}
	
	private function addfield($edit=false)
	{	
	  
								   $this->validData[]=array("feild_label" =>"Go Live Date",
	   								"feild_code" =>"startDate",
								 "feild_type" =>"date",
								 "is_required" =>"1",
								 "feildOptions"=>array("onblur"=>"copy_data(this);"),
							 );
	
								 
	
	$this->getValidFeilds();
	}
	
	function download()
	{
	$this->_baseTemplate="inner-template";
		$this->_bodyTemplate="uploadmarketcap/download";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		$this->smarty->assign('pagetitle','Download Tickers');
		$this->smarty->assign('bredcrumssubtitle','Download Tickers');
		//$this->addfield();
		$indxxdata=$this->db->getResult("select name, code,dateStart,id from tbl_indxx_temp where 1=1",true);
		//echo"select name, code,id from tbl_indxx_temp where 1=1";
		//$this->pr($indxxdata,true);
		
		foreach($indxxdata as $key=> $indxx)
		{
		if(in_array($indxx['id'],$_SESSION["new_added_temp_index"]))
		$indxxdata[$key]['selected']=1;
		}
		//$this->pr($indxxdata,true);
		$this->smarty->assign("indexdata",$indxxdata);
		
		//$this->pr($_SESSION["new_added_temp_index"],true);
		
		//$this->smarty->assign("selected",$indxxdata);
		if(!empty($_GET['submit']))
		{
		//$this->pr($_GET,true);
		if(!empty($_GET['checkboxid']))
		{
		$ids=implode(',',$_GET['checkboxid']);
		
		$data =$this->db->getResult("select tbl_indxx_temp.code,ticker,isin from tbl_indxx_ticker_temp left join tbl_indxx_temp on tbl_indxx_temp.id=tbl_indxx_ticker_temp.indxx_id where tbl_indxx_temp.id in (".$ids."); ");
		//$this->pr($data,true);
		$output = fopen("php://output",'w') or die("Can't open php://output");
header("Content-Type:application/csv"); 
header("Content-Disposition:attachment;filename=updatedticker.csv"); 
fputcsv($output, array('code','ticker','isin','share','weight'));
foreach($data as $product) {
    fputcsv($output, $product);
}
fclose($output) or die("Can't close php://output");
		
	exit;
	}
		
		}
		unset($_SESSION["new_added_temp_index"]);
		$this->show();
	}
	
	function upload()
	{
		$this->_baseTemplate="inner-template";
		$this->_bodyTemplate="uploadmarketcap/upload";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		$this->smarty->assign('pagetitle','Upload Marketcap');
		$this->smarty->assign('bredcrumssubtitle','Upload Marketcap');
		$check=true;
				$errormsg='';
	
			if(isset($_POST['submit']) ){
	$fields=array("1","2","3","4");
	
	$data = csv2::import($fields,$_FILES['inputfile']['tmp_name']);	

			//$this->pr($data,true);
	
					$notinsert=0;
					$insert=0; 
					$skipped=0; 
					$update=0;  
					if(!empty($data))
				   {	
				   
				  // mysql_query("truncate tbl_marketcap");
					foreach($data as $key=> $users)
					{
						
						//$this->pr($users,true);
						
					foreach($users as $keyUser=>$valueUser)
					{	
					if($keyUser!='Ticker' && $keyUser!='ISIN' )	
					{	
							
					if($valueUser!="" && $valueUser!='#N/A' && $valueUser!='#NAME?' && $valueUser!=0)
					{
						
						$data =$this->db->getResult("select * from tbl_marketcap where  date='".date("Y-m-d",strtotime($keyUser))."' and isin='".$users['ISIN']."'",false);
						if(!empty($data))
						{
						$this->db->query("delete from tbl_marketcap where  date='".date("Y-m-d",strtotime($keyUser))."' and isin='".$users['ISIN']."' ");
						}
						if(mysql_query("insert into tbl_marketcap(isin,date,mcap) values('".$users['ISIN']."','".date("Y-m-d",strtotime($keyUser))."','".$valueUser."')"))
						{
							$insert++;
							
					
					
						}
						//$mcapValue=0;
					}else
					{
					}
					
					}
					
					}
					
						
					
				
					}
				
				   }	$this->Redirect("index.php?module=uploadmarketcap&event=upload","Total ".$insert." tickers inserted","success");	
					}

			
			
		
	$this->uploadfield2();
	
	 $this->show();
		
		
		
		}
		function checkMcap($date,$ticker)
	{
		
		$sql=mysql_query("select id from tbl_marketcap where date='".$date."' and ticker='".$ticker."'");
		$indxxid=mysql_fetch_assoc($sql);
		
		if(mysql_num_rows($sql)>0)
		return $indxxid['id'];
		else
		return 0;
	}
	
} // class ends here

?>