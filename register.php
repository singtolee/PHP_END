<?php

ini_set('default_charset', 'utf-8');
 
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['email'])  && isset($_POST['password'])) {
 
    // receiving the post params
	$firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
 
    // check if user is already existed with the same email
    if ($db->isUserExisted($email)) {
        // user already existed
        $response["error"] = TRUE;
        $response["error_msg"] = "User already existed with " . $email;
        echo json_encode($response);
    } else {
        // create a new user
        $user = $db->storeUser($firstname, $lastname,  $email, $password);
        if ($user) {
            // user stored successfully
            $response["error"] = FALSE;
			$response["user"]["user_ID"] = $user["user_ID"];
			$response["user"]["firstname"] = $user["firstname"];
            $response["user"]["lastname"] = $user["lastname"];
			$response["user"]["date_added"] = $user["date_added"];
            $response["user"]["email"] = $user["email"];
            echo json_encode($response,JSON_UNESCAPED_UNICODE);
        } else {
            // user failed to store
            $response["error"] = TRUE;
            $response["error_msg"] = "Unknown error occurred in registration!";
            echo json_encode($response);
        }
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters (name, email or password) is missing!";
    echo json_encode($response);
}
?>