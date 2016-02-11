<?php 
include("dbconfig.php");
function qry_insert($table, $data)
    {
        $qry = array();
        if (is_array($qry) === true)
        {
            $qry['query'] = 'INSERT ';
           

            foreach ($data as $key => $value)
            {
                $data[$key] = $key . ' = ' . $value;
            }

            $qry['query'] .= 'INTO ' . $table . ' SET ' . implode(', ', $data);
        }
		//echo implode('', $qry).";";
    mysql_query(implode('', $qry).";");
	return mysql_insert_id();
}



function selectrow($fieldsarray, $table, $datafields=array())
{
    //The required fields can be passed as an array with the field names or as a comma separated value string
    if(is_array($fieldsarray))
    {
        $fields = implode(", ", $fieldsarray);
    }
    else
    {
        $fields = $fieldsarray;
    }
   $whereQuery='';
   if(!empty($fields))
   {
	$whereQuery.=' WHERE  1=1 ';
	//print_r($fields);
	//exit;
	foreach($datafields as $key=>$value)
	{
	$whereQuery.=" AND ".$key." = '".$value."' ";
	
	}
	}
   
   
   
    //performs the query
	//echo "SELECT $fields FROM $table $whereQuery";
	//exit;
	
    $result = mysql_query("SELECT $fields FROM $table $whereQuery") ;
   
    $num_rows = mysql_num_rows($result);
       
    //if query result is empty, returns NULL, otherwise, returns an array containing the selected fields and their values
    if($num_rows == NULL)
    {
        return NULL;
    }
  
	else
    {
        while($row=mysql_fetch_assoc($result))
       {
		  $queryresult[]=$row;
		}
		 return $queryresult;
    }
}

function getCurrency($date)
{
		$currencyarray=array();
		 $query="select tc.*,cp.price,cp.currency,cp.curr_id,cp.date from tbl_currency tc left join tbl_curr_prices cp on tc.id=cp.curr_id where cp.date='$date'";
		$res=mysql_query($query);
		if(mysql_num_rows($res)>0)
		{
			while($row=mysql_fetch_assoc($res))
			{
				//print_r($row);
				if($row['price']=='')
				{
					$row['price']=1;	
				} 
					$currencyarray[$row['id']]=$row['price'];
			}	
			return $currencyarray;
		}
}
function getCurrencyNew($date)
{
		$currencyarray=array();
		 $query="select *  from tbl_currency ";
		$res=mysql_query($query);
		if(mysql_num_rows($res)>0)
		{
			while($row=mysql_fetch_assoc($res))
			{
				//print_r($row);
				$query2='Select * from tbl_curr_prices where curr_id="'.$row['id'].'" and date ="'.$date.'"';
				$res2=mysql_query($query2);
				if(mysql_num_rows($res2)>0)
				{
				$row2=mysql_fetch_assoc($res2);
				$currencyarray[$row['id']]=$row2['price'];
				}
				else
				{
					$currencyarray[$row['id']]=1;
				}
					
			}	
			return $currencyarray;
		}
}


function getPriceforCurrency($index_currency,$ticker_currency,$date){
	$query="SELECT price  FROM `tbl_curr_prices` WHERE `currencyticker` LIKE '".strtoupper($index_currency.$ticker_currency)."%' AND `date` = '$date'";
	$res=mysql_query($query);
if(mysql_num_rows($res)>0)
{
$row=mysql_fetch_assoc($res);
if($row['price'])
return $row['price'];
else
{

return getPriceforCurrency2($ticker_currency,$index_currency,$date);
//echo "Price Not Available for Currency Ticker ".$ticker." of date.".$date."<br>" ;
//exit;
}
}
else
{
return	getPriceforCurrency2($ticker_currency,$index_currency,$date);
	
//echo "Price Not Available for Currency Ticker ".$ticker." of date.".$date."<br>" ;
//exit;
}



}

