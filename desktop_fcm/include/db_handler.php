<?php


class dbhandler
{
     private $conn;
    
    function __construct() {
         require_once dirname(__FILE__) . '/db_connect.php';
         $db= new dbconnect();// creating object of class dbconnect()
         $this->conn= $db->connect();       
    }
    
    
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
   
     public function getuser($user_id) {
        $stmt = $this->conn->prepare("SELECT user_id, name, email created_at FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id); // previously this was string "s", now i have made it integer
        if ($stmt->execute()) {
          
            $stmt->bind_result($user_id, $name, $email,$created_at);
            $stmt->fetch();
            $user = array();
            $user["user_id"] = $user_id;
            $user["name"] = $name;
            $user["email"] = $email;
            
            $user["created_at"] = $created_at;
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }
    
      public function getallinterests() {
        $stmt = $this->conn->prepare("SELECT * FROM interests");
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
    
    /// function to get ppl as per their interests
    //  ONFIRM THIS QUERY ------- //////??????
    public function getsimilarpeople($user_id)
    {
        $stmt= $this->conn->prepare("SELECT * FROM interests WHERE user_id= ?  GROUP BY interest_id ");
        $stmt->bind_param("i",$user_id);
        $result= $stmt->execute();
        
        if($result)
        {
            $stmt->bind_result($user_id, $name, $email,$created_at);
            $stmt->fetch();
            $user = array();
            $user["user_id"] = $user_id;
            $user["name"] = $name;
            $user["email"] = $email;
            
            $user["created_at"] = $created_at;
            $stmt->close();
            return $user;
            
        }
        else
        {
            return NULL;
        }
    }
    
 
    
    
}
?>