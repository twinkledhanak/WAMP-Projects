<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */





// now we are connecting to database, function used is mysql_connect(), it returns a db handler which is a pointer for database connection
// this pointer is used to manipulate all crud functions

// apparently this class is cretaed so that future if we require handler we can obatin it by creating object of dbconnect,
// then call method connect() and retrieve the handler
class dbconnect
{
    private $conn;
    
    function __construct() {        // this is a constructor, although not reqd, we put it
    }
 
        
 function connect() // we create a separate function as we want to return the db handler which is pointer to our db
 {
     
     include_once dirname(__FILE__) . '/config.php';
     // we first include credentials file
    $this->conn = new  mysqli($hostname,$username,$password,$dbname); // method of advanced mysqli or improved - mysql
    // using method mysqli and passing parameters which are our credentials
    // RETURNED VALUE IS OUR REFERENCE

  if(mysqli_connect_errno()) // if there is error in connecting to database
    echo 'failed to connect to database'. mysqli_connect_error();
  else 
    echo ' connected to database';
  
  
  return $this->conn;
}


}
?>