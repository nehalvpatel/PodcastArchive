<?php

	class Admin {
		private $con;
		
		public function __construct($con) {
			$this->con = $con;
		}
		
		public function validateTimestamp($timestamp) {
			$pattern = "/^(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d)$/";
			preg_match($pattern, substr($timestamp, 3), $matches, PREG_OFFSET_CAPTURE);
			
			if (count($matches) == 0) {
				return FALSE;
			} else {
				return TRUE;
			}
		}
		
		public function convertTimestamp($timestamp) {
			sscanf($timestamp, "%d:%d:%d", $hours, $minutes, $seconds);
			return isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
		}
		
		public function isLoggedIn() {
			if (isset($_SESSION["admin"]) && !empty($_SESSION["admin"])) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
		
		public function doLogin($username, $password){
			$errors = array();
			if (empty($username) || empty($password)) {
				$errors[] = "Please make sure all fields were filled in.";
			} else {
				$query = $this->con->prepare("SELECT `username` FROM `admins` WHERE `username` = :username AND `password` = :password");
				$query->execute(
					array(
						":username" => $_POST["username"],
						":password" => hash("sha512", $_POST["password"] . "305yh83],>")
					)
				);
				$results = $query->fetchAll();
				if(count($results)>0){
					return true;
				} else {
					$errors[] = "Incorrect username or password.";
				}
				
				return $errors;
			}
		}
	}

?>