<?php

ini_set('default_charset', 'utf-8');

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['oID']) && isset($_POST['pID']) && isset($_POST['uID'])) {

    // receiving the post params
	$uid = $_POST['uID'];
  $oid = $_POST['oID'];
  $pid = $_POST['pID'];
  $price = $_POST['price'];
	$quantity = $_POST['quantity'];
	$pname = $_POST['pname'];
    // check if user is already existed with this user_ID
		$order = $db->writeOrderonebyone($uid,$oid,$pid,$price,$quantity,$pname);
		if ($order) {
				$response["error"] = FALSE;
				$response["orderNo"]["order_ID"] = $order["order_ID"];
				$response["orderNo"]["user_ID"] = $order["user_ID"];
				echo json_encode($response,JSON_UNESCAPED_UNICODE);
		} else {
				//failed
				$response["error"] = TRUE;
				$response["error_msg"] = "Unknown error occurred in submitting orders!";
				echo json_encode($response);
		}
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required information is missing!";
    echo json_encode($response);
}
?>
