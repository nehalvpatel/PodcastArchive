<?php
	class Admin{
		
		private $con;
	
		public function __construct($con) {
			$this->con = $con;
		}
		
		public function doLogin($username, $password){
			$errors = array();
			if(empty($username) || empty($password)){
				$errors[] = 'Please make sure all fields were filled in.';
			} else {
				$login_query = $this->con->prepare("SELECT `username` FROM `admins` WHERE `username` = :username AND `password` = :password");
				$login_query->execute(
					array(
						":username" => $_POST["username"],
						":password" => hash('sha512', $_POST['password'] . '305yh83],>')
					)
				);
				$login_results = $login_query->fetchAll();
				if(count($login_results)>0){
					return true;
				} else {
					$errors[] = 'Incorrect username or password.';
				}
			return $errors;
			}
		}
	}
?>