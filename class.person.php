<?php

	class Person {
		private $con;
		private $person_data;
		
		public function __construct($con) {
			$this->con = $con;
			unset($con);
		}
		
		public function initWithID($id) {
			$person_query = $this->con->prepare("SELECT * FROM `people` WHERE `ID` = :ID");
			$person_query->execute(array(":ID" => $id));
			$person_results = $person_query->fetchAll();
			
			if (count($person_results) > 0) {
				$this->person_data = $person_results[0];
			} else {
				throw new Exception("Invalid person ID");
			}
		}
		
		public function initWithName($name) {
			$person_query = $this->con->prepare("SELECT * FROM `people` WHERE `Name` = :Name");
			$person_query->execute(array(":Name" => $name));
			$person_results = $person_query->fetchAll();
			
			if (count($person_results) > 0) {
				$this->person_data = $person_results[0];
			} else {
				throw new Exception("Invalid person ID");
			}
		}
		
		public function reloadData($id = "") {
			if (empty($id)) {
				$id = $this->getID();
			}
			
			$person_query = $this->con->prepare("SELECT * FROM `people` WHERE `ID` = :ID");
			$person_query->execute(array(":ID" => $id));
			$person_results = $person_query->fetchAll();
			
			if (count($person_results) > 0) {
				$this->person_data = $person_results[0];
			} else {
				throw new Exception("Invalid person ID");
			}
		}
		
		private function updateValue($field, $value) {
			try {
				$update_query = $this->con->prepare("UPDATE `people` SET `" . $field . "` = :Value WHERE `ID` = :ID");
				$update_query->execute(
					array(
						":Value" => $value,
						":ID" => $this->getID()
					)
				);
				
				$this->reloadData();
				
				return TRUE;
			} catch (PDOException $e) {
				return FALSE;
			}
		}
		
		public function getID() {
			return $this->person_data["ID"];
		}
		
		public function getName() {
			return $this->person_data["Name"];
		}
		
		public function setName($name) {
			return $this->updateValue("Name", $name);
		}
		
		public function getFirstName() {
			return $this->person_data["FirstName"];
		}
		
		public function setFirstName($firstname) {
			return $this->updateValue("FirstName", $firstname);
		}
		
		public function getLastName() {
			return $this->person_data["LastName"];
		}
		
		public function setLastName($lastname) {
			return $this->updateValue("LastName", $lastname);
		}
		
		public function getFacebook() {
			return $this->person_data["Facebook"];
		}
		
		public function setFacebook($facebook) {
			return $this->updateValue("Facebook", $facebook);
		}
		
		public function getTwitter() {
			return $this->person_data["Twitter"];
		}
		
		public function setTwitter($twitter) {
			return $this->updateValue("Twitter", $twitter);
		}
		
		public function getTwitch() {
			return $this->person_data["Twitch"];
		}
		
		public function setTwitch($twitch) {
			return $this->updateValue("Twitch", $twitch);
		}
		
		public function getGooglePlus() {
			return $this->person_data["GooglePlus"];
		}
		
		public function setGooglePlus($googleplus) {
			return $this->updateValue("GooglePlus", $googleplus);
		}
		
		public function getYouTube() {
			return $this->person_data["YouTube"];
		}
		
		public function setYouTube($youtube) {
			return $this->updateValue("YouTube", $youtube);
		}
		
		public function getReddit() {
			return $this->person_data["Reddit"];
		}
		
		public function setReddit($reddit) {
			return $this->updateValue("Reddit", $reddit);
		}
		
		public function getURL() {
			return $this->person_data["URL"];
		}
		
		public function setURL($url) {
			return $this->updateValue("URL", $url);
		}
		
		public function getOverview() {
			return $this->person_data["Overview"];
		}
		
		public function setOverview($overview) {
			return $this->updateValue("Overview", $overview);
		}
	}

?>