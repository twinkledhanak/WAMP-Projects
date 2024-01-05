<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// this file deals with handling the tables in database
// this file directly communicates with database

// to edit the database we need the pointer returned by the class in db connect
// SPECIFICALLY IN THE CONSTRUCTOR BCOZ HAD IT BEEN IN OTHER METHOD, WE NEEDED TO CALL IT EXPLICITLY, 
// WHEREAS CONSTRUCTOR IS CALLED IMPLICITLY
// we create a constructor here and try to retireve the value of pointer by
//creating an obj of class db connect
// calling the method connect by using that obj
// givibg that value to a object created of this class

class dbhandler
{
    private $conn;
    
    function __construct() {
         require_once dirname(__FILE__) . '/db_connect.php';
         $db= new dbconnect();// creating object of class dbconnect()
         $this->conn= $db->connect();       
    }
    
    // now we start creating different items of the db
    
    // first we start with a user
    // we check if the user is already existing in the db or not
    // assume that user doent exist
    // we put his email id in the db, on successful insertion , we display appropriate msgs
    
    function createuser($name,$email) // we take only 2 parameters
    {
        $response= array(); // we create a array to store the details temporarily and then may be for putting it into  db
        
        
        if(!$this->isuserexists($email))  // if user doesnt exists in db
            // function isuserexixts is created by us
        {
            // so if user doessnt exists then we create user by our own
           $stmt= $this->conn->prepare("INSERT INTO users(name, email) values(?, ?)"); // calling methods using pointer $this->conn
            $stmt->bind_param("ss", $name, $email);
            
            $result= $stmt->execute();
            $stmt->close(); // we close the connection everytime we are done with our work
            
            // check on basis of result value
            if($result) // if reukt is true , ie, some value is inserted in db
            {
                $response["error"]="false";
                $response["name"]=$this->getuserbyname($name);
                $response["email"]=$this->getuserbyemail($email);
            }
            // inserting user has various cases 1. inserted successfully  2. not inserted successfully
            
            else
            {
                $response["error"]="true";
                $response["message"]="error occurred while your registration";
            }
            
        }
        // else if user  exists already in the database
        else {
            // since user is already with us, fetch only name and email of the user            
        
            $response["error"] = false;
            $response["name"]= $this->getuserbyname($name);
            $response["email"] = $this->getuserbyemail($email);
        }
        
     return $response;
    }
    
    
   public function updatefcmid($user_id,$fcm_reg_id)
   {
       $response= array();
       
       $stmt= $this->conn->prepare("UPDATE users SET fcm_reg_id = ? WHERE user_id = ?");
       $stmt->bind_param("si",$fcm_reg_id,$user_id); // fcm_reg_id is of type string
       $result=$stmt->execute();
      
       
       // now checking for the value returned
       if($result)
       {
           $response["error"]="false";
           $response["message"]="Your fcm id is updated successfully ";
       }
       
        else
        {
            $response["error"]="true";
           $response["message"]="Your fcm id is  not updated  ";
        }
        
         $stmt->close();
        return $response;
   }
    
   private function isuserexists($email)  
   {
       $stmt=$this->conn->prepare("SELECT user_id from users WHERE email = ?"); // we search using email since it is a unique key
       $stmt->bind_param("s",$email);
       $result= $stmt->execute();
              
       $stmt->store_result();
        $num_rows = $stmt->num_rows; // if user doesnt exists, then null is returned simply, so we put condition that
        //return result only if it has multiple lines
        $stmt->close();
        
        return $num_rows > 0; // we assume that if user exists, his information will have multiple lines, ie, no of rows >0                    
   }
   
   public function getuserbyemail($email)
   {
       $stmt=$this->conn->prepare("SELECT user_id, name, email, created_at FROM users WHERE email = ?");
       $stmt->bind_param("s", $email);
       $result=$stmt->execute();
      
       
       if($result)
       {
            $stmt->bind_result($user_id, $name, $email, $created_at); // we are not putting inside response array for user
            // as it has doent have fields for user details
             $stmt->fetch();
            $user = array(); // creating a separate array for users
            $user["user_id"] = $user_id;
            $user["name"] = $name;
            $user["email"] = $email;
            $user["created_at"] = $created_at;
            $stmt->close();
            return $user;
       }
       else
       {
            $stmt->close();
           return NULL;
       }            
   }
    
