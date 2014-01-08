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
				$query = $this->con->prepare("SELECT `username` FROM `admins` WHERE `username` = :username AND `password` = :password");
				$query->execute(
					array(
						":username" => $_POST["username"],
						":password" => hash('sha512', $_POST['password'] . '305yh83],>')
					)
				);
				$results = $query->fetchAll();
				if(count($results)>0){
					return true;
				} else {
					$errors[] = 'Incorrect username or password.';
				}
			return $errors;
			}
		}
		
		public function addTimestamp($timestamp, $value, $episode, $type='Text', $url='', $special='0'){
			$errors = array();
			if(empty($timestamp) || empty($value) || empty($episode)){
				$errors[] = 'Please make sure all fields were filled in.';
			}
			if($type=='link' && $url==''){
				$errors[] = 'A URL must be provided for the link.';
			}
			
			// Validate timestamp
			$pattern = '/^(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d)$/';
			preg_match($pattern, substr($timestamp, 3), $matches, PREG_OFFSET_CAPTURE);
			if (count($matches) == 0) {
				$errors[] = 'The timestamp is invalid.';
			}
			
			if(empty($errors)){
				// Convert timestamp
				sscanf($timestamp, '%d:%d:%d', $hours, $minutes, $seconds);
				$timestamp = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
				
				$query = $this->con->prepare('INSERT INTO `timestamps` (timestamp, value, episode, type, url, special) VALUES (:timestamp, :value, :episode, :type, :url, :special)');
				$result = $query->execute(array(
					':timestamp' 	=> $timestamp,
					':value' 		=> $value,
					':episode' 		=> $episode,
					':type' 		=> $type,
					':url' 			=> $url,
					':special' 		=> $special
				));
				if($result){
					return true;
				} else {
					$errors[] = 'A MySQL error occured.';
					return $errors;
				}
			}
		}
		
		public function addTimeline($episode_input, $value){
			
			$errors = array();
			if(empty($episode_input) || empty($value)){
				$errors[] = 'Please make sure all fields were filled in.';
			}
			
			if(empty($errors)){
				$timestamps = preg_split("/\r\n|\n|\r/", $value);
				foreach ($timestamps as $timestamp) {
					$explosion = explode(" - ", $timestamp);
					
					$time = trim($explosion[0]);
					$event = trim($explosion[1]);
					
					$result = $this->addTimestamp($time, $event, 'PKA_'.$episode_input);
					if(!$result === true){
						print_r($result);
					}
				}
				return true;
			}
		}

		public function addEpisode(){
		
		}
	}
?>