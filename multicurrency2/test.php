<?php 
include("function.php");
mysql_query("LOAD DATA INFILE 'C:/Inetpub/vhosts/ip-198-12-158-159.secureserver.net/httpdocs/ical1.4.1/files/input/curr1_16g.csv.20150803' INTO TABLE tbl_curr_prices
				FIELDS TERMINATED BY '|'
				LINES TERMINATED BY '
'
				(currencyticker, @x, @y, price, currency, @z)
				SET date = '2015-08-03'");
				echo mysql_errno();
?>