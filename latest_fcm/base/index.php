<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// basic aim to create this file is to handle rest api points for the android app
// this also involves json parsing using in-built php libraries
// used to connect the innermost database and ?
// it connects to the database via db_handler file
// TO BE PASTED INSIDE V1 FOLDER OF WAMP

error_reporting(-1);
ini_set('display_errors', 'On'); // probably for displaying errors


// for specifying the paths used,     _DIR_  tells the current path used
// but if we do not use it, we need to use entire path


//require_once 'C:\wamp64\www\fcm_chat\include\db_handler.php'; /// still left to include the path
//require 'C:\wamp64\www\fcm_chat\libs\Slim\Slim.php'; /// still left to include path
// also left to implement slim framework


require_once '../include/db_handler.php';
require '.././libs/Slim/Slim.php';


\Slim\Slim::registerAutoloader();
 
$app = new \Slim\Slim(); // creating new object of slim

// now handling user login
// when slim detects that user-login is to be handled, which are provided on the console and are needed
// so our job is to retieve those values functions provided by slim
// //  it user method to retrieve those values using its own functions
// we get access to slim by creating an object of slim
// after we have those values, it is our duty to verify them and add them to our database

$app->post('/user/login', function() use ($app) // this is a function inside a method
{
    // validating details
    verifyRequiredParams(array('name','email')); // we pass array of names and emails as parameters to be verified
    // since we are passing paramters which are just indexes, we write them in ''
    
    // reading post params
    //retrieve values for name and email mostly USING SLIM
    $name = $app->request->post('name'); // calling the same method inside which it is written ,ie, post method
    $email = $app->request->post('email');
    
    //it is important to verify the email, hence call that function
    verifyemail($email);
    
    // retrieve the pointer to the database by creating an object of db handler
    $db = new dbhandler();
    
    // now that we have retrieved values of name and email, we need to create a user entry in the database
    $response= $db->createuser($name, $email);
    
    // now we have to parse it in json format, we call a function that does that n also returns an appropriate result
    echoresponse(200,$response); // we pass $response array also to function as it contains the returned details of the user created
    // we have returned name and email of the user created
});

// COMMON STEPS INVOLVED IN EACH METHOD
//
// 1. write a put/get/post method that takes end url and a function as two paramters
// we are writing different functions bcoz each of them has different no of paramteres , so we write them inside their method simultaneously
// 
// NOW INSIDE THIS FUNCTION: 
// 2. call a method to verify paramteres , paramter for which we are writing method, eg updating id, so fcm id (this method is written by us)
// 3. retrieve the value from console using SLIM METHOD- request->put
// 4. obtain the same pointer for handling crud operations on database
// 5. function for doing this task is written in file db_handler.php, so we call method of that class
// 6. finally, method echoResponse is called



/* * *
 * Updating user
 *  we use this url to update users's fcm reg id
 */
$app->put('/user/:id', function($user_id) use ($app) 
                {
    global $app;
 
    verifyRequiredParams(array('fcm_reg_id'));
 
    $fcm_reg_id = $app->request->put('fcm_reg_id');
 
    $db = new dbhandler();
    $response = $db->updatefcmid($user_id, $fcm_reg_id);
 
    echoresponse(200, $response);
});


/* * *
 * fetching all chat rooms
 */
$app->get('/chat_rooms', function() {
    $response = array();
    $db = new dbhandler();
 
    // fetching all user tasks
    $result = $db->getallchatrooms();
 
    $response["error"] = false;
    $response["chat_rooms"] = array();
 
    // pushing single chat room into array
    while ($chat_room = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["chat_room_id"] = $chat_room["chat_room_id"];
        $tmp["name"] = $chat_room["name"];
        $tmp["created_at"] = $chat_room["created_at"];
        array_push($response["chat_rooms"], $tmp);
    }
 
    echoresponse(200, $response);
});
 

/**
 * Messaging in a chat room
 * Will send push notification using Topic Messaging
 *  */

// to handle messaging, we have to use 2 functions, for quesions and  for answers



$app->post('/chat_rooms/:id/message', function($chat_room_id) { // for posting questions in chat room
    global $app;
    $db = new dbhandler();
 
    verifyRequiredParams(array('user_id', 'message'));
 
    $user_id = $app->request->post('user_id');
    $message = $app->request->post('message');
 
  
    $response= $db->addmessage($user_id, $chat_room_id, $message);
    
    if ($response['error'] == false) {
        require_once __DIR__ . '/../libs/fcm/firebase.php';
        require_once __DIR__ . '/../libs/fcm/push.php';
        $fcm= new firebase();
        $push = new push();
 
        // get the user using userid
      
           $user= $db->getuser($user_id);
        
        $data = array();
        $data['user'] = $user;
      // $data['question']=$response['question'];
        
        $data['message']=$response['message'];
        $data['chat_room_id'] = $chat_room_id;
 
        $push->settitle("fcm message service");
        $push->setisbackground(FALSE);
      
        $push->setmessage($data);
        
 
        // sending push message to a topic
        $fcm ->sendmessagetotopic('topic_'. $chat_room_id, $push->getpush());
        
 
        $response['user'] = $user;
        $response['error'] = false;
    }
 
    echoresponse(200, $response);
});
 