function getPriceforCurrency2($index_currency,$ticker_currency,$date){
	 $query="SELECT price  FROM `tbl_curr_prices` WHERE `currencyticker` LIKE '".strtoupper($index_currency.$ticker_currency)."%' AND `date` = '$date'";
	$res=mysql_query($query);
if(mysql_num_rows($res)>0)
{
$row=mysql_fetch_assoc($res);
if($row['price'])
{
	//echo 1/$row['price'];
	return 1/$row['price'];
}else
{	mail(($_SESSION['User']['email'] ?$_SESSION['User']['email'].",ical@indxx.com" : 'ical@indxx.com'),"Todays Currency factor not found for ".strtoupper($index_currency.$ticker_currency),"Todays Currency factor not found for ".strtoupper($index_currency.$ticker_currency));

	
	
return	getPriceforCurrency3($index_currency,$ticker_currency,$date);
	
//echo "Price Not Available for Currency Ticker ".$ticker." of date.".$date."<br>" ;
//exit;
}
}
else
{
	mail(($_SESSION['User']['email'] ?$_SESSION['User']['email'].",ical@indxx.com" : 'ical@indxx.com'),"Todays Currency factor not found for ".strtoupper($index_currency.$ticker_currency),"Todays Currency factor not found for ".strtoupper($index_currency.$ticker_currency));

return	getPriceforCurrency3($index_currency,$ticker_currency,$date);
}



}

function getPriceforCurrency3($index_currency,$ticker_currency,$date){
	 $query="SELECT price  FROM `tbl_curr_prices` WHERE `currencyticker` LIKE '".strtoupper($index_currency.$ticker_currency)."%' order by date desc limit 0,1";
	$res=mysql_query($query);
if(mysql_num_rows($res)>0)
{
$row=mysql_fetch_assoc($res);
if($row['price'])
{
	//echo 1/$row['price'];
	return $row['price'];
}else
{
return	getPriceforCurrency4($ticker_currency,$index_currency,$date);
	
//echo "Price Not Available for Currency Ticker ".$ticker." of date.".$date."<br>" ;
//exit;
}
}
else
{
return	getPriceforCurrency4($ticker_currency,$index_currency,$date);
}



}
function getPriceforCurrency4($index_currency,$ticker_currency,$date){

	 $query="SELECT price  FROM `tbl_curr_prices` WHERE `currencyticker` LIKE '".strtoupper($index_currency.$ticker_currency)."%' order by date desc limit 0,1";
	$res=mysql_query($query);
if(mysql_num_rows($res)>0)
{
$row=mysql_fetch_assoc($res);
if($row['price'])
{
	//echo 1/$row['price'];
	return 1/$row['price'];
}else
{
mail(($_SESSION['User']['email'] ?$_SESSION['User']['email'].",ical@indxx.com" : 'ical@indxx.com'),"Currency factor not found for ".strtoupper($index_currency.$ticker_currency),"Currency factor not found for ".strtoupper($index_currency.$ticker_currency));
return 0;	

//echo "Price Not Available for Currency Ticker ".$ticker." of date.".$date."<br>" ;
//exit;
}
}
else
{
mail(($_SESSION['User']['email'] ?$_SESSION['User']['email'].",ical@indxx.com" : 'ical@indxx.com'),"Currency factor not found for ".strtoupper($index_currency.$ticker_currency),"Currency factor not found for ".strtoupper($index_currency.$ticker_currency));
return 0;
}



}

function save_process($task,$date,$status){
mysql_query("INSERT INTO `tbl_system_task_complete` (`id`, `sysdate`, `name`, `status`, `date`) VALUES (NULL, CURRENT_TIMESTAMP, '".$task."', '".$status."', '".$date."');");
}

function saveProcess($type=0)
{
//print_r($_SERVER);

$query="Insert into tbl_system_progress (url,type,path,stime)  values ('".mysql_real_escape_string($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'])."','".$type."','".mysql_real_escape_string($_SERVER['SCRIPT_FILENAME'])."','".date("Y-m-d H:i:s",$_SERVER['REQUEST_TIME'])."')";
mysql_query($query);
}


