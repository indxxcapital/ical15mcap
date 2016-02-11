<?php

class Delistrunningsecuritiesupcomingnew extends Application{

	function __construct()
	{
		parent::__construct();
		$this->checkUserSession();
		
$this->addCss('assets/data-tables/DT_bootstrap.css');
$this->addJs('assets/bootstrap/bootstrap.min.js');
$this->addJs('assets/nicescroll/jquery.nicescroll.min.js');
$this->addJs('assets/data-tables/jquery.dataTables.js');
$this->addJs('assets/data-tables/DT_bootstrap.js');
$this->addJs('js/flaty.js');
	}
	
	
	function index()
	{
		
		$this->_baseTemplate="inner-template";
	$this->_bodyTemplate="delistrunningsecuritiesnew/index";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
//	$this->addfield();

		$this->smarty->assign('pagetitle','Index');
		$this->smarty->assign('bredcrumssubtitle','Index');
		$indexdata=$this->db->getResult("select tbl_indxx_temp.name,tbl_delist_tempindex_req.* from tbl_delist_tempindex_req left join tbl_indxx_temp on tbl_indxx_temp.id=tbl_delist_tempindex_req.indxx_id where 1=1  ",true);

		$this->smarty->assign("indexdata",$indexdata);

	//$this->pr($indexdata,true);
	
		//$this->pr($_SESSION);
		 $this->show();
	}
	
	
	
