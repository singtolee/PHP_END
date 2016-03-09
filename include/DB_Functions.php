<?php

class DB_Functions {

    private $conn;

    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }

    // destructor
    function __destruct() {

    }

    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($firstname, $lastname, $email, $password) {
        $encrypted_password = md5($password); // encrypted password

        $stmt = $this->conn->prepare("INSERT INTO oc_app_user(fname, lname, email, password, date_added) VALUES(?, ?, ?, ?, NOW() )");
        $stmt->bind_param("ssss", $firstname, $lastname, $email,  $encrypted_password);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT user_ID, fname, lname, email, date_added FROM oc_app_user WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($user_ID, $fname,$lname, $email, $date_added);
		while($stmt->fetch()){
			$user["user_ID"] = $user_ID;
			$user["firstname"] = $fname;
			$user["lastname"] = $lname;
			$user["email"] = $email;
			$user["date_added"] = $date_added;
		}
			$stmt->free_result;
            $stmt->close();

			$adress = $this->conn->prepare("INSERT INTO oc_app_user_address VALUES(?,'', 'Bangkok', 'CBD', '', '', '', '')");
			$adress->bind_param("s",$user["user_ID"]);
			$xx = $adress->execute();
			$adress->close();

			$balance = $this->conn->prepare("INSERT INTO oc_app_userBP VALUES(?,'', '')");
			$balance->bind_param("s",$user["user_ID"]);
			$xxx = $balance->execute();
			$balance->close();

            return $user;
        } else {
            return false;
        }
    }

	public function updateUserAddress($userID,$building,$floor,$room,$company,$phone){
		if($stmt = $this->conn->prepare("UPDATE oc_app_user_address SET phone = ?, building = ?, floor = ?, room = ?, company = ? WHERE user_ID = ?")){
			$stmt->bind_param("ssssss", $phone, $building, $floor,  $room, $company, $userID);
		}else{printf("Errormessage: %s\n", $this->conn->error);}
		//$stmt = $this->conn->prepare("UPDATE oc_app_user_address SET phone = ?, buildding = ?, floor = ?, room = ?, company = ? WHERE user_ID = ?");
        //$stmt->bind_param("dssssd", $phone, $buildding, $floor,  $room, $company, $userID);
        $result = $stmt->execute();
        $stmt->close();
        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT phone, building, floor, room, company FROM oc_app_user_address WHERE user_ID = ?");
            $stmt->bind_param("s", $userID);
            $stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($phone, $building,$floor, $room, $company);
		while($stmt->fetch()){
			$userAddress["phone"] = $phone;
			$userAddress["building"] = $building;
			$userAddress["floor"] = $floor;
			$userAddress["room"] = $room;
			$userAddress["company"] = $company;
		}
			$stmt->free_result;
            $stmt->close();
            return $userAddress;
        } else {
            return false;
        }
	}

	public function getfruit(){
		//$sql = "SELECT product_id, model, image, price FROM oc_product WHERE tax_class_id = 10 ORDER BY date_added DESC ";
		//$sql = "SELECT product_id, model, image, price FROM oc_product WHERE tax_class_id = 10 ";
		//$sqlnormal = "SELECT oc_product.product_id, oc_product.quantity,oc_product.price, oc_product.image, oc_product_description.name, oc_product_description.description FROM oc_product, oc_product_description WHERE oc_product.tax_class_id = 10 AND oc_product.product_id = oc_product_description.product_id";

		$sqldes = "SELECT oc_product.product_id, oc_product.quantity,oc_product.price, oc_product.image, oc_product_description.name, oc_product_description.description FROM oc_product, oc_product_description WHERE oc_product.tax_class_id = 10 AND oc_product.product_id = oc_product_description.product_id ORDER BY oc_product.date_added DESC";

		$fruit = $this->conn->query($sqldes);
		$prefix = "http://shop.dishhub.co/image/";
		$i =0;
		while($row = $fruit->fetch_row()){
			$fruitcut[$i]["id"] = $row[0];
			$fruitcut[$i]["quantity"] = $row[1];
			$fruitcut[$i]["price"] = $row[2];
			$fruitcut[$i]["image"] = stripslashes($prefix.$row[3]);
			$fruitcut[$i]["name"] = $row[4];
			$fruitcut[$i]["description"] = $row[5];
			$i++;
		}
		$fruit->close();
		return $fruitcut;
	}

	public function loadofficebuilding(){
		$sql = "SELECT building FROM oc_app_building";
		$ob = $this->conn->query($sql);
		$i = 0;
		while($row = $ob ->fetch_row()){
			$building[$i]["building"]=$row[0];
			$i++;
		}
		$ob->close();
		return $building;
	}

    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password) {

		$ppssww = $password;

        $stmt = $this->conn->prepare("SELECT user_ID, fname,  lname,  email, password, date_added FROM oc_app_user WHERE email = ?");

        $stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($user_ID,$firstname,$lastname, $email, $password,  $date_added);
		while($stmt->fetch()){
			$user["user_ID"] = $user_ID;
			$user["firstname"] = $firstname;
			$user["lastname"] = $lastname;
			$user["email"] = $email;
			$user["psw"] = $password;
			$user["date_added"] = $date_added;}

			if(md5($ppssww)==$user["psw"]){
				$stmt->free_result;
				$stmt->close();
				$getBPA = $this->conn->prepare("SELECT phone, building, floor, room, company FROM oc_app_user_address WHERE user_ID = ?");
				$getBPA->bind_param("s",$user["user_ID"]);
				$getBPA->execute();
				$getBPA->store_result();
				$getBPA->bind_result($phone,$building,$floor,$room,$company);
				while($getBPA->fetch()){
					$user["phone"] = $phone;
					$user["building"] = $building;
					$user["floor"] = $floor;
					$user["room"] = $room;
					$user["company"] = $company;
				}
				$getBPA->free_result;
				$getBPA->close;

				$getBP = $this->conn->prepare("SELECT balance, points FROM oc_app_userBP WHERE user_ID = ?");
				$getBP->bind_param("s",$user["user_ID"]);
				$getBP->execute();
				$getBP->store_result();
				$getBP->bind_result($balance,$points);
				while($getBP->fetch()){
					$user["balance"] = $balance;
					$user["points"] = $points;
				}
				$getBP->free_result;
				$getBP->close;
				return $user;
			}else{
				return NULL;
			}
	}
    /**
     * Check user is existed or not
     */
    public function isUserExisted($email) {
        $stmt = $this->conn->prepare("SELECT email from oc_app_user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // user existed
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }

	public function isUserIDExisted($userID) {
        $stmt = $this->conn->prepare("SELECT user_ID from oc_app_user_address WHERE user_ID = ?");
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // user existed
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }

	public function writeOrder($uid,$pm,$dt,$items,$total,$cash,$balance,$points,$outstanding,$reward){
		if($stmt = $this->conn->prepare("INSERT INTO oc_app_order(user_ID, payment_method, deliver_time, items,total,use_cash,use_balance,use_points,outstanding,reward,status,date_added) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', NOW())")){
			$stmt->bind_param("dssddddddd", $uid, $pm, $dt, $items, $total, $cash, $balance, $points, $outstanding, $reward);
		}else{printf("Errormessage: %s\n", $this->conn->error);}

        $result = $stmt->execute();
        $stmt->close();
        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT order_ID,user_ID FROM oc_app_order WHERE user_ID = ?");
            $stmt->bind_param("s", $uid);
            $stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($orderID,$userID);
		while($stmt->fetch()){
			$order["order_ID"] = $orderID;
			$order["user_ID"] = $userID;
		}
			$stmt->free_result;
            $stmt->close();
            return $order;
        } else {
            return false;
        }
	}
	
	public function updateBP($uid,$total,$usecash,$usebalance,$usepoints){
		if($stmt = $this->conn->prepare("SELECT balance,points FROM oc_app_userBP WHERE user_ID = ?")){
			$stmt->bind_param("s", $uid);
			$stmt->store_result();
			$stmt->bind_result($balance,$points);
			while($stmt->fetch()){
				$ucB = $balance;
				$ucP = $points;}
			$stmt->free_result;
            $stmt->close();
			$ucB = $ucB - $usebalance;
			$ucP = $ucP - $usepoints + ($usecash + $usebalance)/20;
			if($bp = $this->conn->prepare("UPDATE oc_app_userBP SET balance = ?, points = ? WHERE user_ID = ?")){
			$bp->bind_param("ddd", $ucB, $ucP, $uid);
		}else{printf("Errormessage: %s\n", $this->conn->error);}
        $bp->execute();
        $bp->close();		
		}else{printf("Errormessage: %s\n", $this->conn->error);}
	}

  public function writeOrderonebyone($uid,$oid,$pid,$price,$quantity,$pname){
    if($stmt = $this->conn->prepare("INSERT INTO oc_app_order_product(name, quantity, price, order_ID, user_ID, product_ID) VALUES(?, ?, ?, ?, ?, ?)")){
			$stmt->bind_param("sddddd", $pname, $quantity, $price, $oid, $uid, $pid);
		}else{printf("Errormessage: %s\n", $this->conn->error);}

        $result = $stmt->execute();
        $stmt->close();
        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT order_ID,user_ID FROM oc_app_order_product WHERE user_ID = ?");
            $stmt->bind_param("s", $uid);
            $stmt->execute();
			         $stmt->store_result();
			            $stmt->bind_result($orderID,$userID);
		                while($stmt->fetch()){
			                   $order["order_ID"] = $orderID;
			                   $order["user_ID"] = $userID;
		                     }
			            $stmt->free_result;
                  $stmt->close();
                  return $order;
        } else {
            return false;
        }
  }
}

?>
