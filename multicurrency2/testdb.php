<?php
mysql_connect("localhost","admin_icai14","Indxxb3930@db");
if(mysql_select_db("admin_icai141backup"))
echo "DB connected";
else
echo "not connected";
?>