function webopen($url)
{
echo $url;
	$link="<script type='text/javascript'>
window.open('".$url."');  
</script>";
echo $link;
}

function read_input_files()
{
read_currency_file();
read_libor_file();
read_cash_index_file();
read_price_file();
}
function read_currency_file(){
//echo currencyfactor_file;
if (!file_exists(currencyfactor_file))
	{
		log_error("Currency factor file not available. Exiting closing file process.");
	}
	
	  $query = "LOAD DATA INFILE '" . str_replace("\\", "/", realpath(currencyfactor_file)) .
				"' INTO TABLE tbl_curr_prices
				FIELDS TERMINATED BY '|'
				LINES TERMINATED BY '\n'
				(currencyticker, @x, @y, price, currency, @z)
				SET date = '" . date . "'";

	$res = mysql_query($query);

	if (($err_code = mysql_errno()))
	{
		log_error("Unable to read currency factor file. MYSQL error code " . $err_code . 
					". Exiting closing file process.");
	}
	else if (!($rows = mysql_affected_rows()))
	{
		log_error("No data in currency factor file. Exiting closing file process.");
		}
	else
	{
		log_info("Currency factor file read. Rows inserted = " . $rows);
	}

}

function sava_database_file($file){
//print_r(explode("_",$file));
$array=explode("_",$file);
$query="insert into tbl_backup  values (NULL,'".$array[0]."','".$array[4]."')";

mysql_query($query);
}
function read_libor_file(){
//echo currencyfactor_file;
if (!file_exists(liborrate_file))
	{
		log_error("Libor rate file not available. Exiting closing file process.");
		mail_exit(__FILE__, __LINE__);
	}
	
	$query = "LOAD DATA INFILE '" . str_replace("\\", "/", realpath(liborrate_file)) .
			"' INTO TABLE tbl_libor_prices 
				FIELDS TERMINATED BY '|'
				LINES TERMINATED BY '\n'
				(ticker, @x, @y, price, @z)
				SET date = '" . date . "'";
	$res = mysql_query($query);
	
	if (($err_code = mysql_errno()))
	{
		log_error("Unable to read libor rate file. MYSQL error code " . $err_code .
			". Exiting closing file process.");
		mail_exit(__FILE__, __LINE__);	
	}
	else if (!($rows = mysql_affected_rows()))
	{
		log_error("No data in libor rate file. Exiting closing file process.");
		mail_exit(__FILE__, __LINE__);
	}
	else
	{
		log_info("Libor rate file read. Rows inserted = " . $rows);
	}

}


function read_cash_index_file(){
if (!file_exists(cashindex_file))
	{
		log_error("Cash index file not available. Exiting closing file process.");
		mail_exit(__FILE__, __LINE__);
	}
	
	$query = "LOAD DATA INFILE '" . str_replace("\\", "/", realpath(cashindex_file)) .
	"' INTO TABLE tbl_cash_prices 
				FIELDS TERMINATED BY '|'
				LINES TERMINATED BY '\n'
				(isin, @x, @y, price, @z)
				SET date = '" . date . "'";
	$res = mysql_query($query);
	
	if (($err_code = mysql_errno()))
	{
		log_error("Unable to read cash index file. MYSQL error code " . $err_code .
				". Exiting closing file process.");
		mail_exit(__FILE__, __LINE__);
	}
	else if (!($rows = mysql_affected_rows()))
	{
		log_error("No data in cash index file. Exiting closing file process.");
		mail_exit(__FILE__, __LINE__);
	}
	else
	{
		log_info("Cash index file read. Rows inserted = " . $rows);
	}
	
}


