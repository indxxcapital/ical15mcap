<?php

class Backup extends Application{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function index()
{
	exec("backup.bat");
}
}