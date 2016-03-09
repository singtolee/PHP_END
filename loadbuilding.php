<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
		$response = $db->loadofficebuilding();
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
  
?>