function read_price_file(){
if (!file_exists(price_file))
	{
		log_error("Price file not available. Exiting closing file process.");
		mail_exit(__FILE__, __LINE__);
	}
	
	/* HACK - 0.001 is added since mysql rounds 10.135 to 10.13 but we want 10.14 */
 $query = "LOAD DATA INFILE '" . str_replace("\\", "/", realpath(price_file)) .
			"' INTO TABLE tbl_prices_local_curr 
				FIELDS TERMINATED BY '|'
				LINES TERMINATED BY '\n'
				(ticker, @x, @y, price, curr, @a, isin, @c)
				SET date = '" . date . "'";

				$res = mysql_query($query);
	$query2="update tbl_prices_local_curr set price=round(CAST(price as decimal(10,3)),2) where date ='".date."'";
		$res2 = mysql_query($query2);

	if (($err_code = mysql_errno()))
	{
		log_error("Unable to read price file. MYSQL error code " . $err_code .
					". Exiting closing file process.");
		mail_exit(__FILE__, __LINE__);
	}
	else if (!($rows = mysql_affected_rows()))
	{
		log_error("No data in price file. Exiting closing file process.");
		mail_exit(__FILE__, __LINE__);
	}
	else
	{
		log_info("Price file read. Rows inserted = " . $rows);
	}

}
function delete_old_ca() 
{
	mysql_query ( 'TRUNCATE TABLE tbl_ca ' );
	if (($err_code = mysql_errno()))
	{
		log_error("MYSQL query failed, error code " . $err_code .". Exiting CA process.");
		mail_exit(__FILE__, __LINE__);
	}

	mysql_query ('TRUNCATE TABLE tbl_ca_values_user' );
	if (($err_code = mysql_errno()))
	{
		log_error("MYSQL query failed, error code " . $err_code .". Exiting CA process.");
		mail_exit(__FILE__, __LINE__);
	}
	mysql_query ('insert into `tbl_ca_values_user`  select * from tbl_ca_values' );
	if (($err_code = mysql_errno()))
	{
		log_error("MYSQL query failed, error code " . $err_code .". Exiting CA process.");
		mail_exit(__FILE__, __LINE__);
	}
	
	mysql_query ('TRUNCATE TABLE tbl_ca_values' );
	if (($err_code = mysql_errno()))
	{
		log_error("MYSQL query failed, error code " . $err_code .". Exiting CA process.");
		mail_exit(__FILE__, __LINE__);
	}
	
	
	mysql_query ( 'TRUNCATE TABLE tbl_ca_plain_txt ' );
	if (($err_code = mysql_errno()))
	{
		log_error("MYSQL query failed, error code " . $err_code .". Exiting CA process.");
		mail_exit(__FILE__, __LINE__);
	}
	
	return true;
}
function read_ca_file(){
log_info("Reading CA file");
	
	if (!file_exists(ca_file))
	{
		log_error("CA file not available. Exiting CA process.");
		mail_exit(__FILE__, __LINE__);
	}
	
	//delete_plain_ca();
	
	$query = "LOAD DATA INFILE '" . str_replace("\\", "/", realpath(ca_file)) .
			"' INTO TABLE tbl_ca_plain_txt LINES TERMINATED BY '\n' IGNORE 2 LINES (value)";
	$res = mysql_query($query);

	if (($err_code = mysql_errno()))
	{
		log_error("MYSQL error, code " . $err_code . ". Exiting CA process.");
		mail_exit(__FILE__, __LINE__);	
	}
	else if (!($rows = mysql_affected_rows()))
	{
		log_error("No data in CA file. Exiting CA file process.");
		mail_exit(__FILE__, __LINE__);
	}
	else
	{
		log_info("CA file read. Rows inserted = " . $rows);
	}

	//saveProcess(2);
	//mysql_close();
	log_info("CA file reading finished ");
	process_ca();
	
}

