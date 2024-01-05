<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// basic aim to create this file is to prepare and push notification json that needs to be sent to firebase
// this class specifies the format to be used for json 
// notifs and data bth uses- title, question,

class push
{
    private $title; // specifies title of the question or may be notif header -- DJCOMM
   
    private $data; // speicifies msg body -- hey, can someone explain me bus arbitration ?? //this might not be visible in notif at that time
    private $image; // specifies image to be sent along with  notif
    private  $message; // to specify the message sent
    private $isbackground; // this is used as flag to indicate whether app is in background or not
    
    // to indicate the type of notification
    // WE ARE NOT USING FLAG HERE TO INDICATE ANYTHING
    
    // creating a constructor
    function _construct()
    {
        
    }
    
    // different functions for setting the parameters , no need to return anything from here
    public function settitle($title)
    {
        $this->title= $title;
    }
    
    public function setmessage($message)
    {
        $this->message= $message;
    }

        public function setimage($imageurl)
    {
        $this->image = $imageurl;
    }
    
    public function setpayload($data)
    {
        $this->data= $data;
    }
    
    public function setisbackground($isbackground)
    {
        $this->isbackground= $isbackground;
    }
    
   

        // function to get all parameters and store them in array $res, return this array
    // this array is a 2d array, for which main part is data and for the rest are the other titles specified
    public function getpush()
    {
        $res = array();
        
        $res['data']['title']= $this->title;
        $res['data']['isbackground']= $this->isbackground;
        $res['data']['message'] = $this->message;
        $res['data']['image']= $this->image;
       
        $res['data']['payload']= $this->data;
      
        $res['data']['timestamp'] = date('Y-m-d G:i:s');
        return $res;
        
    }
}

?>

