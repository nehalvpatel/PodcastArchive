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
		
		public function getContentURL() {
			$vid_info = file_get_contents("http://www.youtube.com/get_video_info?&video_id=" . $this->getYouTube());
			parse_str($vid_info, $vid_bits);
			
			$formats = explode(",", $vid_bits["url_encoded_fmt_stream_map"]);
			
			parse_str($formats[0], $format_bits);
			return urldecode($format_bits["url"]) . "&signature=" . $format_bits["sig"];
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
			return $this->episode_data["Timeline Author"];
		}
		
		public function setTimelineAuthor($timelineAuthor) {
			return $this->updateValue("Timline Author", $timelineAuthor);
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
		
		public function getHorizontalTimeline() {
			$timestamps = $this->getTimestamps();
			$timeline_array = array();
			
			// If the first timestamp is far into the episode, add an intro timestamp.
			if ($timestamps[0]["Timestamp"] > 20) {
				$timeline_array[] = array(
					"Begin" => 0,
					"Value" => "Intro",
					"URL" => ""
				);
			}
			
			$i = 0;
			foreach ($timestamps as $timestamp) {
				// Only allow text timestamps in the horizontal timeline.
				$timeline_array[] = array(
					"Begin" => $timestamp["Timestamp"],
					"Value" => $timestamp["Value"],
					"URL" => $timestamp["URL"]
				);
				
				// Set the previous array element's finishing time to the currents starting time.
				if (isset($timeline_array[count($timeline_array) - 2])) {
					$timeline_array[count($timeline_array) - 2]["End"] = $timestamp["Timestamp"];
				}
				
				$last_timestamp = $timestamp["Timestamp"];
			}
			
			// The last topic ends when the episode ends.
			$timeline_array[count($timeline_array) - 1]["End"] = $this->getYouTubeLength();
			
			// We now start printing the timeline.
			$toggler = true;
			foreach ($timeline_array as $timeline_key => $timeline_element) {
				// Find size of timeline element.
				$timeline_element_size = $timeline_element["End"] - $timeline_element["Begin"];
				
				// Express the timeline size as a quotent of the full current episode size.
				$timeline_element_quotent = $timeline_element_size / $this->getYouTubeLength();
				
				// Multiply by 100 to express in percentage form.
				$timeline_element_percentage = $timeline_element_quotent * 100;
				
				$timeline_array[$timeline_key]["Percent"] = $timeline_element_percentage;
				
				$i++;
			}
			
			return $timeline_array;
		}
	}

?>