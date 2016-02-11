<?php 
/*echo  date_default_timezone_get() ;
echo date("Y-m-d H:i:s");*/
$ip="192.169.250.22";
$user="syntaxuser";
$password="Indxx_2015";
$date=date("Ymd",strtotime(date("Y-m-d"))-86400);
//$date="20151104";
$files=array("ca_sl.csv.".$date);
foreach ($files as $k=> $file){
$fileContents = file_get_contents('ftp://'.$user.':'.$password.'@'.$ip.'/bloomberg-input2/'.$file);
$str= get_string_between($fileContents,"START-OF-DATA","END-OF-DATA");
$array=explode("\n",$str);
unset($array[0]);
//print_r($array);
//exit;
$str=implode("\n",$array);
$filename="";
if($k==0)
$filename="ca_16g.csv.".$date;
$myfile = fopen("../files/input/".$filename, "w") or die("Unable to open file!");

fwrite($myfile, $str);
fclose($myfile);
}





function get_string_between($string, $start, $end){
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}


?>

