<?php

require '/dbconnect.php';
$db= new dbconnect();
$conn= $db->connect();
	$stmt= $conn->prepare("SELECT name,sapid,department FROM user");
	$stmt->execute();
	$result=$stmt->fetch(); 
	 $users= array();
	 echo 'Result: '.$result;
	 if($result)
	 {
	 
	
		 if($result  > 0)
		 {
			 $data= mysqli_query($stmt,MYSQLI_USE_RESULT);
			 while($row = $data->fetch_assoc())
			 {
				 $name= $row["name"];
				 $sapid= $row["sapid"];
				 $department= $row["department"];
				 
				 echo 'Name: '.$name.'sapid: ',$sapid.'department: '.$department ;
				
				 
			 }
		 }
	 header('Content-Type: application/json');
	 echo json_encode(array("Students"=>$users));
	 }


?>