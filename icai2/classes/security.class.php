<?php

class Security extends Application{

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
		
	function add_currency()
	{
		
		$this->_baseTemplate="inner-template";
		$this->_bodyTemplate="security/addCurrency";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		$this->smarty->assign('pagetitle','Security Prices');
		$this->smarty->assign('bredcrumssubtitle','Add Currency Prices');
		
		if(isset($_POST['submit'])){
		
			$olddata=$this->db->getResult("select * from tbl_curr_prices where 1=1");
			//$this->pr($olddata);
			$a=$_POST['ticker'];
			$b=$_POST['curr'];
			$c=$_POST['price'];
			$d=$_POST['dateStart'];
			
			$data1=$this->db->getResult("select currencyticker from tbl_curr_prices where currencyticker ='".mysql_real_escape_string($a)."'");
			//$this->pr($data1,true);
			
			if(empty($data1)){
				$this->db->query("INSERT into tbl_curr_prices set currencyticker='".mysql_real_escape_string($_POST['ticker'])."',currency='".mysql_real_escape_string($_POST['curr'])."',price='".mysql_real_escape_string($_POST['price'])."',date='".$_POST['dateStart']."'");
			
				$this->Redirect("index.php?module=security&event=add_currency","Currency added successfully!!!","success");	
			}
			else{
				$data2=$this->db->getResult("select currencyticker from tbl_curr_prices where currencyticker ='".mysql_real_escape_string($a)."' and date='".mysql_real_escape_string($d)."'");
				//$this->pr($data2,true);
				if(empty($data2)){
				$this->db->query("INSERT into tbl_curr_prices set currencyticker='".mysql_real_escape_string($_POST['ticker'])."',currency='".mysql_real_escape_string($_POST['curr'])."',price='".mysql_real_escape_string($_POST['price'])."',date='".$_POST['dateStart']."'");
				$this->Redirect("index.php?module=security&event=add_currency","Currency added successfully!!!","success");	
				
				}
				else{
				$this->db->query("UPDATE tbl_curr_prices set currencyticker='".mysql_real_escape_string($_POST['ticker'])."',currency='".mysql_real_escape_string($_POST['curr'])."',price='".mysql_real_escape_string($_POST['price'])."',date='".$_POST['dateStart']."' where date='".$_POST['dateStart']."'" );
				$this->Redirect("index.php?module=security&event=add_currency","Old Data Removed <br/> Currency Updated!!!");	
				//$this->Redirect("index.php?module=security&event=add_currency",$a." Currency Already in Database , Please Check!","error");	
				}
				
			}
			exit;
		}		
		$this->addfieldsCurrency();
		$this->show();
		}
	
