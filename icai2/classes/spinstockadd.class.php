<?php 

class Spinstockadd extends Application{
		
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
		function index(){
		
		
		$this->_baseTemplate="inner-template";
		$this->_bodyTemplate="spinstockadd/index";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		
	$data=	$this->db->getResult("Select ssa.dbApprove,ssa.action_id,ssa.id,tbl_ca.identifier,tbl_ca.company_name,tbl_ca.mnemonic,tbl_ca.eff_date from tbl_spin_stock_add ssa 
	left join tbl_ca on ssa.action_id=tbl_ca.action_id 
	
	 ",true);
		//$this->pr($data,true);
		
				$this->smarty->assign("ca_array",$data);
				 $this->show();

		
		}
		function delete(){
		if($_GET['id'])
		{
		
		$this->db->query("delete from tbl_spin_stock_add where action_id='".$_GET['id']."'");
			$this->db->query("delete from tbl_spin_stock_add_securities where req_id='".$_GET['id']."'");
				$this->db->query("delete from tbl_spin_stock_add_securities_temp where req_id='".$_GET['id']."'");
				$this->Redirect("index.php?module=spinstockadd","Record Deleted Successfully","success");		
		}
		
		}
		
		
		function edit(){
		if($_GET['id'])
		{
		

		}	

$cadata=$this->db->getResult("select tbl_ca.* from tbl_ca where action_id = '".$_GET['id']."' ",true);


		//$this->pr($cadata,true);
		$this->smarty->assign("viewdata",$cadata);
		
		
//$this->pr($indexdata2,true);
$ticker=$cadata[0]['identifier'];
$this->addfieldforrunning(10,$cadata[0]['action_id']);
$tickerdata=$this->db->getResult("select tbl_indxx_ticker.indxx_id from tbl_indxx_ticker where ticker = '".$ticker."' ",true);
//$this->pr($tickerdata,true);	
	
	$indxes=array();
	if(!empty($tickerdata))
	{
	foreach($tickerdata as $data)
	{
	$indxes[]=$data['indxx_id'];
	}
	}
	if(!empty($indxes)){
	$ids=implode(',',$indxes);
	
		//	echo "select tbl_indxx.name,tbl_indxx.code from tbl_indxx where id = '".$ticker['indxx_id']."' ";
	$indxxd=$this->db->getResult("select tbl_indxx.name,tbl_indxx.code,tbl_indxx.id as indxx_id from tbl_indxx where id in (".$ids.")",true);
	

$selected_index=$this->db->getResult("select indxx_id  from tbl_spin_stock_add_securities where req_id='".$_GET['id']."'");
$selectedarray=array();
if(!empty($selected_index))
{
	foreach($selected_index as $selected)
	{
		$selectedarray[]=$selected['indxx_id'];
	}
}

		$this->smarty->assign("selectlive",$selectedarray);
//$this->pr($indxxd,true);
$this->smarty->assign("indxxd",$indxxd);
	}


$tickertempdata=$this->db->getResult("select tbl_indxx_ticker_temp.indxx_id from tbl_indxx_ticker_temp where ticker = '".$ticker."' ",true);
//$this->pr($tickertempdata,true);	
	
	$indxes=array();
	if(!empty($tickertempdata))
	{
	foreach($tickertempdata as $data)
	{
	$indxes[]=$data['indxx_id'];
	}
	}
	if(!empty($indxes)){
	$idsu=implode(',',$indxes);
	
		//	echo "select tbl_indxx.name,tbl_indxx.code from tbl_indxx where id = '".$ticker['indxx_id']."' ";
	
	
	$indxxu=$this->db->getResult("select tbl_indxx_temp.name,tbl_indxx_temp.code,tbl_indxx_temp.id as indxx_id from tbl_indxx_temp where id in (".$idsu.")",true);
	
	}

	
		
//$this->pr($clientdata,true);
$this->smarty->assign("indxxu",$indxxu);



		$this->_baseTemplate="inner-template";
		$this->_bodyTemplate="spinstockadd/addStockforSpin";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		

				 $this->show();
		
		}
		
		
		
		
	private function addfieldforrunning($count,$action_id)
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
								 
		$this->validData[]=array("feild_label" =>"Share",
		 							"feild_code" =>"share[".$i.']',
								 "feild_type" =>"hidden",
								 "is_required" =>"",
								
								);
	//  "feild_tpl" =>"place_text2",
	
	 $this->validData[]=array("feild_label" =>"Ca Action ",
	 							"feild_code" =>"ca_action_id[".$i.']',
								 "feild_type" =>"hidden",
								 "is_required" =>"",
								   "feild_tpl" =>"hidden2",
								 'value'=>$action_id
								 );
	
	 $this->validData[]=array(	"feild_label"=>"Ticker Currency",
	 							"feild_code" =>"curr[".$i.']',
								 "feild_type" =>"text",
								  "feild_tpl" =>"place_text2",
								 "is_required" =>"",
								
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
		function view(){
		$data=	$this->db->getResult("Select ssa.dbApprove,ssa.action_id,ssa.id,tbl_ca.identifier,tbl_ca.company_name,tbl_ca.mnemonic,tbl_ca.eff_date from tbl_spin_stock_add ssa 
	left join tbl_ca on ssa.action_id=tbl_ca.action_id 
	where ssa.id=".$_GET['id']."
	 ",false,1);
	 if(!empty($data))
	 {
		$data2=	$this->db->getResult("Select ssas.*,tbl_indxx.name as indxx_name,tbl_indxx.code as indxx_code from tbl_spin_stock_add_securities ssas 
	left join tbl_indxx on tbl_indxx.id=ssas.indxx_id
	where ssas.req_id=".$data['action_id']."
	 ",true);
	 
	
	 $data3=	$this->db->getResult("Select ssas.*,tbl_indxx_temp.name as indxx_name,tbl_indxx_temp.code as indxx_code from tbl_spin_stock_add_securities_temp ssas 
	left join tbl_indxx_temp on tbl_indxx_temp.id=ssas.indxx_id
	where ssas.req_id=".$data['action_id']."
	 ",true);
	 
	//$this->pr($data2,true);
	}
	 $this->smarty->assign("ca_data",$data);
			
$this->smarty->assign("ca_values",$data2);
$this->smarty->assign("ca_valuesU",$data3);

		$this->_baseTemplate="inner-template";
		$this->_bodyTemplate="spinstockadd/view";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		

				 $this->show();

	 
	 
	 
	 
		}
		
		
		
		function approve()
		{
			
			
			if($_GET['id'])
			{
			$this->db->query('update tbl_spin_stock_add set dbApprove="1"  where id="'.$_GET['id'].'"');
				$data=	$this->db->getResult("Select ssa.user_id,tbl_ca_user.email,ssa.action_id,ssa.id,tbl_ca.identifier,tbl_ca.company_name,tbl_ca.mnemonic,tbl_ca.eff_date from tbl_spin_stock_add ssa 
	left join tbl_ca on ssa.action_id=tbl_ca.action_id 
	left join tbl_ca_user on tbl_ca_user.id=ssa.user_id
	where ssa.id=".$_GET['id']."
	 ",false,1);
			
			//$this->pr($data,true);
			
			$to=$data['email'];		
			$to="dbajpai@indxx.com";
		$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'From: Indexing <indexing@indxx.com>' . "\r\n"."CC: indexing@indxx.com". "\r\n";
$body='Hi <br>';
$body.='Corporate Action '.$data['identifier'].'('.$data['mnemonic'].') Stock addition has been approved by DB user , <br> Please visit  '.$this->siteconfig->base_url.'index.php?module=myca&event=view&id='.$_GET['id'].' to check it.<br>Thanks ';
mail($to,"Spin-off Stock Addition Approval ",$body,$headers);
			
			
			$this->Redirect("index.php?module=spinstockadd","","");		
			
			
			
			
			}
		
		}
}