function process_ca(){
log_info("Processing CA file and data");
$query = "INSERT INTO tbl_ca_values (ca_id, ca_action_id, field_name, field_value) VALUES";
	$ca_row=0;
	$res = mysql_query("Select * from tbl_ca_plain_txt");
	if (($err_code = mysql_errno()))
	{
		log_error("Unable to read CA plain table. MYSQL error code " . $err_code .
					". Exiting CA process.");
		mail_exit(__FILE__, __LINE__);
	}
	
	
	
	while(false != ($row = mysql_fetch_assoc($res)))
	{
		
		
		if (strstr(json_encode($row['value']), "CP_DELETE_REASON")) 
		{
			//echo "CP_DELETE_REASON<br>";
			
			log_info("Corporate action delisted from bloomberg");
		}
		else{
		$security = explode('|', mysql_real_escape_string($row['value']));
		//print_r($security);
		/* Ignore securities with no CAs */
		if (count($security) > 5) 
		{	
			log_info("Reading CA for security = " .$security[0]);
			
			
				
			//log_info("Reading CA for security = " .$security[0]);
			//print_r($security); exit();	
							
			$checkArray = array();				
			$checkTickerArray = array();

			$data ['status'] 			= "'1'";

			$data ['identifier'] 		= "'"  .$security ['0']. "'";
			$checkArray ['identifier'] 	= $security ['0'];
				
			$data ['company_id'] 		= "'"  .$security ['1']. "'";
			$data ['security_id'] 		= "'"  .$security ['2']. "'";		
			$data ['rcode'] 			= "'"  .$security ['3']. "'";

			$data ['action_id'] 		= "'"  .$security ['4']. "'";
			$checkArray ['action_id'] 	= $security ['4'];

			$data ['mnemonic'] 			= "'"  .$security ['5']. "'";
			$checkArray ['mnemonic'] 	= $security ['5'];
			
			$ca_field_id = selectrow (array('id'), 'tbl_ca_subcategory', array('code' => $security ['5']));
				
			$data ['field_id'] 			= "'"  .$ca_field_id ['0'] ['id']. "'";
			$data ['flag'] 		= "'"  .$security ['6']. "'";			
			$data ['company_name'] 		= "'"  .$security ['7']. "'";			
			$data ['secid_type'] 		= "'"  .$security ['8']. "'";
			$data ['secid'] 			= "'"  .$security ['9']. "'";
			$data ['currency'] 			= "'"  .$security ['10']. "'";				
			$data ['market_sector_desc'] 	= "'"  .$security ['11']. "'";
			$data ['bloomberg_unique_id'] 	= "'"  .$security ['12']. "'";
				
			if ($security ['13'] == '')
				$data ['ann_date'] = '0000-00-00';
			else
				$data ['ann_date'] = "'" . date ( "Y-m-d", strtotime ( $security ['13'] ) ) . "'";
				
			$checkArray ['ann_date'] = str_replace ( "'", "", $data ['ann_date'] );
				
			$security ['14'] = str_replace ( 'N.A.', "", $security ['14'] );
			if ($security ['14'] == '')
				$data ['eff_date'] = '0000-00-00';
			else
				$data ['eff_date'] = "'" . date ( "Y-m-d", strtotime ( $security ['14'] ) ) . "'";
				
			$checkArray ['eff_date'] = str_replace ( "'", "", $data ['eff_date'] );
				
			if ($security ['15'] == '')
				$data ['amd_date'] = '0000-00-00';
			else
				$data ['amd_date'] = "'" . date ( "Y-m-d", strtotime ( $security ['15'] ) ) . "'";
					
			$data ['bloomberg_global_id'] 	= "'"  .$security ['16']. "'";
			$data ['bl_global_company_id'] 	= "'"  .$security ['17']. "'";
			$data ['bl_security_id_num'] 	= "'"  .$security ['18']. "'";
			$data ['feed_source'] 			= "'"  .$security ['19']. "'";
			$data ['nfields'] 				= "'"  .$security ['20']. "'";
				
			if($checkArray['mnemonic'] == '' || $checkArray ['ann_date'] == '0000-00-00' 
					|| $checkArray ['eff_date'] == '0000-00-00') 
			{
				$msg = "Mnemonic/Ann_date/Eff_date missing in security = " .$data ['identifier']. 
						", bloomberg_unique_id = " .$data ['bloomberg_unique_id']. ". Ignoring this CA.\n";
					log_info($msg);	
			} 
			else 
			{
				 $ca_id = qry_insert ( 'tbl_ca', $data );
				//exit;
				$num_fields = 2 * $security ['20'];
	
				for($k = 1; $k < $num_fields + 1; $k = $k + 2) 
				{
					$name = $security [$k + 20];
					$value = $security [$k + 21];

					if ($value != 'N.A.' && trim($value) != '' && $value != ' ')
					{		
						/*
						$field_id = selectrow ( array ('id'), 'tbl_ca_action_fields', array ('field_name' => $security [$k + 20]) );
						$data2 ['field_id'] = "'" . $field_id ['0'] ['id'] . "'";

						$data2[$ca_row] ['ca_id'] = $ca_id;//"'" . $ca_id . "'";
						$data2[$ca_row] ['ca_action_id'] = $data ['action_id'];
						$data2[$ca_row] ['field_name'] = $name;//"'" . $name. "'";
						$data2[$ca_row] ['field_value'] = $value;//"'"  .$value. "'";
					
						qry_insert ( 'tbl_ca_values', $data2 );
						*/
						
						if ($ca_row)
							$query .= ",";
							
						$query .= " ('" .$ca_id. "', " .$data ['action_id']. ", '" .$name. "', '"  .$value. "')";						
						$ca_row++;
						
					}
				}	
			}
			unset($checkArray);
			unset($checkTickerArray);
		
		}
		}
	}
	
	
	
		mysql_free_result($res);
	if($ca_row){
	
	mysql_query($query);
	
		if (($err_code = mysql_errno()))
		{
			log_error("MYSQL query failed, error code " . $err_code . ". Exiting CA process.");
			mail_exit(__FILE__, __LINE__);
		}
	
	}	
		
	log_info("Processing CA file done");

	}


