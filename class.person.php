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
		
		public function getGender() {
			return $this->person_data["Gender"];
		}
		
		public function setGender($gender) {
			return $this->updateValue("Gender", $gender);
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
		
		public function getSocialLinks() {
			$social_links = array();
			if ($this->getYouTube() != "") {
				$social_link = array();
				$social_link["Name"] = "YouTube";
				$social_link["Link"] = "https://www.youtube.com/channel/" . $this->getYouTube();
				$social_link["Image"] = "youtube.png";
				$social_links[] = $social_link;
			}
			if ($this->getTwitch() != "") {
				$social_link = array();
				$social_link["Name"] = "Twitch";
				$social_link["Link"] = "http://www.twitch.tv/" . $this->getTwitch();
				$social_link["Image"] = "twitch.png";
				$social_links[] = $social_link;
			}
			if ($this->getFacebook() != "") {
				$social_link = array();
				$social_link["Name"] = "Facebook";
				$social_link["Link"] = "https://www.facebook.com/" . $this->getFacebook();
				$social_link["Image"] = "facebook.png";
				$social_links[] = $social_link;
			}
			if ($this->getTwitter() != "") {
				$social_link = array();
				$social_link["Name"] = "Twitter";
				$social_link["Link"] = "https://twitter.com/account/redirect_by_id/" . $this->getTwitter();
				$social_link["Image"] = "twitter.png";
				$social_links[] = $social_link;
			}
			if ($this->getReddit() != "") {
				$social_link = array();
				$social_link["Name"] = "reddit";
				$social_link["Link"] = "http://www.reddit.com/user/" . $this->getReddit();
				$social_link["Image"] = "reddit.png";
				$social_links[] = $social_link;
			}
			if ($this->getGooglePlus() != "") {
				$social_link = array();
				$social_link["Name"] = "Google Plus";
				$social_link["Link"] = "https://plus.google.com/" . $this->getGooglePlus();
				$social_link["Image"] = "googleplus.png";
				$social_links[] = $social_link;
			}
			return $social_links;
		}
		
		public function getRecentYouTubeVideos() {
			if ($this->getYouTube() != "") {
				$youtube_results = json_decode(file_get_contents("https://gdata.youtube.com/feeds/users/" . $this->getYouTube() . "/uploads?alt=json&max-results=5"), true);
				
				$youtube_videos = array();
				foreach ($youtube_results["feed"]["entry"] as $video_result) {
					$youtube_video = array();
					$youtube_video["Title"] = $video_result["title"]["\$t"];
					$youtube_video["Link"] = $video_result["link"][0]["href"];
					$youtube_video["Comments"] = $video_result["gd\$comments"]["gd\$feedLink"]["countHint"];
					$youtube_video["Thumbnail"] = $video_result["media\$group"]["media\$thumbnail"][0]["url"];
					
					$init = $video_result["media\$group"]["yt\$duration"]["seconds"];
					$hours = floor($init / 3600);
					$minutes = floor(($init / 60) % 60);
					$seconds = $init % 60;
					$youtube_video["Duration"] = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
						
					$youtube_videos[] = $youtube_video;
				}
				
				return $youtube_videos;
			} else {
				return array();
			}
		}
	}

?>