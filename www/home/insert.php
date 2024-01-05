<?php



require '/db_connect.php';

if(isset($name))
{
	$username=$_POST['name']; /* here note, we write name and not $name , inside square brackets  */
}


 

$response= array();
$db= new db_connect();
$conn= $db->connect();

$stmt= $conn->prepare("INSERT INTO user(name) VALUES(?) ");
$stmt->bind_param("s",$username);

$result= $stmt->execute();

$stmt->close();
if($result)
{
	$response['success']=true;
	$response['message']="inserted";
	
	echo json_encode($response);
}

else
{$response['success']=false;
	$response['message']=" not inserted";
	echo json_encode($response);
	
}


	



?>