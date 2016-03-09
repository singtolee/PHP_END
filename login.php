<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
 
if (($_POST['email'])!='' && ($_POST['password'])!='') {
 
    // receiving the post params
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
	
	if($db->isUserExisted($email)){
		
		$user = $db->getUserByEmailAndPassword($email, $password);
		
		if ($user != false) {
        // use is found
        $response["error"] = FALSE;
		$response["user"]["user_ID"] = $user["user_ID"];
		$response["user"]["firstname"] = $user["firstname"];
        $response["user"]["lastname"] = $user["lastname"];
		$response["user"]["date_added"] = $user["date_added"];
        $response["user"]["email"] = $user["email"];
		$response["user"]["phone"] = $user["phone"];
		$response["user"]["building"] = $user["building"];
		$response["user"]["floor"] = $user["floor"];
		$response["user"]["room"] = $user["room"];
		$response["user"]["company"] = $user["company"];
		$response["user"]["balance"] = $user["balance"];
		$response["user"]["points"] = $user["points"];
		
        echo json_encode($response,JSON_UNESCAPED_UNICODE);
    } else {
        // user is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "Login credentials are wrong. Please try again!";
        echo json_encode($response);
    }
		
	}else{
		$response["error"] = TRUE;
        $response["error_msg"] = "There is no user under this Email.";
        echo json_encode($response);
	}
} else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters email or password is missing!";
    echo json_encode($response);
}
?>