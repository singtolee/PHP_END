<?php

ini_set('default_charset', 'utf-8');

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['uid']) && isset($_POST['pnumber']) && isset($_POST['total'])) {

    // receiving the post params
	$uid = $_POST['uid'];
    $pm = $_POST['pm'];
    $dt = $_POST['dt'];
    $pnumber = $_POST['pnumber'];
	$total = $_POST['total'];
	$usecash = $_POST['usecash'];
	$usebalance = $_POST['usebalance'];
	$usepoints = $_POST['usepoints'];
	$outstanding = $_POST['outstanding'];
	$reward = $_POST['reward'];

    // check if user is already existed with this user_ID
    if ($db->isUserIDExisted($uid)) {
        // create a new user
        $order = $db->writeOrder($uid,$pm,$dt,$pnumber,$total,$usecash,$usebalance,$usepoints,$outstanding,$reward);
        if ($order) {
            $response["error"] = FALSE;
			$response["orderNo"]["order_ID"] = $order["order_ID"];
            $response["orderNo"]["user_ID"] = $order["user_ID"];
            echo json_encode($response,JSON_UNESCAPED_UNICODE);
			$db->updateBP($uid,$total,$usecash,$usebalance,$usepoints);
        } else {
            //failed
            $response["error"] = TRUE;
            $response["error_msg"] = "Unknown error occurred in submitting orders!";
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
