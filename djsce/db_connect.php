<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class dbconnect
{
    private $conn;
    // making constructor to initialize and destructor to destroy connection
    
    function _construct()
    {
        
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