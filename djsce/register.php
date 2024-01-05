<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// main aim to create this file is to insert details inside database..
 function __construct() {        // this is a constructor, although not reqd, we put it
    }
 
    if(isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["fcm_reg_id"]))
    {
        
        $response= array();
        require_once dirname(__FILE__) . '/db_connect.php';
        $db= new dbconnect();
        $conn=$db->connect();
        
        $name=$_POST["name"];
        $email=$_POST["email"];
        $fcm_reg_id= $_POST["fcm_reg_id"]; // fetching the id
        
        
        $stmt= $conn->prepare("INSERT INTO users(name,email,fcm_reg_id) VALUES(?,?,?) ");
        $stmt->bind_params("sss",$name,$email,$fcm_reg_id);
        $result= $stmt->execute();
        if($result)
        {
           $response['error']=FALSE;
           $response['message']="inserted into database";
            echo json_encode($response);
            // WE ARE ENCODING THE RESPONSE ARRAY EVEN WHILE PUTTING DATA INTO DATABASE
        }
        
        else
        {
           $response['error']=TRUE;
           $response['message']="not inserted into database";
            echo json_encode($response);
        }
        
    }

?>