<?php

	class Author {
		private $con;
		private $author_data;
		
		public function __construct($con, $author_id) {
			$this->con = $con;
			unset($con);
			
			$this->reloadData($author_id);
		}
		
		public function reloadData($author_id = "") {
			if (empty($author_id)) {
				$author_id = $this->getID();
			}
			
			$author_query = $this->con->prepare("SELECT * FROM `admins` WHERE `ID` = :author_id");
			$author_query->execute(
				array(
					":author_id" => $author_id
				)
			);
			$author_results = $author_query->fetchAll();
			
			if (count($author_results) > 0) {
				$this->author_data = $author_results[0];
			} else {
				throw new Exception("Invalid author ID");
			}
		}
		
		private function updateValue($field, $value) {
			try {
				$update_query = $this->con->prepare("UPDATE `admins` SET `" . $field . "` = :Value WHERE `ID` = :ID");
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
			return $this->author_data["ID"];
		}
		
		public function getUsername() {
			return $this->author_data["Username"];
		}
		
		public function getName() {
			return $this->author_data["Name"];
		}
		
		public function setName($name) {
			return $this->updateValue("Name", $name);
		}
		
		public function getDisplayName() {
			if ($this->getName() != "") {
				return $this->getName();
			} else {
				return $this->getUsername();
			}
		}
		
		public function getPraise() {
			return $this->author_data["Praise"];
		}
		
		public function setPraise($praise) {
			return $this->updateValue("Praise", $praise);
		}
		
		public function getReddit() {
			return $this->author_data["Reddit"];
		}
		
		public function setReddit($reddit) {
			return $this->updateValue("Reddit", $reddit);
		}
		
		public function getLink() {
			return $this->author_data["Link"];
		}
		
		public function setLink($link) {
			return $this->updateValue("Link", $link);
		}
		
		public function getDisplayLink() {
			if ($this->getLink() != "") {
				return $this->getLink();
			} elseif ($this->getReddit() != "") {
				return "http://www.reddit.com/user/" . $this->getReddit();
			} else {
				return FALSE;
			}
		}
	}

?>