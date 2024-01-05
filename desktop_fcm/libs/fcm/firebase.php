<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class firebase
{
      public function sendmessagetotopic($to,$message) // sends messages via topic
    {
        //this is used to send msg to particular chat room or (topic) eg.. computers, elex, extc
        // in other words, to send msg to users subscribed to a particular topic
        
        // WE ARE CONSIDERING GLOBAL MESSAGES AS PART OF TOPIC GLOBAL, SO NO NEED FOR A METHOD sendglobalmessages
        $fields= array(
            'to' => '/topics/' . $to, 
            'data' => $message,
            );
        return $this->sendpushnotifications($fields);
    }
}


?>