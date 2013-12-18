<?php

	class Episode {
		private $con;
		private $episode_data;
		
		public function __construct($episode, $con) {
			$this->con = $con;
			unset($con);
			
			$episode_query = $this->con->prepare("SELECT * FROM `Episodes` WHERE `Name` = :Name");
			$episode_query->execute(array(":Name" => $episode));
			
			if ($episode_query->rowCount() > 0) {
				$episode_results = $episode_query->fetchAll();
				$this->episode_data = $episode_results[0];
				
				$this->reloadTimestamps();
			} else {
				throw new Exception("Invalid episode name");
			}
		}
		
		public function reloadData() {
			$episode_query = $this->con->prepare("SELECT * FROM `Episodes` WHERE `Name` = :Name");
			$episode_query->execute(array(":Name" => $this->getName()));
			$episode_results = $episode_query->fetchAll();
			$this->episode_data = $episode_results[0];
		}
		
		public function reloadTimestamps() {
			$timestamps_query = $this->con->prepare("SELECT * FROM `Timestamps` WHERE `Episode` = :Episode ORDER BY `Timestamp` ASC");
			$timestamps_query->execute(array(":Episode" => $this->getName()));
			$this->episode_data["Timestamps"] = $timestamps_query->fetchAll();
		}
		
		public function getName() {
			return $this->episode_data["Name"];
		}
		
		public function getEpisodeNumber() {
			$episode_explosion = explode("_", $this->getName());
			return $episode_explosion[1];
		}
		
		public function getTitle() {
			return $this->episode_data["Title"];
		}
		
		public function setTitle($title) {
			try {
				$title_query = $this->con->prepare("UPDATE `Episodes` SET `Title` = :Title WHERE `Name` = :Name");
				$title_query->execute(
					array(
						":Title" => $title,
						":Name" => $this->getName()
					)
				);
				
				$this->reloadData();
				
				return TRUE;
			} catch (PDOException $e) {
				return FALSE;
			}
		}
		
		public function getDescription() {
			return $this->episode_data["Description"];
		}
		
		public function setDescription($description) {
			try {
				$description_query = $this->con->prepare("UPDATE `Episodes` SET `Description` = :Description WHERE `Name` = :Name");
				$description_query->execute(
					array(
						":Description" => $description,
						":Name" => $this->getName()
					)
				);
				
				$this->reloadData();
				
				return TRUE;
			} catch (PDOException $e) {
				return FALSE;
			}
		}
		
		public function getLength() {
			return $this->episode_data["Length"];
		}
		
		public function setLength($length) {
			try {
				$length_query = $this->con->prepare("UPDATE `Episodes` SET `Length` = :Length WHERE `Name` = :Name");
				$length_query->execute(
					array(
						":Length" => $length,
						":Name" => $this->getName()
					)
				);
				
				$this->reloadData();
				
				return TRUE;
			} catch (PDOException $e) {
				return FALSE;
			}
		}
		
		public function getBytes() {
			return $this->episode_data["Bytes"];
		}
		
		public function setBytes($bytes) {
			try {
				$bytes_query = $this->con->prepare("UPDATE `Episodes` SET `Bytes` = :Bytes WHERE `Name` = :Name");
				$bytes_query->execute(
					array(
						":Bytes" => $bytes,
						":Name" => $this->getName()
					)
				);
				
				$this->reloadData();
				
				return TRUE;
			} catch (PDOException $e) {
				return FALSE;
			}
		}
		
		public function getYouTube() {
			return $this->episode_data["WoodysGamertag"];
		}
		
		public function setYouTube($youtube) {
			try {
				$youtube_query = $this->con->prepare("UPDATE `Episodes` SET `YouTube` = :YouTube WHERE `Name` = :Name");
				$youtube_query->execute(
					array(
						":YouTube" => $youtube,
						":Name" => $this->getName()
					)
				);
				
				$this->reloadData();
				
				return TRUE;
			} catch (PDOException $e) {
				return FALSE;
			}
		}
		
		public function getTimestamps() {
			return $this->episode_data["Timestamps"];
		}
		
		public function addTimestamp($type, $timestamp, $value, $url = "") {
			$type = ucfirst(strtolower($type));
			
			try {
				$timestamp_query = $this->con->prepare("INSERT INTO `Timestamps` (`Episode`, `Type`, `Timestamp`, `Value`, `URL`) VALUES (:Episode, :Type, :Timestamp, :Value, :URL)");
				$timestamp_query->execute(array(
					":Episode" => $this->getName(),
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