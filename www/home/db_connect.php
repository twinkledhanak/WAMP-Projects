<?php

class db_connect
{
	private $conn;
	
	function _construct()
	{
		
	}
	
	
	function connect()
	{
		//include_once dirname(_FILE_) .'\config.php';
		require '/config.php';
		
		$this->conn= new mysqli($hostname,$username,$password,$dbname);
		if(mysqli_connect_errno())
		{
			echo 'not connected to db';
		}
		else
		{
			echo 'connected to db';
		}
		
		return $this->conn;
	}
	
}


?>