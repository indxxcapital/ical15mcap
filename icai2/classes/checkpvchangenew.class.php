<?php

class Checkpvchangenew extends Application{

	function __construct()
	{
	//	echo "deepak";
		parent::__construct();
	}
	
	function index()
	{
	echo "SELECT distinct(ticker)  FROM tbl_indxx_ticker WHERE status='1' union SELECT distinct(ticker)  FROM tbl_indxx_ticker_temp WHERE status='1' ";	}
}