$app->post('/sendtoall', function() use ($app)
{
    $response=array();
    verifyRequiredParams('user_id','message');
    
     require_once __DIR__ . '/../libs/fcm/firebase.php';
        require_once __DIR__ . '/../libs/fcm/push.php';
        
    
    $db= new dbhandler(); // retrieveing pointer for database connection
    
    $fcm= new firebase();
    $push= new push();
    
    $user_id= $app->request->post('user_id') ; // retrieving user_id and notice 
    $message = $app->request->post('message');
    
    // now getting the user using user id
    
    
    $user= $db->getuser($user_id);
    // creating tmp message, skipping database insertion
    $msg = array();
    $msg['message'] = $message;
    $msg['message_id'] = '';
    $msg['chat_room_id'] = '';
    $msg['created_at'] = date('Y-m-d G:i:s');
    
    
    
    // we are also passing image along with our data
     $data = array();
    $data['user'] = $user;
    $data['message'] = $msg;
  //  $data['image'] = 'http://www.androidhive.info/wp-content/uploads/2016/01/Air-1.png';
    $data['image']= 'https://www.google.co.in/search?hl=en&site=imghp&tbm=isch&source=hp&biw=1280&bih=615&q=720+X+359+all+images&oq=720+X+359+all+images&gs_l=img.3...2999.21164.0.21367.20.7.0.13.0.0.113.654.6j1.7.0....0...1ac.1.64.img..0.5.479...0j0i10i30k1j0i5i30k1j0i10i24k1j0i24k1.BG0Xk8OYxok#imgrc=rtJZV1FzYIPHdM%3A';
    
    $backvar=FALSE;
    
    $push->settitle("Firebase Cloud Messaging");
    $push->setisbackground($backvar);
  
    $push->setmessage($data);// may be we are not sending image along with json
    
    // sending message to topic `global`
    // On the device every user should subscribe to `global` topic
   // $gcm->sendToTopic('global', $push->getPush());
    
     // FOR SENDING GLOBAL MESSAGES, WE NEED THE REG IDS OF ALL USERS
    // FOR NOW THE IDS ARE HARDCODED HERE AND STORED IN AN ARRAY
    
    $reg_id= array('1','2'); // ALL USER IDS ARE ASSIGNED HERE---------------------------STILL   LEFT  TO  BE  WRITTEN
 
  //  $fcm->sendglobalmessage($reg_id, $push->getpush()); // instead of writing $notice_id or anything else, write getpush() method
    
    $fcm->sendmessagetotopic($reg_id, $push->getpush());
    
    $response['user'] = $user;
    $response['error'] = false;
 
    echoresponse(200, $response);
}
);

/**
 * Fetching single chat room including all the chat messages
 *  */
$app->get('/chat_rooms/:id/message', function($chat_room_id) { // this is for getting all questions of chat rooms
    global $app;
    $db = new dbhandler();
 
    $result = $db->getChatRoom($chat_room_id);
 
    // we are trying to create 2d matrix  ( may be ??????????? )
    
    $response['error'] = false;
    $response['messages'] = array();
    $response['chat_room'] = array();
 
    $i = 0;
    // looping through result and preparing tasks array
    while ($chat_room = $result->fetch_assoc()) { // fetching the result
        // adding chat room node
        if ($i == 0) {
            $tmp = array();
            $tmp["chat_room_id"] = $chat_room["chat_room_id"];
            $tmp["name"] = $chat_room["name"];
            $tmp["created_at"] = $chat_room["chat_room_created_at"];
            $response['chat_room'] = $tmp;
            // basically we are storing single chat chat room in single array 
            // then the value of that single chat room is stored inside main array which is response array
        }
 
        if ($chat_room['user_id'] != NULL) {
            // question node
            $cmt = array();
            $cmt["message"] = $chat_room["message"];
            $cmt["message_id"] = $chat_room["message_id"];
            $cmt["created_at"] = $chat_room["created_at"];
           
            // answer node
 
            // user node
            $user = array();
            $user['user_id'] = $chat_room['user_id'];
            $user['name'] = $chat_room['name'];
            $cmt['user'] = $user;
            
 
            array_push($response["messages"], $cmt);
        }
    }
 
    echoresponse(200, $response);
});
///////////////////////



/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $required_fields = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
 
    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoresponse(400, $response);
        $app->stop();
    }
}
/**
 * Validating email address
 */
function verifyemail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoresponse(400, $response);
        $app->stop();
    }
}

function IsNullOrEmptyString($str) {
    return (!isset($str) || trim($str) === '');
}


/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoresponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);
 
    // setting response content type to json
    $app->contentType('application/json');
 
    echo json_encode($response); // this is the library used for encoding json
}
 
$app->run();

 


?>

