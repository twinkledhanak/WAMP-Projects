<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


error_reporting(-1);
ini_set('display_errors', 'On'); 


require_once '../include/db_handler.php';
require '.././libs/Slim/Slim.php';


\Slim\Slim::registerAutoloader();
 
$app = new \Slim\Slim();


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

$app->get('/interests', function() {
    $response = array();
    $db = new dbhandler();
 
    // fetching all user tasks
    $result = $db->getallinterests();
 
    $response["error"] = false;
    $response["interests"] = array();
 
    // pushing single chat room into array
    while ($interest = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["interest_id"] = $interest["interest_id"];
        $tmp["name"] = $interest["name"];
        
        array_push($response["interests"], $tmp);
    }
 
    echoresponse(200, $response);
});
 
$app->get('/interests/:id', function($interest_id) { // this is for getting all questions of chat rooms
    global $app;
    $db = new dbhandler();
 
   // $result = $db->getChatRoom($chat_room_id);
    $result= $db->getsimilarpeople($interest_id);
 
    // we are trying to create 2d matrix  ( may be ??????????? )
    
    $response['error'] = false;
    $response['messages'] = array();
    
 
    $i = 0;
    // looping through result and preparing tasks array
    while ($interests = $result->fetch_assoc()) { // fetching the result
        // adding chat room node
        if ($i == 0) {
            $tmp = array();
            $tmp["interest_id"] = $interests["interest_id"];
            $tmp["name"] = $chat_room["name"];
           
            $response['interests'] = $tmp;
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
 
    echo json_encode($response); // this is the library used for encoding json // instead of using :  echo ($response); we use the json_encode bcoz earlier one will give 
    // error like array to string conversion and arrays cannot be directly printed using echo statement, so we use json_encode
}
 
$app->run();

 


?>