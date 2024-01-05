<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// this is to retreieve all tokens from database ---FOR TIME BEING ONLY TOKENS ARE RETRIEVED

 function __construct() {        // this is a constructor, although not reqd, we put it
    }
    
     require_once dirname(__FILE__) . '/db_connect.php';
        $db= new dbconnect();
        $conn=$db->connect();
        $response= array();
        
        if(isset($_POST["user_id"]))
        {
            $user_id= $_POST["user_id"];
        }
   
        
       $stmt= $conn->prepare("SELECT name,email,fcm_reg_id FROM users WHERE user_id=?");
       $stmt->bind_param("i",$user_id);
       $result= $stmt->execute();
       
       if($result) // if many users exist
       {
            $stmt->store_result();
            $numrows=$stmt->num_rows;
            if($numrows>0)
             {
             $response['data']=array();
           while($result= mysqli_fetch_array($stmt))
           {
           $data=array();
            $data['name']=$result['name'];
            $data['email']=$result['email'];
            $data['fcm_reg_ic']=$result['fcm_reg_id'];
             array_push($response["data"], $data);
           }
            
            // success
            $response['error'] = false;
           $response['message']="all records showm";
 
           
 
            // echoing JSON response
            echo json_encode($response); 
            echo "user table has multiple cols";
    
            } 
           
       }
 else {
           $response['error']=TRUE;
           $response['message']="no records found";
            echo json_encode($response); 
     
}


?>