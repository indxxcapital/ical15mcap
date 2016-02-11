<?php
$ip="192.169.250.22";
$user="syntaxuser";
$password="Indxx_2015";
 //$date=date("Ymd",strtotime(date("Y-m-d"))-86400);
 $date=date("Ymd",strtotime(date("Y-m-d")));
//$date="20151022";
$files=array("multicurr_sl.csv.".$date,"libr_sl.csv.".$date,"curr1_sl.csv.".$date,"cashindex_sl.csv.".$date);
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
$filename="multicurr_16g.csv.".$date;
if($k==1)
$filename="libr_16g.csv.".$date;
if($k==2)
$filename="curr1_16g.csv.".$date;
if($k==3)
$filename="cashindex_16g.csv.".$date;

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