	protected function editfornext(){
		 
		
		$this->_baseTemplate="inner-template";
			$this->_bodyTemplate="delistrunning/edit";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
			
			$this->smarty->assign('pagetitle','Index');
		$this->smarty->assign('bredcrumssubtitle','Edit Index');
		$this->addeditfield();
		
		
		unset($_SESSION['NewIndxxName']);
		unset($_SESSION['NewIndxxId']);
		unset($_SESSION['indxx_code']);
		unset($_SESSION['indxx_type']);
		
		
		$editdata=$this->db->getResult("select tbl_delist_tempindex_req.*,tbl_indxx_temp.name as indexname from tbl_delist_tempindex_req left join  tbl_indxx_temp on tbl_indxx_temp.id=tbl_delist_tempindex_req.indxx_id where tbl_delist_tempindex_req.id='".$_GET['id']."'");
		//$this->pr($editdata,true);
		$this->smarty->assign("postData",$editdata);
		
		
		
		
		$tempindexid=0;
		
		if(isset($_POST['submit']))
		{
			$checkdata=$this->db->getResult("select tbl_delist_tempindex_req.* from tbl_delist_tempindex_req  where tbl_delist_tempindex_req.indxx_id='".$_POST['indxx_id']."' and tbl_delist_tempindex_req.startdate='".$_POST['dateStart']."'");
			if(empty($checkdata))
			{
					$this->db->query("INSERT into tbl_delist_tempindex_req set indxx_id='".mysql_real_escape_string($_POST['indxx_id'])."',startdate='".$_POST['dateStart']."'");
					
					$tempindexid=mysql_insert_id();
					
					
			}
			
			else
			{
				//$this->pr($checkdata,true);	
				
				
				$tempindexid=$checkdata['id'];
				
				$this->db->query("UPDATE tbl_delist_tempindex_req set indxx_id='".mysql_real_escape_string($_POST['indxx_id'])."',startdate='".$_POST['dateStart']."' where id='".$checkdata['id']."'");
				
				
			}
			
		
		
			$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew&event=edit2&id=".$tempindexid,"Index updated successfully!!!<br> Please update associated securities!!!","success");
			
			}
		
		 $this->show();
			
	}



function addNew3()
	{
			$this->_baseTemplate="inner-template";
			$this->_bodyTemplate="delistrunning/add3";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
			
			$this->smarty->assign('pagetitle','Index');
		$this->smarty->assign('bredcrumssubtitle','Add Securities');
		
		$totalfields=30;
		$this->smarty->assign('totalfields',$totalfields);
		
		
		$editdata=$this->db->getResult("select tbl_indxx_temp.* from tbl_indxx_temp  where tbl_indxx_temp.id='".$_GET['id']."'");
		//$this->pr($editdata,true);
		$this->smarty->assign("postData",$editdata);
		
		$this->addfield2($totalfields);
		
		$added=0;
		
		
		if($_POST['submit'])
		{
			
			
			
			for($i=1;$i<$_POST['totalfields'];$i++)
			{
				if($_POST['name'][$i] && $_POST['isin'][$i] && $_POST['ticker'][$i] && $_POST['divcurr'][$i] && $_POST['curr'][$i])
				{
					//$this->pr($_POST,true);	
					$this->db->query("INSERT into tbl_securities_replaced set status='0',name='".mysql_real_escape_string($_POST['name'][$i])."',isin='".mysql_real_escape_string($_POST['isin'][$i])."',ticker='".mysql_real_escape_string($_POST['ticker'][$i])."',curr='".mysql_real_escape_string($_POST['curr'][$i])."',divcurr='".mysql_real_escape_string($_POST['divcurr'][$i])."',indxx_id='".mysql_real_escape_string($_POST['indxx_id'])."'");
					$added++;
		
				}	
			}
			
			
			if($added>=1)
		{
			$indexxname=$this->db->getResult("select tbl_indxx_temp.name from tbl_indxx_temp  where tbl_indxx_temp.id='".$_GET['indxx_id']."'");
			
			$this->smarty->assign('indexxname',$indexxname['0']['name']);
			$this->Redirect("index.php?module=delistrunningsecurities&event=addNewNext&total=".$added."&id=".$_GET['indxx_id']."reqid=".$_GET['reqid'],"Index added successfully!!! <br> Please Wait for Approval","success");	
		}
		else
		{
			$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew&event=addNew2","No security added!!! <br> Please add again","error");	
		}
		}
		
		
		
			 $this->show();
	}
	






function editrunning(){
///echo "deepak";

		//$this->pr($_SESSION,true);
			$this->_baseTemplate="inner-template";
			$this->_bodyTemplate="delistrunning/editrunning";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
			
			$this->smarty->assign('pagetitle','Index');
		$this->smarty->assign('bredcrumssubtitle','Add Securities');
		//$this->pr($_SESSION,true);
		//echo "select tbl_indxx_ticker_temp.* from tbl_indxx_ticker_temp where indxx_id='".$_SESSION['liveindexid']."' ";
		//exit;
		$indexdata=$this->db->getResult("select tbl_indxx_temp.* from tbl_indxx_temp where id='".$_SESSION['tempindexid']."' ");
		
		$tickerdata=$this->db->getResult("select tbl_indxx_ticker_temp.* from tbl_indxx_ticker_temp where indxx_id='".$_SESSION['liveindexid']."' ",true);
		//$this->pr($indexdata,true);
		$this->smarty->assign("runningindexdata",$indexdata);
		
		$sharedata=$this->db->getResult("select tbl_share_temp.* from tbl_share_temp where indxx_id='".$_SESSION['liveindexid']."' ",true);
	//$this->pr($sharedata,true);
	$tempShareArray=array();
	
	if(!empty($sharedata))
	{
	foreach($sharedata as $share)
	{
	$tempShareArray[$share['isin']]=$share['share'];
	}
	}
		if(count($tickerdata)>0 && count($tickerdata)<=30)
		{
			$totalfields=30;	
		}elseif(count($tickerdata)>=30)
		{
			$totalfields=count($tickerdata);	
		}
		else
		{
		$totalfields=30;
		}
		
		$array=array();
		
		for($i=0;$i<$totalfields;$i++)
		{
			$array['name['.($i+1).']']=$tickerdata[$i]['name'];
			
			$array['isin['.($i+1).']']=$tickerdata[$i]['isin'];
			$array['ticker['.($i+1).']']=$tickerdata[$i]['ticker'];
			$array['share['.($i+1).']']=$tempShareArray[$tickerdata[$i]['isin']];
			$array['curr['.($i+1).']']=$tickerdata[$i]['curr'];
			$array['divcurr['.($i+1).']']=$tickerdata[$i]['divcurr'];
			
			//$array[]=	
		}
		
		$this->smarty->assign('postData',$array);
		$this->smarty->assign('totalfields',$totalfields);
		
		$this->addfieldrunning($totalfields,$indexdata['status'],$indexdata['dbusersignoff']);
		
		$added=0;
		
		
		if($_POST['submit'])
		{
			
			
			
				$this->db->query("delete from tbl_indxx_ticker_temp where indxx_id='".$_SESSION['tempindexid']."'");
			//$this->db->query("delete from tbl_share_temp where indxx_id='".$_SESSION['tempindexid']."'");
			
			for($i=1;$i<=$_POST['totalfields'];$i++)
			{
				if($_POST['name'][$i] && $_POST['isin'][$i] && $_POST['ticker'][$i]  && $_POST['curr'][$i])
				{
					//$this->pr($_POST,true);

					
						
					$this->db->query("INSERT into tbl_indxx_ticker_temp set status='1',name='".mysql_real_escape_string($_POST['name'][$i])."',isin='".mysql_real_escape_string($_POST['isin'][$i])."',ticker='".mysql_real_escape_string($_POST['ticker'][$i])."',weight='0',curr='".mysql_real_escape_string($_POST['curr'][$i])."',divcurr='".mysql_real_escape_string($_POST['divcurr'][$i])."',indxx_id='".mysql_real_escape_string($_SESSION['tempindexid'])."'");
					
					//$this->db->query("INSERT into tbl_share_temp set isin='".mysql_real_escape_string($_POST['isin'][$i])."',date='".$this->_date."',share='".mysql_real_escape_string($_POST['share'][$i])."',indxx_id='".mysql_real_escape_string($_SESSION['tempindexid'])."'");
					$added++;
		
				}	
			}
			
			
			if($added>=1)
		{
			$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew&event=addedrunning&id=".$added,"Index added successfully!!! <br> Please Wait for Approval","success");	
		}
		else
		{
			$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew&event=addNew2","No security added!!! <br> Please add again","error");	
		}
		}
		
		
		
			 $this->show();
	


}

	
	function edit2()
	{
		//$this->pr($_SESSION,true);
			$this->_baseTemplate="inner-template";
			$this->_bodyTemplate="delistrunning/add2";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
			
			$this->smarty->assign('pagetitle','Index');
		$this->smarty->assign('bredcrumssubtitle','Edit Securities');
		//$this->pr($_SESSION,true);
		//echo "select tbl_indxx_ticker_temp.* from tbl_indxx_ticker_temp where indxx_id='".$_SESSION['liveindexid']."' ";
		//exit;
		$indexdata=$this->db->getResult("select tbl_delist_tempindex_req.* from tbl_delist_tempindex_req where indxx_id='".$_GET['id']."' ");
		
		$tickerdata=$this->db->getResult("select tbl_securities_replaced.*,tbl_indxx_ticker_temp.* from tbl_securities_replaced left join tbl_indxx_ticker_temp on tbl_indxx_ticker_temp.indxx_id=tbl_securities_replaced.indxx_id where indxx_id='".$_GET['id']."' ",true);
		//$this->pr($indexdata,true);
		//$this->smarty->assign("indexdata",$indexdata);

		
		if(count($tickerdata)>0 && count($tickerdata)<=30)
		{
			$totalfields=30;	
		}elseif(count($tickerdata)>=30)
		{
			$totalfields=count($tickerdata);	
		}
		else
		{
		$totalfields=30;
		}
		
		$array=array();
		
		for($i=0;$i<$totalfields;$i++)
		{
			$array['name['.($i+1).']']=$tickerdata[$i]['name'];
			
			$array['isin['.($i+1).']']=$tickerdata[$i]['isin'];
			$array['ticker['.($i+1).']']=$tickerdata[$i]['ticker'];
			$array['weight['.($i+1).']']=$tickerdata[$i]['weight'];
			$array['curr['.($i+1).']']=$tickerdata[$i]['curr'];
			$array['divcurr['.($i+1).']']=$tickerdata[$i]['divcurr'];
			
			//$array[]=	
		}
		
		$this->smarty->assign('postData',$array);
		$this->smarty->assign('totalfields',$totalfields);
		
		$this->addfield2($totalfields);
		
		$added=0;
		
		
		if($_POST['submit'])
		{
				
			
			for($i=1;$i<=$_POST['totalfields'];$i++)
			{
				if($_POST['name'][$i] && $_POST['isin'][$i] && $_POST['ticker'][$i] && $_POST['weight'][$i] && $_POST['curr'][$i])
				{
					//$this->pr($_POST,true);
					
						
					$this->db->query("UPDATE into tbl_indxx_ticker_temp set status='0',name='".mysql_real_escape_string($_POST['name'][$i])."',isin='".mysql_real_escape_string($_POST['isin'][$i])."',ticker='".mysql_real_escape_string($_POST['ticker'][$i])."',weight='".mysql_real_escape_string($_POST['weight'][$i])."',curr='".mysql_real_escape_string($_POST['curr'][$i])."',indxx_id='".mysql_real_escape_string($_SESSION['tempindexid'])."'");
					
					$added++;
		
				}	
			}
			
			
			if($added>=1)
		{
			$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew&event=addNewNext&id=".$added,"Index added successfully!!! <br> Please Wait for Approval","success");	
		}
		else
		{
			$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew&event=addNew2","No security added!!! <br> Please add again","error");	
		}
		}
		
		
		
			 $this->show();
	}
	
	
	function getSecurityId($isin,$indxx_id){

	$indexdata=$this->db->getResult("select tbl_indxx_ticker_temp.id from tbl_indxx_ticker_temp where indxx_id='".$indxx_id."' and isin='".$isin."' ",false);
	return $indexdata['id'];
	
		
	}
	
	
	function addNew2()
	{
			$this->_baseTemplate="inner-template";
			$this->_bodyTemplate="delistrunningsecuritiesnew/add2";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
			
			$this->smarty->assign('pagetitle','Index');
		$this->smarty->assign('bredcrumssubtitle','Select Securities');
		//$this->pr($_SESSION,true);
		
		
		
		$cas=$this->db->getResult("select tbl_indxx_temp.name,tbl_indxx_temp.code,tbl_indxx_temp.id from tbl_indxx_ticker_temp
		left join tbl_indxx_temp on tbl_indxx_temp.id=tbl_indxx_ticker_temp.indxx_id
		
		 where isin='".$_SESSION['delistsecurity']."'",true);
	
	//$this->pr($cas,true);
	$this->smarty->assign('cas',$cas);
	
	
	
		
	if(isset($_POST['submit']))
	{
		//$this->pr($_POST,true);
		$i=0;
		if(!empty($_POST['checkboxid']))
		{
		foreach($_POST['checkboxid'] as $ca_id)
		{
			
			$security_id=$this->getSecurityId($_SESSION['delistsecurity'],$ca_id);
			if($security_id)
{			
			$req_id=0;	
			
			//echo "select * from tbl_delist_tempindex_req  where indxx_id='".$ca_id."' and startdate='".$_SESSION['delistDate']."'";
			//exit;
	$query=$this->db->getResult("select * from tbl_delist_tempindex_req  where indxx_id='".$ca_id."' and startdate='".$_SESSION['delistDate']."'",false);
	if(!empty($query))
	$req_id=$query['id'];
	else{
$this->db->query("insert into tbl_delist_tempindex_req  (indxx_id ,startdate,status,user_id) values ('".$ca_id."','".$_SESSION['delistDate']."','1','".$_SESSION['User']['id']."');");
	$req_id=mysql_insert_id();
	}
	
	
	$query=$this->db->getResult("select * from tbl_delist_tempsecurity  where indxx_id='".$ca_id."' and req_id='".$req_id."' and security_id='".$_SESSION['delistsecurity']."'",false);
	if(empty($query)){
$this->db->query("insert into `tbl_delist_tempsecurity`  (status,indxx_id ,security_id,req_id) values ('1','".$ca_id."','".$_SESSION['delistsecurity']."','".$req_id."');");
}
	
}
	
	
	//$this->db->query("insert into tbl_indxx_temp  (indxx_id ,startdate) values ('".$ca_id."','".$_SESSION['delistDate']."');");

			$i++;
			
		}
		}
		
		
		


//print_r($emailto);

	//exit;

//	echo  'Select  email from tbl_ca_user where type="1" or id in ('.implode(',',$emailto).') ';
	$admins =	$this->db->getResult('Select  email from tbl_ca_user where type="1"  ',true);

//$this->pr($admins,true);	
	
	$user=array();
	if(!empty($admins))	
	foreach($admins as $admin)
	{
	$user[]=$admin['email'];
	}
		
  $to=implode(',',$user);		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'From: Indexing <indexing@indxx.com>' . "\r\n";
$body='Hi <br>';
$body.='New request for Delist security has been submitted  , <br> Please  <a href="'.$this->siteconfig->base_url.'index.php?module=delistrunningsecurities">Click here </a> to do more.<br>Thanks ';


		mail($to,"ICAI :Security Delist Request " ,$body,$headers);
		
		$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew","Securities to be delisted added successfully!!! <br> Please Wait for Approval","success");
	}
		
			 $this->show();
	
	}
	
	
	
	function checkDelistReq($indxx_id,$date){
		
	
	}
	
	private function addfield2($count)
	{	
	   for($i=1;$i<=$count;$i++)
{	   
	   $this->validData[]=array("feild_label" =>"Security Name",
	   								"feild_code" =>"name[".$i.']',
								 "feild_type" =>"text",
								 "feild_tpl" =>"place_text3",
								 "is_required" =>"",
								
								 );
		 $this->validData[]=array("feild_label" =>"Security Isin",
		 							"feild_code" =>"isin[".$i.']',
								 "feild_type" =>"text",
								 "feild_tpl" =>"place_text2",
								 "is_required" =>"",
								
								 );
								 
		 $this->validData[]=array("feild_label" =>"Security Ticker",
		 							"feild_code" =>"ticker[".$i.']',
								 "feild_type" =>"text",
								  "feild_tpl" =>"place_text2",
								 "is_required" =>"",
								
								 );
								 
								 
								  $this->validData[]=array(	"feild_label"=>"Currency",
	 							"feild_code" =>"curr[".$i.']',
								 "feild_type" =>"text",
								  "feild_tpl" =>"place_text2",
								 "is_required" =>"",
								
								 );	 
	 
								 
		 $this->validData[]=array("feild_label" =>"Div Currency",
		 							"feild_code" =>"divcurr[".$i.']',
								 "feild_type" =>"text",
								 "is_required" =>"",
								  "feild_tpl" =>"place_text2",
								);
	
	
	 /*$this->validData[]=array("feild_label" =>"Index",
	 							"feild_code" =>"indxx_id[".$i.']',
								 "feild_type" =>"hidden",
								 "is_required" =>"",
								   "feild_tpl" =>"hidden2",
								 'value'=>$_SESSION['NewIndxxId']
								 );*/
								 
								 
								 
	$this->validData[]=array("feild_label" =>"Index",
	 							"feild_code" =>"indxx_id",
								 "feild_type" =>"hidden",
								 "is_required" =>"",
								   "feild_tpl" =>"hidden2",
								 'value'=>$_GET['indxx_id']
								 );
	
	
}
	$this->getValidFeilds();
	}
	
	private function addfieldrunning($count,$status=0,$dbusersignoff=0)
	{	
	//echo $status.$dbusersignoff;
		//exit;
	   for($i=1;$i<=$count;$i++)
{	   
	   $this->validData[]=array("feild_label" =>"Security Name",
	   								"feild_code" =>"name[".$i.']',
								 "feild_type" =>"text",
								 "feild_tpl" =>"place_text3",
								 "is_required" =>"",
								
								 );
		 $this->validData[]=array("feild_label" =>"Security Isin",
		 							"feild_code" =>"isin[".$i.']',
								 "feild_type" =>"text",
								 "feild_tpl" =>"place_text2",
								 "is_required" =>"",
								
								 );
								 
		 $this->validData[]=array("feild_label" =>"Security Ticker",
		 							"feild_code" =>"ticker[".$i.']',
								 "feild_type" =>"text",
								  "feild_tpl" =>"place_text2",
								 "is_required" =>"",
								
								 );
								 
		/*if($dbusersignoff==1 && $status==1)
		{ $this->validData[]=array("feild_label" =>"Share",
		 							"feild_code" =>"share[".$i.']',
								 "feild_type" =>"text",
								 "is_required" =>"",
								  "feild_tpl" =>"place_text2",
								);
		}
	else{
	 $this->validData[]=array("feild_label" =>"Share",
		 							"feild_code" =>"share[".$i.']',
								 "feild_type" =>"hidden",
								 "is_required" =>"",
								  
								);
		
	}*/
	 $this->validData[]=array("feild_label" =>"Index",

	 							"feild_code" =>"indxx_id[".$i.']',
								 "feild_type" =>"hidden",
								 "is_required" =>"",
								   "feild_tpl" =>"hidden2",
								 'value'=>$_SESSION['NewIndxxId']
								 );
	
	 $this->validData[]=array(	"feild_label"=>"Ticker Currency",
	 							"feild_code" =>"curr[".$i.']',
								 "feild_type" =>"text",
								  "feild_tpl" =>"place_text2",
								 "is_required" =>"",
							// "feildValues"=>array("onclick"=>"checkvalue('".$id."')"),
								
								 );	 
	$this->validData[]=array(	"feild_label"=>"Dividend Currency",
	 							"feild_code" =>"divcurr[".$i.']',
								 "feild_type" =>"text",
								  "feild_tpl" =>"place_text2",
								 "is_required" =>"",
								
								 );	 
}
	$this->getValidFeilds();
	}
	
	
	
	function addNew()
	{
			$this->_baseTemplate="inner-template";
			$this->_bodyTemplate="delistrunningsecuritiesnew/add";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		
			//$array=$this->getRunningSecurities();
			//$this->pr($array,true);
		//	$this->smarty->assign("securities",$array);
			$this->smarty->assign('pagetitle','Index');
		$this->smarty->assign('bredcrumssubtitle','Add Index');
		
		
		unset($_SESSION['NewIndxxName']);
		unset($_SESSION['NewIndxxId']);
		unset($_SESSION['indxx_code']);
		unset($_SESSION['indxx_type']);
		
		
		
		if(isset($_POST['submit']))
		{
			
			$_SESSION['delistDate']=$_POST['startdate'];
			$_SESSION['delistsecurity']=$_POST['isin'];
			
			
			
			//$this->pr($_POST,true);
			
		/*	
			$this->db->query("INSERT into tbl_delist_tempindex_req set indxx_id='".mysql_real_escape_string($_POST['indxx_id'])."',startdate='".mysql_real_escape_string($_POST['startdate'])."',user_id='".$_SESSION['User']['id']."' ");
			
			$reqid=mysql_insert_id();*/
			
			$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew&event=addNew2&delistDate=".$_POST['startdate']."&isin=".$_POST['isin'],"Please Select index for Security ".$_POST['isin']." for deletion on ".$_POST['startdate'],"success");	
		}
		
	
			
			$this->addfieldreplace();
			 $this->show();
	}
	
	
	private function addfieldreplace()
	{	
	  
								 
		 
	 $this->validData[]=array(	"feild_label"=>"Index Start Date",
	 							"feild_code" =>"startdate",
								 "feild_type" =>"date",
								 "is_required" =>"1",
								);
									 
 $this->validData[]=array("feild_label" =>"Security",
	   								"feild_code" =>"isin",
								 "feild_type" =>"radio",
								 "is_required" =>"1",
								 "model"=>$this->getUpcomingSecurities(),
								
								 );
		
	
	$this->getValidFeilds();
	}
	
	
	
	
	
	
function addedrunning()
{
	
	$indexdata=$this->db->getResult("select tbl_indxx_temp.* from tbl_indxx_temp where id='".$_SESSION['tempindexid']."' ");
	//$this->pr($indexdata,true);
		$this->_baseTemplate="inner-template";
			$this->_bodyTemplate="delistrunning/addedrunning";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
			
			$this->smarty->assign('pagetitle','Securities');
					
			$this->smarty->assign('indexdata',$indexdata);
		$this->smarty->assign('bredcrumssubtitle','Add/Submit Securities');
		
		
		
			
		$this->show();

	
	}	
	function addNewNext()
	{
		$this->_baseTemplate="inner-template";
			$this->_bodyTemplate="delistrunning/addnext";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
			
			$this->smarty->assign('pagetitle','Securities');
		$this->smarty->assign('bredcrumssubtitle','Add/Submit Securities');
		
		$indexname=$this->db->getResult("select tbl_indxx_temp.name as indexxname,tbl_delist_tempindex_req.* from tbl_indxx_temp left join  tbl_delist_tempindex_req on tbl_delist_tempindex_req.indxx_id=tbl_indxx_temp.id where tbl_delist_tempindex_req.id='".$_GET['reqid']."' ",true);
		
		$countid=$this->db->getResult("select count(id) as countid from tbl_delist_tempsecurity where req_id='".$_GET['reqid']."'");
		
	//	$this->pr($indexname,true);		
		$this->smarty->assign('indexxname',$indexname['0']['indexxname']);
		$this->smarty->assign('total',$countid['countid']);
		
			
		$this->show();
	}
	
	
	
	
	
	private function addfield()
	{	
	   $this->validData[]=array("feild_label" =>"Index Name",
	   								"feild_code" =>"name",
								 "feild_type" =>"text",
								 "is_required" =>"1",
								
								 );
		 $this->validData[]=array("feild_label" =>"Index Ticker",
		 							"feild_code" =>"code",
								 "feild_type" =>"text",
								 "is_required" =>"1",
								
								 );
								 
		 $this->validData[]=array("feild_label" =>"Investment Amount",
		 							"feild_code" =>"investmentammount",
								 "feild_type" =>"text",
								 "is_required" =>"",
								
								 );
								 
		 $this->validData[]=array("feild_label" =>"Index value",
		 							"feild_code" =>"indexvalue",
								 "feild_type" =>"text",
								 "is_required" =>"1",
								
								 );
	 $this->validData[]=array("feild_label" =>"Type",
	 							"feild_code" =>"type",
								 "feild_type" =>"select",
								 "is_required" =>"1",
								 //"feild_tpl" =>"selectsearch",
								  "model"=>$this->getTypes(),
								 );
								 
								 $this->validData[]=array("feild_label" =>"Zone",
	 							"feild_code" =>"zone",
								 "feild_type" =>"select",
								 "is_required" =>"1",
								 //"feild_tpl" =>"selectsearch",
								  "model"=>$this->getCalendarZone(),
								 );
								 
	 $this->validData[]=array(	"feild_label"=>"Currency",
	 							"feild_code" =>"curr",
								 "feild_type" =>"text",
								 "is_required" =>"1",
								
								 );
								 	 
	 $this->validData[]=array(	"feild_label"=>"Index Start Date",
	 							"feild_code" =>"dateStart",
								 "feild_type" =>"date",
								 "is_required" =>"1",
								);
									 
	/*$this->validData[]=array("feild_code" =>"product_file",
									 "feild_type" =>"file",
									 "is_required" =>"1",
									 "validate" =>"file|csv",		
									 "feild_label" =>"Upload File",
									 );*/
								 
								 
	 
	
	$this->getValidFeilds();
	}
	
	
	private function addeditfield()
	{	
	  	 $this->validData[]=array("feild_label" =>"Index",
	 							"feild_code" =>"indexname",
								 "feild_type" =>"select",
								 "is_required" =>"1",
								 //"feild_tpl" =>"selectsearch",
								  "model"=>$this->getRunningIndexes(),
								 );
		
								 	 
	 $this->validData[]=array(	"feild_label"=>"Index Start Date",
	 							"feild_code" =>"dateStart",
								 "feild_type" =>"date",
								 "is_required" =>"1",
								);
									 
	
	
	$this->getValidFeilds();
	}
	
	
	
	
	
	 protected function view(){
		 
		$this->_baseTemplate="inner-template";
			$this->_bodyTemplate="delistrunning/view";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
			
			$this->smarty->assign('pagetitle','Index');
		$this->smarty->assign('bredcrumssubtitle','ViewIndex');
		
		
		
		$viewdata=$this->db->getResult("select * from tbl_indxx_temp where tbl_indxx_temp.id='".$_GET['id']."'",true);
		//$this->pr($viewdata,true);
		$this->smarty->assign("viewindexdata",$viewdata);
		
		
		
		
		
		
		
		
		$userdata=$this->db->getResult("select tbl_ca_user.*,tbl_assign_index.* from tbl_assign_index left join tbl_ca_user on tbl_ca_user.id=tbl_assign_index.user_id where tbl_assign_index.indxx_id='".$_GET['id']."'",true);
		//$this->pr($viewdata,true);
		//$this->smarty->assign("totalusers",count($userdata));
		$this->smarty->assign("userdata",$userdata);
		
		
		
		$sequruityData=$this->db->getResult('SELECT * FROM tbl_indxx_ticker_temp where indxx_id="'.$_GET['id'].'"',true);
	//	$this->pr($sequruityData,true);
		$this->smarty->assign("indexSecurity",$sequruityData);
		$this->smarty->assign("totalindexSecurityrows",count($sequruityData));
		
		
		 $this->show();
			
	}
		protected function delete(){
		 
		 //$this->_baseTemplate="inner-template";
			//$this->_bodyTemplate="delistrunning/delete";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
			
			$this->smarty->assign('pagetitle','Index');
		$this->smarty->assign('bredcrumssubtitle','Deleted Index');
		
		
		if($_SESSION['User']['type']=='1' && $_GET['id'])
		{
		 $deleteddata=$this->db->getResult("select tbl_delist_tempindex_req.*,tbl_indxx_temp.name from tbl_delist_tempindex_req left join tbl_indxx_temp on tbl_delist_tempindex_req.indxx_id=tbl_indxx_temp.id where tbl_delist_tempindex_req.id='".$_GET['id']."'");
		$this->smarty->assign("deleteddata",$deleteddata);
		
		 	$indxx=$deleteddata;
		//$this->pr($indxx,true);
		
		/*$indxxadmins =	$this->db->getResult('Select  user_id from tbl_assign_index_temp2 where indxx_id="'.$_GET['id'].'" ',true);
$indxxadmin='';
//print_r($indxxadmins );
$emailto=array();

if(!empty($indxxadmins))
{
foreach($indxxadmins as $array)
{
	$emailto[]=$array['user_id'];
}


}

//print_r($emailto);

	//exit;
	if(!empty($emailto))
	{	
//	echo  'Select  email from tbl_ca_user where type="1" or id in ('.implode(',',$emailto).') ';
	$admins =	$this->db->getResult('Select  email from tbl_ca_user where type="1" or id in ('.implode(',',$emailto).') ',true);
	}
else
{	$admins =	$this->db->getResult('Select  email from tbl_ca_user where 1=1 ',true);
}
//$this->pr($admins,true);	
	
	$user=array();
	if(!empty($admins))	
	foreach($admins as $admin)
	{
	$user[]=$admin['email'];
	}
		
  $to=implode(',',$user);		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'From: Indexing <indexing@indxx.com>' . "\r\n";
$body='Hi <br>';
$body.='Your Indxx '.$indxx['name'].'('.$indxx['code'].') has been deleted by admin , <br> Please  <a href="'.$this->siteconfig->base_url.'index.php?module=delistrunningsecurities">Click here </a> to do more.<br>Thanks ';


		mail($to,"ICAI :Upcoming Indxx Deleted " ,$body,$headers);*/
		
		
	
		 
		 
		 
		 
		$strQuery = "delete from tbl_delist_tempindex_req where tbl_delist_tempindex_req.id='".$_GET['id']."'";
			$this->db->query($strQuery);
			
			
			//$strQuery2 = "delete from tbl_securities_replaced where tbl_securities_replaced.indxx_id='".$_GET['id']."'";
			//$this->db->query($strQuery2);
			
			
			$strQuery3 = "delete from tbl_delist_tempsecurity where tbl_delist_tempsecurity.indxx_id='".$_GET['id']."'";
			$this->db->query($strQuery3);
			
			
			$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew","Index deleted successfully!","success");
			
			
			
						
	//	echo "select * tbl_indxx_temp where tbl_indxx_temp.id='".$_GET['id']."'";
				
			
			
			
			
			
			
		}
		
		else
		{
			$checkdata=$this->db->getResult("select tbl_delist_tempindex_req.* from tbl_delist_tempindex_req where tbl_delist_tempindex_req.id='".$_GET['id']."'");
		
		if($checkdata['status']=='1')
		{
				$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew","You are not authorized to perofrm this task!","error");
		}
		else
		{
			$strQuery = "delete from tbl_delist_tempindex_req where tbl_delist_tempindex_req.id='".$_GET['id']."'";
			$this->db->query($strQuery);
			
			
			$strQuery3 = "delete from tbl_delist_tempsecurity where tbl_delist_tempsecurity.indxx_id='".$_GET['id']."'";
			$this->db->query($strQuery3);
			
			
			$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew","Index deleted successfully!","success");	
		}
		}
			
			$this->show();
	}
	
	
	
	
	
	protected function deleteindex(){
		 
		 $this->_baseTemplate="inner-template";
			$this->_bodyTemplate="delistrunning/delete";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
			
			$this->smarty->assign('pagetitle','Index');
		$this->smarty->assign('bredcrumssubtitle','Deleted Index');
		
		
			if(!empty($_POST))
			{
				//$this->pr($_POST,true);
				
			foreach($_POST['array1'] as $key2=>$val2)
			{
				if(!empty($val2) && $val2)
				{
			
		 $deleteddata=$this->db->getResult("select tbl_delist_tempindex_req.*,tbl_indxx_temp.name from tbl_delist_tempindex_req left join tbl_indxx_temp on tbl_delist_tempindex_req.indxx_id=tbl_indxx_temp.id where tbl_delist_tempindex_req.id='".$val2."'");
		$this->smarty->assign("deleteddata",$deleteddata);
		
		 $indexname=$deleteddata['name'];
		 $indexticker=$deleteddata['ticker'];
	
		
		 	$indxx=$deleteddata;
		//$this->pr($indxx,true);
		
	/*	$indxxadmins =	$this->db->getResult('Select  user_id from tbl_assign_index_temp2 where indxx_id="'.$val2.'" ',true);
$indxxadmin='';
//print_r($indxxadmins );
$emailto=array();

if(!empty($indxxadmins))
{
foreach($indxxadmins as $array)
{
	$emailto[]=$array['user_id'];
}


}

//print_r($emailto);

	//exit;
	if(!empty($emailto))
	{	
//	echo  'Select  email from tbl_ca_user where type="1" or id in ('.implode(',',$emailto).') ';
	$admins =	$this->db->getResult('Select  email from tbl_ca_user where type="1" or id in ('.implode(',',$emailto).') ',true);
	}
else
{	$admins =	$this->db->getResult('Select  email from tbl_ca_user where 1=1 ',true);
}
//$this->pr($admins,true);	
	
	$user=array();
	if(!empty($admins))	
	foreach($admins as $admin)
	{
	$user[]=$admin['email'];
	}
		
  $to=implode(',',$user);		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'From: Indexing <indexing@indxx.com>' . "\r\n";
$body='Hi <br>';
$body.='Your Upcoming Indxx '.$indxx['name'].'('.$indxx['code'].') has been deleted by admin , <br> Please  <a href="'.$this->siteconfig->base_url.'index.php?module=delistrunningsecurities">Click here </a> to do more.<br>Thanks ';


		mail($to,"ICAI :Upcoming Indxx Deleted " ,$body,$headers);
		*/
		
	
		 
		 
		 
		 
		 
		$strQuery = "delete from tbl_delist_tempindex_req where tbl_delist_tempindex_req.id='".$val2."'";
			$this->db->query($strQuery);
			
			
			//$strQuery2 = "delete from tbl_securities_replaced where tbl_securities_replaced.indxx_id='".$val2."'";
			//$this->db->query($strQuery2);
			
			
			$strQuery3 = "delete from tbl_delist_tempsecurity where tbl_delist_tempsecurity.indxx_id='".$val2."'";
			$this->db->query($strQuery3);
			
			
			
						
	//	echo "select * tbl_indxx_temp where tbl_indxx_temp.id='".$_GET['id']."'";
				
			
			
			
			
			
			
		
		}
			}
		}
		
		else
		{
			$checkdata=$this->db->getResult("select tbl_delist_tempindex_req.* from tbl_delist_tempindex_req where tbl_delist_tempindex_req.id='".$_GET['id']."'");
		
		if($checkdata['status']=='1')
		{
			
				$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew","You are not authorized to perofrm this task!","error");
		}
			else
			{
				$strQuery = "delete from tbl_delist_tempindex_req where tbl_delist_tempindex_req.id='".$val2."'";
			$this->db->query($strQuery);
			
			
			//$strQuery2 = "delete from tbl_securities_replaced where tbl_securities_replaced.indxx_id='".$val2."'";
			//$this->db->query($strQuery2);
			
			
			$strQuery3 = "delete from tbl_delist_tempsecurity where tbl_delist_tempsecurity.indxx_id='".$val2."'";
			$this->db->query($strQuery3);
			
			}
		}
			$this->show();
	}
	
	
	function subindextemp()
	{
		
		if($_GET['id'])
		{

		
		
		$indxx=$this->db->getResult("select id from tbl_delist_tempindex_req where tbl_delist_tempindex_req.id='".$_GET['reqid']."'");
		$this->smarty->assign("indxx",$indxx);
	$admins =	$this->db->getResult('Select  email from tbl_ca_user where type="1" ');
//$this->pr($admins,true);	
	if(!empty($admins))	
	 $to=implode(',',$admins);
	//exit;	
		
//		mail()
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'From: Indexing <indexing@indxx.com>' . "\r\n"."CC: indexing@indxx.com". "\r\n";
$body='Hi <br>';
$body.='You have new indxx for Delisting, <br> Please  <a href="'.$this->siteconfig->base_url.'index.php?module=delistrunningsecurities&event=viewupcoming&id='.$_GET['reqid'].'">Click here </a> to check it.<br>Thanks ';


		mail($to,"ICAI : New indxx submitted for Delisting Approval" ,$body,$headers);
		
		$this->db->query("UPDATE tbl_delist_tempindex_req set status='1' where id='".$_GET['reqid']."'");
		
		
		$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew","Record updated successfully!!!","success");	
		}
		else
		{
				$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew","You are not authorized to perofrm this task!","error");
		}
		//
	}
	
	
	
	protected function viewupcoming(){
		 
		$this->_baseTemplate="inner-template";
			$this->_bodyTemplate="delistrunningsecuritiesnew/viewupcoming";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
			
			$this->smarty->assign('pagetitle','Index');
		$this->smarty->assign('bredcrumssubtitle','View Index');
		
		//$this->pr($_SESSION,true);
		
		///$viewadmindata=$this->db->getResult("select isAdmin from tbl_assign_index_temp where indxx_id='".$_GET['id']."' and user_id='".$_SESSION['User']['id']."'",true);
		//$this->pr($viewadmindata,true);
		//$this->smarty->assign("viewadmindata",$viewadmindata);
		
	
		
		$viewdata=$this->db->getResult("select tbl_delist_tempindex_req.*,tbl_indxx_temp.name as indexname,tbl_indxx_temp.code from tbl_delist_tempindex_req left join tbl_indxx_temp on tbl_indxx_temp.id=tbl_delist_tempindex_req.indxx_id where tbl_delist_tempindex_req.id='".$_GET['id']."'",true);
		//$this->pr($viewdata,true);
		$this->smarty->assign("viewindexdata",$viewdata);
		
		
		
		$securitytobedelisted=$this->db->getResult("select tbl_indxx_ticker_temp.* from tbl_delist_tempsecurity left join tbl_indxx_ticker_temp on tbl_indxx_ticker_temp.isin=tbl_delist_tempsecurity.security_id  where tbl_delist_tempsecurity.req_id='".$_GET['id']."' and  tbl_delist_tempsecurity.indxx_id='".$viewdata[0]['indxx_id']."' and tbl_indxx_ticker_temp.indxx_id='".$viewdata[0]['indxx_id']."'",true);
		//$this->pr($securitytobereplaced);
		$this->smarty->assign("securitytobedelisted",$securitytobedelisted);
		
		
		//$newsecurities=$this->db->getResult('SELECT tbl_delist_tempindex_req.id as indxxid,tbl_securities_replaced. * FROM tbl_securities_replaced  left join tbl_delist_tempindex_req on tbl_delist_tempindex_req.indxx_id=tbl_securities_replaced.indxx_id where tbl_delist_tempindex_req.id="'.$_GET['reqid'].'"',true);
		//$this->pr($newsecurities,true);
		//$this->smarty->assign("indexSecurity",$newsecurities);
		$this->smarty->assign("total1",count($securitytobedelisted));
		//$this->smarty->assign("total2",count($newsecurities));
		
		
		 $this->show();
			
	}
	
	
	
	
	
	protected function approvetemp(){
		 
		$this->_baseTemplate="inner-template";
			$this->_bodyTemplate="delistrunning/approve";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
			
			$this->smarty->assign('pagetitle','Index');
		$this->smarty->assign('bredcrumssubtitle','Approved Index');
		
			if($_SESSION['User']['type']=='1' && $_GET['id'])
		{
		
		
		$this->db->query("UPDATE tbl_delist_tempindex_req set adminapprove='1' where tbl_delist_tempindex_req.id='".$_GET['id']."'");
		
		
		
		
		if($_GET['id'])
		{

		
		
		$indxx=$this->db->getResult("select * from tbl_indxx_temp where tbl_indxx_temp.id='".$_GET['id']."'");
		$this->smarty->assign("indxx",$indxx);
	$admins =	$this->db->getResult('Select  email from tbl_database_users');
//$this->pr($admins,true);	
	if(!empty($admins))	
	 $to=implode(',',$admins);
	//exit;	
		
//		mail()
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'From: Indexing <indexing@indxx.com>' . "\r\n";
$body='Hi <br>';
$body.='There is a change in database. <br> Please upload new securities and currencies. ';
$body.="Delisting in : ".$indxx['name']."(".$indxx['code'].")";



		mail($to,"ICAI : Change In Database : Delisting" ,$body,$headers);
		
		
		$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew","Record updated successfully!!!","success");	
		}
		else
		{
				$this->Redirect("index.php?module=delistrunningsecuritiesupcomingnew","You are not authorized to perofrm this task!","error");
		}
		
		
		
		
		}
		 $this->show();
			
	}
	
	
	
	
	protected function dbapprove(){
		 
		$this->_baseTemplate="inner-template";
			$this->_bodyTemplate="delistrunning/approve";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
			
			$this->smarty->assign('pagetitle','Index');
		$this->smarty->assign('bredcrumssubtitle','Approved Index');
		
			if($_SESSION['User']['type']=='3' && $_GET['id'])
		{
		
		
		
		$viewdata=$this->db->getResult("select tbl_delist_tempindex_req.*,tbl_indxx_temp.name as indexname,tbl_indxx_temp.code from tbl_delist_tempindex_req left join tbl_indxx_temp on tbl_indxx_temp.id=tbl_delist_tempindex_req.indxx_id where tbl_delist_tempindex_req.id='".$_GET['id']."'",true);
		//$this->pr($viewdata,true);
		$this->smarty->assign("viewindexdata",$viewdata);
		

		
		
		$securitytobedelisted=$this->db->getResult("select tbl_delist_tempsecurity.*,tbl_indxx_ticker_temp.*,tbl_delist_tempindex_req.id as indxxid from tbl_delist_tempsecurity left join tbl_indxx_ticker_temp on tbl_indxx_ticker_temp.id=tbl_delist_tempsecurity.security_id left join tbl_delist_tempindex_req on tbl_delist_tempindex_req.indxx_id=tbl_delist_tempsecurity.indxx_id where tbl_delist_tempindex_req.id='".$_GET['id']."'",true);
		//$this->pr($viewdata,true);
		$this->smarty->assign("securitytobedelisted",$securitytobedelisted);
		
		
		//$newsecurities=$this->db->getResult('SELECT tbl_delist_tempindex_req.id as indxxid,tbl_securities_replaced. * FROM tbl_securities_replaced  left join tbl_delist_tempindex_req on tbl_delist_tempindex_req.indxx_id=tbl_securities_replaced.indxx_id where tbl_delist_tempindex_req.id="'.$_GET['id'].'"',true);
	//	$this->pr($sequruityData,true);
		//$this->smarty->assign("indexSecurity",$newsecurities);
		$this->smarty->assign("total1",count($securitytobedelisted));
		//$this->smarty->assign("total2",count($newsecurities));
		
		
		
					$this->db->query("UPDATE tbl_delist_tempindex_req set dbapprove='1' where tbl_delist_tempindex_req.id='".$_GET['id']."'");
		
		
		
		
		
		}
		 $this->show();
			
	}
	
	
	
	
} // class ends here

?>