	function add_security()
	{
	
		
		$this->_baseTemplate="inner-template";
		$this->_bodyTemplate="security/addSecurity";
		$this->_title=$this->siteconfig->site_title;
		$this->_meta_description=$this->siteconfig->default_meta_description;
		$this->_meta_keywords=$this->siteconfig->default_meta_keyword;
		$this->smarty->assign('pagetitle','Security Prices');
		$this->smarty->assign('bredcrumssubtitle','Add Security Prices');
		
		if(isset($_POST['submit'])){
			$olddata=$this->db->getResult("select * from tbl_prices_local_curr where 1=1");
			//$this->pr($olddata);
			$a=$_POST['ticker'];
			$b=$_POST['isin'];
			$c=$_POST['price'];
			$d=$_POST['curr'];
			$e=$_POST['date'];
			//echo mysql_real_escape_string($a);
			
			$data1=$this->db->getResult("select ticker from tbl_prices_local_curr where ticker ='".mysql_real_escape_string($a)."'");			
			if(empty($data1)){
				echo "No Matched Ticker in Database<br/>";
				$this->db->query("INSERT into tbl_prices_local_curr set ticker='".mysql_real_escape_string($_POST['ticker'])."',isin='".mysql_real_escape_string($_POST['isin'])."',price='".mysql_real_escape_string($_POST['price'])."',curr='".mysql_real_escape_string($_POST['curr'])."',date='".$_POST['date']."'");
				
				$this->Redirect("index.php?module=security&event=add_security","Security added successfully!!!","success");	
			}
			else{
				echo "Matching Ticker found in Database<br/>";
				$data2=$this->db->getResult("select ticker from tbl_prices_local_curr where ticker ='".mysql_real_escape_string($a)."' and date='".mysql_real_escape_string($e)."'");
				//$this->pr($data2,true);
				if(empty($data2)){
				$this->db->query("INSERT into tbl_prices_local_curr set ticker='".mysql_real_escape_string($_POST['ticker'])."',isin='".mysql_real_escape_string($_POST['isin'])."',price='".mysql_real_escape_string($_POST['price'])."',curr='".mysql_real_escape_string($_POST['curr'])."',date='".$_POST['date']."'");
				$this->Redirect("index.php?module=security&event=add_security","Security added successfully!!!","success");	
				
				}
				else{
				$this->db->query("UPDATE tbl_prices_local_curr set ticker='".mysql_real_escape_string($_POST['ticker'])."',isin ='".mysql_real_escape_string($_POST['isin'])."',price='".mysql_real_escape_string($_POST['price'])."',curr='".mysql_real_escape_string($_POST['curr'])."',date='".$_POST['date']."' where date='".$_POST['date']."'" );
				$this->Redirect("index.php?module=security&event=add_security","Old Data Removed <br/> Security Updated!!!");	
				//$this->Redirect("index.php?module=security&event=add_currency",$a." Currency Already in Database , Please Check!","error");	
				}
				
			}
			exit;
		}		
		$this->addfieldsSecurity();
		$this->show();
		
}
	
	private function addfieldsSecurity()
	{	
	   $this->validData[]=array("feild_label" =>"Ticker",
	   								"feild_code" =>"ticker",
								 "feild_type" =>"text",
								 "is_required" =>"1",
								
								 );
		 $this->validData[]=array("feild_label" =>"Isin",
		 							"feild_code" =>"isin",
								 "feild_type" =>"text",
								 "is_required" =>"1",
								
								 );
		
	 $this->validData[]=array("feild_label" =>"Price",
	 							"feild_code" =>"price",
								 "feild_type" =>"text",
								 "is_required" =>"1",
								 //"feild_tpl" =>"selectsearch",
								  "model"=>$this->getReturnTypes(),
								 );							 
	 
								 						 
	 $this->validData[]=array(	"feild_label"=>"Currency",
	 							"feild_code" =>"curr",
								 "feild_type" =>"text",
								 "is_required" =>"1",
								
								 );
								 	 
	 $this->validData[]=array(	"feild_label"=>"Date",
	 							"feild_code" =>"date",
								 "feild_type" =>"date",
								 "is_required" =>"1",
								);
									
	
	$this->getValidFeilds();
	}
	
	
	private function addfieldsCurrency()
	{
	$this->validData[]=array("feild_label" =>"Currency Ticker",
	   								"feild_code" =>"ticker",
								 "feild_type" =>"text",
								 "is_required" =>"1",
								
								 );
		  
	 $this->validData[]=array(	"feild_label"=>"Currency",
	 							"feild_code" =>"curr",
								 "feild_type" =>"text",
								 "is_required" =>"1",
								
								 );		
	 $this->validData[]=array("feild_label" =>"Price",
	 							"feild_code" =>"price",
								 "feild_type" =>"text",
								 "is_required" =>"1",
								 //"feild_tpl" =>"selectsearch",
								  "model"=>$this->getReturnTypes(),
								 );							 
	
								 	 
	 $this->validData[]=array(	"feild_label"=>"Date",
	 							"feild_code" =>"dateStart",
								 "feild_type" =>"date",
								 "is_required" =>"1",
								);
									
	
	$this->getValidFeilds();}
	
} // class ends here

?>