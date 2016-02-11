<?php

class Restoreindexlive extends Application{

	function __construct()
	{
		parent::__construct();
	$this->addCss('assets/data-tables/DT_bootstrap.css');
$this->addJs('assets/bootstrap/bootstrap.min.js');
$this->addJs('assets/nicescroll/jquery.nicescroll.min.js');
$this->addJs('assets/data-tables/jquery.dataTables.js');
$this->addJs('assets/data-tables/DT_bootstrap.js');
$this->addJs('js/flaty.js');	
		$this->checkusersession();
	}
	
	
	function index()
	{
		
		//echo "deepak";
		//exit;
		
		$this->_baseTemplate="inner-template";
	$this->_bodyTemplate="restoreindexlive/index";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;

		

if(!empty($_POST))
{
if(!empty($_POST['index_id']))
{
  	$ids=implode(",",$_POST['index_id']);
 
if($ids)
{
	//echo "delete from tbl_indxx where id not in (".$ids.")";
$this->db->query("delete from tbl_indxx where id not in (".$ids.")");
$this->db->query("delete from tbl_indxx_ticker where indxx_id not in (".$ids.")");
$this->db->query("delete from tbl_share where indxx_id not in (".$ids.")");
$this->db->query("delete from tbl_final_price where indxx_id not in (".$ids.")");
$this->db->query("delete from tbl_indxx_value_open where indxx_id not in (".$ids.")");
$this->db->query("delete from tbl_indxx_value where indxx_id not in (".$ids.")");
}


}




}

	//$this->pr($holidaydata,true);
	
	$holidaydata=$this->db->getResult("select * from tbl_indxx" ,true);
		
		$this->smarty->assign("liveindexdata",$holidaydata);
			//$this->pr($_SESSION);
		 $this->show();
	}
	
	function addNew()
	{
			$this->_baseTemplate="inner-template";
			$this->_bodyTemplate="holidays/add";
			$this->_title=$this->siteconfig->site_title;
			$this->_meta_description=$this->siteconfig->default_meta_description;
			$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
			
			$this->smarty->assign('pagetitle','holidays');
		$this->smarty->assign('bredcrumssubtitle','Add holidays');
		
		
		if(isset($_POST['submit']))
		{
		$this->db->query("INSERT into tbl_holidays set status='1',title='".mysql_real_escape_string($_POST['title'])."',zone_id='".mysql_real_escape_string($_POST['zone_id'])."',date='".mysql_real_escape_string($_POST['date'])."'");
		
		
			$this->Redirect("index.php?module=holidays&event=addNew","Record added successfully!!!","success");	
		}
			
			$this->addfield();
			 $this->show();
	}
	
	
	
	
	
	
	
	
	
	
} // class ends here

?>