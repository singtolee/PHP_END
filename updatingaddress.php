<?php

ini_set('default_charset', 'utf-8');
 
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['userID']) && isset($_POST['building']) && isset($_POST['floor'])  && isset($_POST['room'])) {
 
    // receiving the post params
	$userID = $_POST['userID'];
    $building = $_POST['building'];
    $floor = $_POST['floor'];
    $room = $_POST['room'];
	$company = $_POST['company'];
	$phone = $_POST['phone'];
 
    // check if user is already existed with this user_ID
    if ($db->isUserIDExisted($userID)) {
        // create a new user
        $userAddress = $db->updateUserAddress($userID,$building,$floor,$room,$company,$phone);
        if ($userAddress) {
            $response["error"] = FALSE;
			$response["userAddress"]["building"] = $userAddress["building"];
            $response["userAddress"]["floor"] = $userAddress["floor"];
			$response["userAddress"]["room"] = $userAddress["room"];
            $response["userAddress"]["company"] = $userAddress["company"];
			$response["userAddress"]["phone"] = $userAddress["phone"];
            echo json_encode($response,JSON_UNESCAPED_UNICODE);
        } else {
            // user failed to store
            $response["error"] = TRUE;
            $response["error_msg"] = "Unknown error occurred in updating address!";
            echo json_encode($response);
        }
    } else {
       //no user yet
	   $response["error"] = TRUE;
	   $response["error_msg"] = "User does not exist!";
	   echo json_encode($response);
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required information is missing!";
    echo json_encode($response);
}
?>