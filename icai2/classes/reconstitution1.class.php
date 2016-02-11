<?php

class Reconstitution1 extends Application{

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
		
		/*$this->_baseTemplate="inner-template";
		$this->_bodyTemplate="reconstitution/index";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		$this->smarty->assign('pagetitle','Reconstitution');
		$this->smarty->assign('bredcrumssubtitle','Reconstitution');
		$this->addfield();
		$myids='';
		$this->show();*/
		
		
	}
	
		function prepare()
	{
		$this->_baseTemplate="inner-template";
		$this->_bodyTemplate="reconstitution/prepare";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		$this->smarty->assign('pagetitle','Reconstitution');
		$this->smarty->assign('bredcrumssubtitle','Prepare Input File');
		$this->uploadfield2();
		if(!empty($_POST))
		{
		$csv_mimetypes = array(
    'text/csv',
    'text/plain',
    'application/csv',
    'text/comma-separated-values',
    'application/excel',
    'application/vnd.ms-excel',
    'application/vnd.msexcel',
    'text/anytext',
    'application/octet-stream',
    'application/txt',
);


if (!in_array($_FILES['inputfile']['type'], $csv_mimetypes)) {
		$check=false;
				$errormsg='Invalid input file, Please upload correct csv file';
			//break;
			$this->Redirect("index.php?module=reconstitution&event=prepare","Error in input :".$errormsg,"error");	
			}
			//$this->pr($_FILES,true);
		
			$fields=array("1");		
				$data = csv::import($fields,$_FILES['inputfile']['tmp_name']);	
//$this->pr($data,true);



		if(!empty($data))
		{$outputArray=array();
		foreach($data as $key=> $security)
		{
			$newData=$this->getallDataofTicker($security['1']);
			 //$this->pr($newData);
			//$outputArray[$key]['index_code']=$security['1'];
			$outputArray[$key]['name']=$newData['name'];
			$outputArray[$key]['isin']=$security['1'];
			$outputArray[$key]['ticker']=$newData['ticker'];
			$outputArray[$key]['curr']=$newData['curr'];
			$outputArray[$key]['divcurr']=$newData['divcurr'];
			$outputArray[$key]['sedol']=$newData['sedol'];
			$outputArray[$key]['cusip']=$newData['cusip'];
			$outputArray[$key]['countryname']=$newData['countryname'];
			$outputArray[$key]['sector']=$newData['sector'];
			$outputArray[$key]['industry']=$newData['industry'];
			$outputArray[$key]['subindustry']=$newData['subindustry'];
			//$outputArray[$key]['shares']="";
			//$outputArray[$key]['weight']="";
			
			
			

		}
		$output = fopen("php://output",'w') or die("Can't open php://output");
header("Content-Type:application/csv"); 
header("Content-Disposition:attachment;filename=".$_FILES['inputfile']['name']); 
fputcsv($output, array('Full Name','ISIN','Ticker','Curncy','Dvd Curncy','SEDOL','CUSIP','Cntry','sector','industry','subindustry'));
foreach($outputArray as $product) {
    fputcsv($output, $product);
}
fclose($output) or die("Can't close php://output");
		
	exit;
		//$this->pr($outputArray);
		//exit;

		}
		
		
	
		}
		
		
		$myids='';
		$this->show();
				
	}
	
	function getallDataofTicker($isin)
	{
	$indxxdata=array();
	$indxxdata=$this->db->getResult("select * from tbl_indxx_ticker where isin='".$isin."'",false,1);
		if(empty($indxxdata))
		$indxxdata=$this->db->getResult("select * from tbl_indxx_ticker_temp where isin='".$isin."'",false,1);
	
	
	return $indxxdata;
	
	}
		
	function addNew()
	{
		$this->_baseTemplate="inner-template";
		$this->_bodyTemplate="reconstitution/addNew";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		$this->smarty->assign('pagetitle','Reconstitution');
		$this->smarty->assign('bredcrumssubtitle','Add New Reconstitution');
		$this->addfield();
		$myids='';
		if(!empty($_SESSION['Index']))
		//$this->pr( $_SESSION['Index']);
		//exit;
		$myids=implode($_SESSION['Index']);
		//echo $myids;
		//exit;
		if($myids)
		$indxxdata=$this->db->getResult("select name, code,id from tbl_indxx where 1=1 and ".$myids,true);
		else
		$indxxdata=$this->db->getResult("select name, code,id from tbl_indxx where 1=1",true);
		$this->smarty->assign("indexdata",$indxxdata);
		//$this->pr($indxxdata['code']);
		//exit;	
		 if(!empty($_POST))
		 {
		$tempselectData=array();
		if(!empty($_POST['checkboxid']))
		{
		//$this->pr($_POST['checkboxid']);
		//exit;
		foreach($_POST['checkboxid'] as $indxx)
		{
		$tempselectData[$indxx]['startDate']=$_POST['startDate'];
		
		$tempselectData[$indxx]['calcdate']=$_POST['startDate_'.$indxx];
	//$tempselectData[]=$array;
		}
		//$this->pr($tempselectData,true);
		
		if($tempselectData){
		$_SESSION['reconstitute']=$tempselectData;
		$this->Redirect("index.php?module=reconstitution&event=uploaddata",count($tempselectData)." index Selected <br> Please upload data file","success");	
		}
		}
		
		
		}
				
		$this->show();
				
		
		}
	
	function uploaddata()
	{
	$this->_baseTemplate="inner-template";
		$this->_bodyTemplate="reconstitution/upload";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		$this->smarty->assign('pagetitle','Reconstitution');
		$this->smarty->assign('bredcrumssubtitle','Upload Input Data');
	$this->uploadfield2();

$message='';
		$check=true;
				$errormsg='';
	
			if(isset($_POST['submit']) ){
			
			$csv_mimetypes = array(
    'text/csv',
    'text/plain',
    'application/csv',
    'text/comma-separated-values',
    'application/excel',
    'application/vnd.ms-excel',
    'application/vnd.msexcel',
    'text/anytext',
    'application/octet-stream',
    'application/txt',
);


if (!in_array($_FILES['inputfile']['type'], $csv_mimetypes)) {
		$check=false;
				$errormsg='Invalid input file, Please upload correct csv file';
			//break;
			$this->Redirect("index.php?module=reconstitution&event=uploaddata","Error in input :".$errormsg,"error");	
			}
			
			
			$fields=array("1",'2','3','4','5','6','7','8','9','10','11');		
			$data = csv::import($fields,$_FILES['inputfile']['tmp_name']);			
			 $ordered = array();
			 if(!empty($data))
			{   $indxx_id_array=array() ;
			
				foreach ($data as $key => $value)
					$ordered[$value['1']][] = $value;
			
				ksort($ordered, SORT_ASC);
				if(!empty($ordered))
				{
				foreach($ordered as $key=>$indxx)
				{
					$indxx_id_array[]=$this->getLiveIndexId($key);
				
				}
			
			
			foreach($indxx_id_array as $input_index)
			{
			if(in_array($input_index,array_keys($_SESSION['reconstitute'])))
			{}
			else
				$this->Redirect("index.php?module=reconstitution&event=addNew","Select index and input sheet index mismatch","error");	
			}
			foreach(array_keys($_SESSION['reconstitute']) as $input_index)
			{
			if(in_array($input_index,$indxx_id_array))
			{//echo "success";
			}else
				$this->Redirect("index.php?module=reconstitution&event=addNew","Select index and input sheet index mismatch","error");	
			}
			
			if(!empty($ordered))
				{
					$sum=0;
					$check=false;
				foreach($ordered as $key=>$indxx)
				{
				
				if(!empty($indxx))
				{
				foreach($indxx as $ticker)
				{if($ticker['11'])
				{
					$sum+=$ticker['11'];
					$check=true;
				}
				}
				}
				if(strval($sum)!=100 && $check){
				$this->Redirect("index.php?module=reconstitution&event=addNew",$key." index Weights are " .strval($sum)." Please Recalculate Weights","error");	
				
				}
				
				}
				}
			//exit;
			
			//;
			foreach($indxx_id_array as $old_indxx_id)
			{
				
			$upcomming_old_index= 	$this->db->getResult("Select id,code from tbl_indxx_temp where code=(select code from tbl_indxx where id='".$old_indxx_id."') and dateStart='".$_SESSION['reconstitute'][$old_indxx_id]['startDate']."'");
			if(!empty($upcomming_old_index))
			{
				//delete this index all data 
					$this->db->query("delete from tbl_indxx_temp where id='".$upcomming_old_index['id']."'");
					
					$this->db->query("delete from tbl_final_price_temp where indxx_id='".$upcomming_old_index['id']."'");
					$this->db->query("delete from tbl_share_temp where indxx_id='".$upcomming_old_index['id']."'");
					$this->db->query("delete from tbl_indxx_ticker_temp where indxx_id='".$upcomming_old_index['id']."'");
					$this->db->query("delete from tbl_assign_index_temp where indxx_id='".$upcomming_old_index['id']."'");
					
					$message.="<br>Duplicate  Request for ".$upcomming_old_index['code'].", Old Data Removed.<br>";
				
			}
			}
			$i=0;
			if(!empty($ordered))
				{
				foreach($ordered as $key=>$indxx)
				{
				$live_index_id=$this->getLiveIndexId($key);
				
				$indxx_id=0;
					$this->db->query("insert into tbl_indxx_temp(name,code,type,cash_adjust,curr,status,usersignoff,submitted,addtype,client_id,display_currency,ireturn,ica,recalc,div_type,currency_hedged,dateStart,calcdate) select name,code,type,cash_adjust,curr,status,usersignoff,submitted,addtype,client_id,display_currency,ireturn,ica,recalc,div_type,currency_hedged,'".$_SESSION['reconstitute'][$live_index_id]['startDate']."','".$_SESSION['reconstitute'][$live_index_id]['calcdate']."' from tbl_indxx where code='".$key."' ");
					$indxx_id=mysql_insert_id();
				$this->db->query("insert into tbl_assign_index_temp (user_id, indxx_id,status) values('".$_SESSION['User']['id']."','".$indxx_id."','1');");
					
				
				$i++;
				if(!empty($indxx))
				{
					$share_array=array();
					$tickerArray=array();
				foreach($indxx as $ticker)
				{
				if($ticker['10'])
				{
				$share_array[]="('".$indxx_id."','".$ticker[3]."','".$ticker[10]."')";
				}
				
				$tickerArray[]="('".$indxx_id."','".$ticker[2]."','".$ticker[3]."','".$ticker[4]."','".$ticker[5]."','".$ticker[6]."','".$ticker[7]."','".$ticker[8]."','".$ticker[9]."','".$ticker[11]."')";
				}
				
				if(!empty($share_array))
				$this->db->query("insert into tbl_share_temp (indxx_id,isin,share) values ".implode(",",$share_array).";");
				if(!empty($tickerArray))
				$this->db->query("insert into tbl_indxx_ticker_temp (indxx_id,ticker,isin,name,curr,divcurr,sedol,cusip,countryname,weight) values ".implode(",",$tickerArray).";");
				
				}
				}
				$this->Redirect("index.php?module=caupcomingindex",$i. " index added Successfully".$message,"success");	
			
			}
			
			
			
			/*
				$comparearray=array_diff(array_keys($_SESSION['reconstitute']),$indxx_id_array);
				
				if(!empty($comparearray))
			{
			echo "input file index code mismatch";
			}else{
			echo "success";
			}	
				*/
				
				
				}
				}
				
				//exit;
				
	
			
	}
	$this->show();
}
	function uploadfield2()
	{
		 $this->validData[]=array("feild_label" =>"Reconstitution input sheet",
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
								 "feildOptions"=>array("onfocusout"=>"copy_data(this);"),
							 );
	
								 
	
	$this->getValidFeilds();
	}
	
	function download()
	{
	$this->_baseTemplate="inner-template";
		$this->_bodyTemplate="reconstitution/download";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		$this->smarty->assign('pagetitle','Reconstitution');
		$this->smarty->assign('bredcrumssubtitle','Download Tickers');
		//$this->addfield();
		$indxxdata=$this->db->getResult("select name, code,dateStart,id from tbl_indxx_temp where 1=1",true);
		//echo"select name, code,id from tbl_indxx_temp where 1=1";
		//$this->pr($indxxdata,true);
		$this->smarty->assign("indexdata",$indxxdata);
		
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
		
		$this->show();
	}
	
	function upload()
	{
		$this->_baseTemplate="inner-template";
		$this->_bodyTemplate="reconstitution/upload";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		$this->smarty->assign('pagetitle','Reconstitution');
		$this->smarty->assign('bredcrumssubtitle','Upload Shares / Weights');
		$check=true;
		$errormsg='';
	
		if(isset($_POST['submit']) ){
			
			$csv_mimetypes = array(
					'text/csv',
					'text/plain',
					'application/csv',
					'text/comma-separated-values',
					'application/excel',
					'application/vnd.ms-excel',
					'application/vnd.msexcel',
					'text/anytext',
					'application/octet-stream',
					'application/txt',
					);


			if (!in_array($_FILES['inputfile']['type'], $csv_mimetypes)) 
			{
		$check=false;
				$errormsg='Invalid input file, Please upload correct csv file';
			//break;
			$this->Redirect("index.php?module=reconstitution&event=upload","Error in input :".$errormsg,"error");	
			}
			//$this->pr($_FILES,true);
			if($this->validatPost())
			{	
				$fields=array("1",'2','3','4','5');		
				//$this->pr($fields,true);
				
				$data = csv::import($fields,$_FILES['inputfile']['tmp_name']);	
				//$this->pr($data,true);
				//echo $data;
				


	$added=0;
	$indxx_id_array=array();
	$weightUpdateArray=array();
	$shareInsertArray=array();
		
				if(!empty($data))
				{
					
								
					 $ordered = array();
						foreach ($data as $key => $value)
							$ordered[$value['1']][] = $value;
					
						ksort($ordered, SORT_ASC);
						
						//$this->pr($ordered,true);
					
					//echo ksort($ordered, SORT_ASC);
					foreach($ordered as $key=> $indxx)
					{
						//$this->pr($ordered);
						 $indxx_id=$this->gettempIndexId($key);
						
						$indxx_id_array[]=$indxx_id;
						$sumofWeights=0;	
						foreach($indxx as $ticker)	
						{
							
							if(!$ticker[5] && !$ticker[4])
							{	$check=false;
								$errormsg.=" Share and Weights both not available for ".$key." and ticker ".$ticker[2]."<br>" ;
							}
							else{
								$ticker[5]=str_replace("%","",$ticker[5]);
								if( $ticker[5] && is_numeric($ticker[5])){
									$sumofWeights+=$ticker[5];	
									$weightUpdateArray[]="update tbl_indxx_ticker_temp set weight='".$ticker[5]."%' where ticker='".$ticker[2]."' and isin='".$ticker[3]."' and indxx_id='".$indxx_id."'";
								}
								else{
									$check=false;
									$errormsg.="weight is non numeric in index ".$key." and ticker ".$ticker[2]."<br>" ;
								}
							
								if($ticker[4])
								{
									if(!is_numeric($ticker[4]))
									{
										$check=false;
										$errormsg.="share is non numeric in index ".$key." and ticker ".$ticker[2]."<br>" ;
									}
									else{
										$shareInsertArray[]="('".$indxx_id."','".$ticker[3]."','".$ticker[4]."')";
								
									}
							
								}
							
							}	
						
						
						}
					if(strval($sumofWeights)!=100)
					{
						$check=false;
					$errormsg.="Sum is not 100% for index ".$key." : given sum is :".$sumofWeights."<br>" ;
					
					}
					
					}
					   
					
					//$this->pr($ordered,true);
									
				}

				if($check)
				{
				
				if(!empty($weightUpdateArray))
				{
				foreach($weightUpdateArray as $weightUpdate)
				$this->db->query($weightUpdate);
				
				}
				if(!empty($shareInsertArray))
				{
					//echo "delete from tbl_share_temp where indxx_id in (".implode(",",$indxx_id_array).")";
					$this->db->query("delete from tbl_share_temp where indxx_id in (".implode(",",$indxx_id_array).")");
					
				$this->db->query('insert into tbl_share_temp (indxx_id,isin,share) values '.implode(",",$shareInsertArray).";");
				}
					//exit;
				$this->Redirect("index.php?module=caupcomingindex","Share/Weights file Updated Successfully","success");	
				}
				
				if(!$check)
				{
					$this->Redirect("index.php?module=reconstitution&event=upload","Error in input :".$errormsg,"error");	}
				
				else
					{
						$this->Redirect("index.php?module=reconstitution&event=upload","No index added!!! <br> Please add again","error");	
					}

			}
			}
			$this->uploadfield2();
			$this->show();
		
		
		
		}
	
} // class ends here

?>