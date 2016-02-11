<?php

class Openingtest extends Application{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function index()
	{
		
	$this->opening->calcindxxopening();
	$this->opening->calcindxxopeningtemp();
	$this->opening->compositopen();
	$this->opening->notifyforca();
	$this->opening->calcftpopen();

	}

}
	