function getPriceforCurrency5($index_currency,$ticker_currency,$date){
	mail(($_SESSION['User']['email'] ?$_SESSION['User']['email'].",ical@indxx.com" : 'ical@indxx.com'),"Currency factor not found for ".strtoupper($index_currency.$ticker_currency),"Currency factor not found for ".strtoupper($index_currency.$ticker_currency)." for date ".$date." using Old Price");
	
	$query="SELECT currencyticker,price,currency  FROM `tbl_curr_prices` WHERE `currencyticker` LIKE '".strtoupper($index_currency.$ticker_currency)."%' order by date desc limit 0,1";
	
	$res=mysql_query($query);
if(mysql_num_rows($res)>0)
{
$row=mysql_fetch_assoc($res);
if($row['price'])
{
	//echo 1/$row['price'];
	return $row;
}else
{
return	getPriceforCurrency6($ticker_currency,$index_currency,$date);
	
//echo "Price Not Available for Currency Ticker ".$ticker." of date.".$date."<br>" ;
//exit;
}
}
else
{
return	getPriceforCurrency6($ticker_currency,$index_currency,$date);
}



}
function getPriceforCurrency6($index_currency,$ticker_currency,$date){

	 $query="SELECT currencyticker,price,currency  FROM `tbl_curr_prices` WHERE `currencyticker` LIKE '".strtoupper($index_currency.$ticker_currency)."%' order by date desc limit 0,1";
	$res=mysql_query($query);
if(mysql_num_rows($res)>0)
{
$row=mysql_fetch_assoc($res);
if($row['price'])
{
	//echo 1/$row['price'];
 $row['price']= 1/$row['price'];
 return $row;
}else
{
mail(($_SESSION['User']['email'] ?$_SESSION['User']['email'].",ical@indxx.com" : 'ical@indxx.com'),"Currency factor not found for ".strtoupper($index_currency.$ticker_currency),"Currency factor not found for ".strtoupper($index_currency.$ticker_currency));
return NULL;	

//echo "Price Not Available for Currency Ticker ".$ticker." of date.".$date."<br>" ;
//exit;
}
}
else
{
mail(($_SESSION['User']['email'] ?$_SESSION['User']['email'].",ical@indxx.com" : 'ical@indxx.com'),"Currency factor not found for ".strtoupper($index_currency.$ticker_currency),"Currency factor not found for ".strtoupper($index_currency.$ticker_currency));
return NULL;
}



}


?>