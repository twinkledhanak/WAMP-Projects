<?php

require '/dbconnect.php';
$db= new dbconnect();
$conn= $db->connect();

if($_SERVER["REQUEST_METHOD"]=="POST")
{
	if(isset($_POST["name"]) &&  isset($_POST["sapid"]) && isset($_POST["department"]) )
	{
		$name=$_POST["name"];
		$sapid=$_POST["sapid"];
		$department=$_POST["department"];
	}
	
	else
	{
		echo 'Insert all values please';
	}
	
	
	$stmt=$conn->prepare("INSERT INTO USER(name,sapid,department) VALUES(?,?,?)");
	$stmt->bind_param("sis",$name,$sapid,$department);
	$result=$stmt->execute();
	
	$response= array();
	
	if($result)
	{
		$response["error"]=false;
		$response["message"]="Inserted into database successfully";
		echo json_encode($response);
	}
	else
	{
		$response["error"]=true;
		$response["message"]="Not Inserted into database";
		echo json_encode($response);
	}
	
	
	
	
}



?>