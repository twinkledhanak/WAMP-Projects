<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/// basic aim for creating this file is to send all kinds of messages via php backend to firebase api via curl requests
//in prev files we have connected to database and also operated crud methods on them
// here we make use of curl requests

//we cover basic three categories of msging here
// 1. sending message individually to every device by its id ()()()()()--although doubtful ?????

//2. sending messaging to topic by topic name , ie, computers, it, elex, extc

//3. sending push messages to multiple users by therir firebase ids

class firebase
{
    //functions for sending messages
    // $message is anallogus to   question/ answer/ notice. it is just a variable name that will receive the values that are passed
    
    
    // FOR THIS CODE, FLOW IS AS FOLLOWS:
    // FIRST CALL IS MADE TO METHOS sendmessagetotopic AND THEN THIS CALLS sendpushnotification
    public function sendmessagetotopic($to,$message) // sends messages via topic
    {
        // HERE WE ARE TAKING INPUT FROM USER ONLY, FOR SENDER ID AND MESSAGE
        // THIS INPUT IS GIVEN VIA A CONSOLE, WHICH HAS ONCLICK MEHOD AS sendmessagetotopic
        // WE ARE NOT HARDCORING THE MESSAGE OR THE SENDER ID HERE
        
        //this is used to send msg to particular chat room or (topic) eg.. computers, elex, extc
        // in other words, to send msg to users subscribed to a particular topic
        
        // WE ARE CONSIDERING GLOBAL MESSAGES AS PART OF TOPIC GLOBAL, SO NO NEED FOR A METHOD sendglobalmessages
        $fields= array(
            'to' => '/topics/' . $to, // ?????????????????????????????????????????????????????????????????????????????
            'data' => $message,
            );
        return $this->sendpushnotifications($fields);
    }
    
    
    
   // we are requiring fields array, which is still to be found
    
    // function for making curl request TO FIREBASE SERVERS
    
    
    
    private function sendpushnotifications($fields)// we are passing $fields array because we need
    {
       // initially i had used require '..path..'; , but it was giving error that constant is already defined bcoz it was again including same constant twice
        // so we need to specify that require_once, so it will nt include file more than once and not give error saying constannt is already defined
       require_once  'C:\wamp64\www\fcm_chat\include\config.php';  
       
      //  require_once _DIR_. '/config.php';
        
         // for making curl request, we need 3 types of paramteres
         // 1. object of curl obtained using curl_init method
         // 2. soe constant of curl methods, ie, HTTP_HEADERS n all
         // 3. link or url to which we must direct our page
         
         $ch=  curl_init();
         $url= 'https://fcm.googleapis.com/fcm/send'; // 
         
       //  $url='https://firebase.google.com/docs/cloud-messaging/http-server-ref'; // not sure about this url
         
         ///// now we  make a header array for JASON PARSING
         
         $headers = array('Authorization: key=' . 'FIREBASE_API_KEY',  'Content-Type: application/json');
         // this statement is used to give the firebase key to the server and for which json format is used
         // so we specify format or mime type for json which is application/json
         
         // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url); // second and third paramter are related that whatever type of constant mentioned in 2nd param
        // is set in third paramter
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
         // Close connection
        curl_close($ch);
        return $result;
    }
         
         
    
    
    
    
    
}



?>

