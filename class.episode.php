<?php

	class Episode {
		private $con;
		private $episode_data;
		
		public function __construct($episode, $con) {
			$this->con = $con;
			unset($con);
			
			$this->reloadData($episode);
			$this->reloadTimestamps();
		}
		
		public function reloadData($identifier = "") {
			if (empty($identifier)) {
				$identifier = $this->getIdentifier();
			}
			
			$episode_query = $this->con->prepare("SELECT * FROM `episodes` WHERE `Identifier` = :Identifier");
			$episode_query->execute(array(":Identifier" => $identifier));
			$episode_results = $episode_query->fetchAll();
			
			if (count($episode_results) > 0) {
				$this->episode_data = $episode_results[0];
			} else {
				throw new Exception("Invalid episode identifier");
			}
		}
		
		public function reloadTimestamps() {
			$timestamps_query = $this->con->prepare("SELECT * FROM `timestamps` WHERE `Episode` = :Episode ORDER BY `Timestamp` ASC");
			$timestamps_query->execute(array(":Episode" => $this->getIdentifier()));
			$this->episode_data["Timestamps"] = $timestamps_query->fetchAll();
		}
		
		private function updateValue($field, $value) {
			try {
				$update_query = $this->con->prepare("UPDATE `episodes` SET `" . $field . "` = :Value WHERE `Identifier` = :Identifier");
				$update_query->execute(
					array(
						":Value" => $value,
						":Identifier" => $this->getIdentifier()
					)
				);
				
				$this->reloadData();
				
				return TRUE;
			} catch (PDOException $e) {
				return FALSE;
			}
		}
		
		public function getIdentifier() {
			return $this->episode_data["Identifier"];
		}
		
		public function getNumber() {
			return $this->episode_data["Number"];
		}
		
		public function getHosts() {
			return $this->episode_data["Hosts"];
		}
		
		public function setHosts($hosts) {
			return $this->updateValue("Hosts", $hosts);
		}
		
		public function getGuests() {
			return $this->episode_data["Guests"];
		}
		
		public function setGuests($guests) {
			return $this->updateValue("Guests", $guests);
		}
		
		public function getLength() {
			return $this->episode_data["Length"];
		}
		
		public function setLength($length) {
			return $this->updateValue("Length", $length);
		}
		
		public function getBytes() {
			return $this->episode_data["Bytes"];
		}
		
		public function setBytes($bytes) {
			return $this->updateValue("Bytes", $bytes);
		}
		
		public function getYouTube() {
			return $this->episode_data["YouTube"];
		}
		
		public function setYouTube($youtube) {
			return $this->updateValue("YouTube", $youtube);
		}
		
		public function getReddit() {
			return $this->episode_data["Reddit"];
		}
		
		public function setReddit($reddit) {
			return $this->updateValue("Reddit", $reddit);
		}
		
		public function getTimestamps() {
			return $this->episode_data["Timestamps"];
		}
		
		public function addTimestamp($type, $timestamp, $value, $url = "") {
			$type = ucfirst(strtolower($type));
			
			try {
				$timestamp_query = $this->con->prepare("INSERT INTO `timestamps` (`Episode`, `Type`, `Timestamp`, `Value`, `URL`) VALUES (:Episode, :Type, :Timestamp, :Value, :URL)");
				$timestamp_query->execute(array(
					":Episode" => $this->getIdentifier(),
					":Type" => $type,
					":Timestamp" => $timestamp,
					":Value" => $value,
					":URL" => $url
				));
				
				$this->reloadTimestamps();
				
				return TRUE;
			} catch (PDOException $e) {
				return FALSE;
			}
		}
	} 

?>