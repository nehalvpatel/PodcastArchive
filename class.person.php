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
		
		public function getRealName() {
			return $this->person_data["RealName"];
		}
		
		public function setRealName($realname) {
			return $this->updateValue("RealName", $realname);
		}
		
		public function getImage() {
			return $this->person_data["Image"];
		}
		
		public function setImage($image) {
			return $this->updateValue("Image", $image);
		}
		
		public function getYouTube() {
			return $this->person_data["YouTube"];
		}
		
		public function setYouTube($youtube) {
			return $this->updateValue("YouTube", $youtube);
		}
	}

?>