   public function getuserbyname($name)
   {
       
       // in this prepare statement, we cannot write select * from ... as no of parameters in prepare and bind param statement wont match
       $stmt=$this->conn->prepare("SELECT user_id,name,email,created_at FROM users WHERE name = ?");
       $stmt->bind_param("s",$name);
       $result=$stmt->execute();
       
       if($result) // if actually user is found
       {
           $stmt->bind_result($user_id, $name, $email, $created_at); 
            $stmt->fetch();
            $user = array(); // creating a separate array for users
            $user["user_id"] = $user_id;
            $user["name"] = $name;
            $user["email"] = $email;
            $user["created_at"] = $created_at;
            $stmt->close();
            return $user;
       }
       else
       {
           $stmt->close();
           return NULL;   
       }
       
   }
   
   // adding function for inserting message into datbase
   
   // messaging in a chat room / to persional message
    public function addmessage($user_id, $chat_room_id, $message) {
        $response = array();
 
        $stmt = $this->conn->prepare("INSERT INTO messages (chat_room_id, user_id, message) values(?, ?, ?)");
        $stmt->bind_param("iis", $chat_room_id, $user_id, $message);
 
        $result = $stmt->execute();
             echo $result;
       // if ($result) {
            $response['error'] = false;
 
            // get the message
            $message_id = $this->conn->insert_id;
            $stmt = $this->conn->prepare("SELECT message_id, user_id, chat_room_id, message, created_at FROM messages WHERE message_id = ?");
            $stmt->bind_param("i", $message_id);
            if ($stmt->execute()) {
                $stmt->bind_result($message_id, $user_id, $chat_room_id, $message, $created_at);
                $stmt->fetch();
                $tmp = array();
                $tmp['message_id'] = $message_id;
                $tmp['chat_room_id'] = $chat_room_id;
                $tmp['message'] = $message;
                $tmp['created_at'] = $created_at;
                
                $response['message'] = $tmp;
            }
     //   } else {
       //     $response['error'] = true;
         //   $response['message'] = 'Failed send message';
        //}
 
        return $response;
    }
   
   
   // this below function for adding message in database
   // messaging in a chat room / to persional message
    
        // fetching single user by id
    public function getuser($user_id) {
        $stmt = $this->conn->prepare("SELECT user_id, name, email, fcm_reg_id, created_at FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id); // previously this was string "s", now i have made it integer
        if ($stmt->execute()) {
          
            $stmt->bind_result($user_id, $name, $email, $fcm_reg_id, $created_at);
            $stmt->fetch();
            $user = array();
            $user["user_id"] = $user_id;
            $user["name"] = $name;
            $user["email"] = $email;
            $user["fcm_reg_id"] = $fcm_reg_id;
            $user["created_at"] = $created_at;
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }
    
     
    // fetching all chat rooms
    public function getallchatrooms() {
        $stmt = $this->conn->prepare("SELECT * FROM chat_rooms");
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
 
    // fetching single chat room by id
   function getChatRoom($chat_room_id) {
        $stmt = $this->conn->prepare("SELECT cr.chat_room_id, cr.name, cr.created_at as chat_room_created_at, u.name as username, c.* FROM chat_rooms cr LEFT JOIN messages c ON c.chat_room_id = cr.chat_room_id LEFT JOIN users u ON u.user_id = c.user_id WHERE cr.chat_room_id = ?");
        $stmt->bind_param("i", $chat_room_id);
        $stmt->execute();
        $tasks = $stmt->get_result(); // get_result() is not a user defined function, it is provided by mysqli
        $stmt->close();
        return $tasks;
    }
 

    
    
 }
?>
