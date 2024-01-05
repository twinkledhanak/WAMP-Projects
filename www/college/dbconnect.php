<?php
class dbconnect
{
private $conn;

function _construct()
{
	
}

function connect()
{
	require '/config.php';
	
	$this->conn= new mysqli($hostname,$username,$password,$dbname);
	
	if(mysqli_connect_errno())
	{
		echo 'Not connected to database';
	}
	else
	{
		echo 'Connected to database';
	}
	
	return $this->conn;
}

}

?>