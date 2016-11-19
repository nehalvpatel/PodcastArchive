<?php

	class Episode {
		private $con;
		private $episode_data;
		
		public function __construct($con) {
			$this->con = $con;
			unset($con);
		}
		
		public function initWithIdentifier($identifier) {
			$episode_query = $this->con->prepare("SELECT * FROM `episodes` WHERE `Identifier` = :Identifier");
			$episode_query->execute(array(":Identifier" => $identifier));
			$episode_results = $episode_query->fetchAll();
			
			if (count($episode_results) > 0) {
				$this->episode_data = $episode_results[0];
				$this->reloadTimestamps();
			} else {
				throw new Exception("Invalid episode identifier");
			}
		}
		
		public function initWithNumber($number) {
			$episode_query = $this->con->prepare("SELECT * FROM `episodes` WHERE `Number` = :Number");
			$episode_query->execute(array(":Number" => $number));
			$episode_results = $episode_query->fetchAll();
			
			if (count($episode_results) > 0) {
				$this->episode_data = $episode_results[0];
				$this->reloadTimestamps();
			} else {
				throw new Exception("Invalid episode number");
			}
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
			$timestamps_query = $this->con->prepare("SELECT * FROM `timestamps` WHERE `Episode` = :Episode AND `Deleted`=0 ORDER BY `Timestamp` ASC");
			$timestamps_query->execute(array(":Episode" => $this->getIdentifier()));
			$timestamps = $timestamps_query->fetchAll();
			
			if (count($timestamps) > 0) {
				$timeline_array = array();
				
				// If the first timestamp is far into the episode, add an intro timestamp.
				if ($timestamps[0]["Timestamp"] > 20) {
					$timeline_array[] = array(
						"HMS" => "00:00:00",
						"Value" => "Intro",
						"URL" => "",
						"Begin" => 0
					);
				}
				// We now find the end time value for each timestamp and add it to the timestamp's array element.
				foreach ($timestamps as $timestamp) {
					$init = $timestamp["Timestamp"];
					$hours = floor($init / 3600);
					$minutes = floor(($init / 60) % 60);
					$seconds = $init % 60;
					
					$timeline_array[] = array(
						"id" => $timestamp["ID"],
						"HMS" => sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds),
						"Value" => $timestamp["Value"],
						"URL" 	=> $timestamp["URL"],
						"Begin" => $timestamp["Timestamp"]
					);
					
					// Set the previous array element's finishing time to the currents starting time.
					if (isset($timeline_array[count($timeline_array) - 2])) {
						$timeline_array[count($timeline_array) - 2]["End"] = $timestamp["Timestamp"];
					}
					
					$last_timestamp = $timestamp["Timestamp"];
				}
				
				// The last timestamp ends when the episode ends.
				$timeline_array[count($timeline_array) - 1]["End"] = $this->getYouTubeLength();
				
				// We now find the length of each timestamp as a percentage of the full episode length.
				foreach ($timeline_array as $timeline_key => $timeline_element) {
					// Find size of timeline element.
					$timeline_element_size = $timeline_element["End"] - $timeline_element["Begin"];
					
					// Express the timeline size as a quotent of the full current episode size. The * 1.01 gives us some visual spacing to avoid timeline glitches.
					$timeline_element_quotent = $timeline_element_size / ($this->getYouTubeLength() * 1.01);
					
					// Multiply by 100 to express in percentage form and put the value into the $timeline_array array.
					$timeline_array[$timeline_key]["Width"] = $timeline_element_quotent * 100;
				}
				
				$this->episode_data["Timestamps"] = $timeline_array;
			} else {
				$this->episode_data["Timestamps"] = array();
			}
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
		
		public function getDate() {
			return $this->episode_data["Date"];
		}
		
		public function setDate($date) {
			return $this->updateValue("Date", $date);
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
		
		public function getSponsors() {
			return $this->episode_data["Sponsors"];
		}
		
		public function setSponsors($sponsors) {
			return $this->updateValue("Sponsors", $sponsors);
		}
		
		public function getLength() {
			return $this->episode_data["Length"];
		}
		
		public function setLength($length) {
			return $this->updateValue("Length", $length);
		}
		
		public function getYouTubeLength() {
			return $this->episode_data["YouTube Length"];
		}
		
		public function setYouTubeLength($youtubelength) {
			return $this->updateValue("YouTube Length", $youtubelength);
		}
		
		public function getBytes() {
			return $this->episode_data["Bytes"];
		}
		
		public function setBytes($bytes) {
			return $this->updateValue("Bytes", $bytes);
		}
		
		public function getDuration() {
			$hours = floor($this->getLength() / 3600);
			$minutes = floor(($this->getLength() / 60) % 60);
			$seconds = $this->getLength() % 60;
			
			return "T" . $hours . "H" . $minutes . "M" . $seconds . "S";
		}
		
		public function getYouTube() {
			return $this->episode_data["YouTube"];
		}
		
		public function setYouTube($youtube) {
			return $this->updateValue("YouTube", $youtube);
		}
		
		public function getPublished() {
			return $this->episode_data["Published"];
		}
		
		public function setPublished($published) {
			return $this->updateValue("Published", $published);
		}
		
		public function getReddit() {
			return $this->episode_data["Reddit"];
		}
		
		public function setReddit($reddit) {
			return $this->updateValue("Reddit", $reddit);
		}
		
		public function getTimelineAuthor() {
			return $this->episode_data["TimelineAuthor"];
		}
		
		public function setTimelineAuthor($timelineauthor) {
			return $this->updateValue("TimelineAuthor", $timelineauthor);
		}
		
		public function getTimestamps() {
			return $this->episode_data["Timestamps"];
		}
		
		public function addTimestamp($timestamp, $value, $url = "", $special = "0") {
			try {
				$timestamp_query = $this->con->prepare("INSERT INTO `timestamps` (`Episode`, `Timestamp`, `Value`, `URL`, `Special`) VALUES (:Episode, :Timestamp, :Value, :URL, :Special)");
				$timestamp_query->execute(array(
					":Episode" => $this->getIdentifier(),
					":Timestamp" => $timestamp,
					":Value" => $value,
					":URL" => $url,
					":Special" => $special
				));
				
				$this->reloadTimestamps();
				
				return TRUE;
			} catch (PDOException $e) {
				return FALSE;
			}
		}
	}

?>