<?php 

class Myindextemp extends Application{
		
		function __construct()
		{
			parent::__construct();
	$this->addCss('assets/data-tables/DT_bootstrap.css');
$this->addJs('assets/bootstrap/bootstrap.min.js');
$this->addJs('assets/nicescroll/jquery.nicescroll.min.js');
$this->addJs('assets/data-tables/jquery.dataTables.js');
$this->addJs('assets/data-tables/DT_bootstrap.js');
$this->addJs('js/flaty.js');
		}
		
		function index()
		{
			$id=$_SESSION['User']['id'];


	$indxx=	$this->db->getResult("select distinct(indxx_id) as indxx from tbl_assign_index_temp where user_id = '".$id."'",	true);
	//	$this->pr($indxx);
		$array=array();
		
		if(!empty($indxx))
		{
		foreach($indxx as $ind)
		{
	//	$this->pr($ind);
		$indxxticker=	$this->db->getResult("select id,name,code from tbl_indxx_temp where id ='".$ind['indxx']."'",	true);
		
		if(!empty($indxxticker))
		{
		foreach($indxxticker as $ticker)
		{
		$array[]=$ticker;
		}
		}
		
			//	$this->pr($indxxticker);
		}
		}
		
		
		
		$this->smarty->assign("indexarray",$array);
			
		$this->_baseTemplate="inner-template";
	$this->_bodyTemplate="myindextemp/index";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		
		
				$this->smarty->assign("ca_array",$ca_array);
				 $this->show();
	
		
		
		}
		
		
		function viewca(){
			
						if(date('D')!="Mon")
{
$date=date("Y-m-d",strtotime($this->_date)-86400);
}else{
$date=date("Y-m-d",strtotime($this->_date)-(86400*3));
}
			
			$this->_baseTemplate="inner-template";
	$this->_bodyTemplate="myca/index";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
//	$this->addfield();

		$this->smarty->assign('pagetitle','Corporate Actions');
		$this->smarty->assign('bredcrumssubtitle','View Corporate Actions');

		
		$indxxData=	$this->db->getResult("select id,name,code from tbl_indxx_temp where id ='".$_GET['id']."'",	true);
		$this->smarty->assign("indxxData",$indxxData[0]);
	//	$this->pr($indxxData,true);
		
		$array=array();
	

		$ca_array=	$this->db->getResult("SELECT c1.id, c1.action_id, c1.identifier, c1.mnemonic, c1.company_name, c1.ann_date, c1.eff_date, c1.flag, c1.status, caat.user_id as approved
FROM tbl_indxx_ticker_temp c2, tbl_ca c1
LEFT JOIN tbl_ca_admin_approve_temp caat ON c1.action_id = caat.ca_action_id
WHERE c1.identifier = c2.ticker
AND c2.indxx_id =".$_GET['id']." 
",true);
		//$this->pr($ca_array,true);
		/*$indxxticker=	$this->db->getResult("select distinct(ticker) as indxxticker from tbl_indxx_ticker_temp where indxx_id ='".$_GET['id']."'",	true);
		
		if(!empty($indxxticker))
		{
		foreach($indxxticker as $ticker)
		{
		$array[]=$ticker['indxxticker'];
		}
		}
		if(!empty($array))
		{$array=array_unique($array);
		$ca_array=array();
		{
		foreach ($array as $identifier)
		{
$ca=	$this->db->getResult("select id,action_id,identifier,mnemonic,company_name,ann_date,eff_date,action_id,status,flag,(if(action_id in (select ca_action_id from tbl_ca_admin_approve where ca_action_id=cat.action_id),1,0)) as approved from tbl_ca cat where identifier ='".$identifier."' and eff_date>='".$date."'",true);
		//$this->pr($ca); 
		if(!empty($ca))
		{
		foreach($ca as $cas)
		{
		$ca_array[]=$cas;
		}
		}
		
		}
		}}
		
			
		foreach($ca_array as $key=>$value)
		{
			$dividendvalue=$this->db->getResult("select tbl_ca_values.field_value,ca_id from  tbl_ca_values  where tbl_ca_values.ca_id='".$value['id']."' and field_name='CP_DVD_TYP' and field_value!='1000'",true);
			
			if(!empty($dividendvalue['0']['field_value']))
			$ca_array[$key]['notregularcash']=	$dividendvalue['0']['field_value'];	
		}
		
		//$this->pr($ca_array,true);
		*/
	$this->smarty->assign("ca_array",$ca_array);
//	$this->pr($indxxd,true);
	
		//$this->pr($_SESSION);
		 $this->show();
		}
		
		
		
}?>