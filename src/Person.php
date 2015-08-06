<?php

namespace PainkillerAlready;

class Person
{
	// f3
	private $_f3;

	// database
	private $_connection;
	
	// instance
	private $_init_id;
	private $_data;
	
	public function __construct($initiator, $f3)
	{
		$this->_f3 = $f3;
		$this->_connection = $this->_f3->get("DB");
		
		if (is_array($initiator)) {
			$this->_init_id = $initiator["ID"];
			$this->_data = $initiator;
		} else {
			$this->_init_id = $initiator;
		}
	}
	
	public function checkData()
	{
		if (count($this->_data) == 0) {
			$this->reloadData($this->_init_id);
		}
	}
	
	public function reloadData($person_id = "")
	{
		if ($person_id === "") {
			$person_id = $this->getID();
		}
		
		if (is_numeric($person_id)) {
			$person_query = "SELECT * FROM `people` WHERE `ID` = :ID";
			$person_parameters = array(
				":ID" => $person_id
			);
			$person_results = $this->_connection->exec($person_query, $person_parameters, 600);
			
			if (count($person_results) > 0) {
				$this->_data = $person_results[0];
			} else {
				throw new \Exception("No person with that ID exists");
			}
		} else {
			throw new \Exception("Invalid person ID");
		}
	}
	
	private function _getValue($field)
	{
		$this->checkData();
		return $this->_data[$field];
	}
	
	private function _setValue($field, $value)
	{
		$this->checkData();
		try {
			$update_query = "UPDATE `people` SET {$field} = :Value WHERE `ID` = :ID";
			$update_parameters = array(
				":Value" => $value,
				":ID" => $this->getID()
			);
			$this->_connection->exec($update_query, $update_parameters);
			
			$this->reloadData();
			
			return true;
		} catch (\PDOException $e) {
			$error_info = array(
				"parameters" => $update_parameters,
				"error" => array(
					"mesage" => $e->getMessage(),
					"trace" => $e->getTrace()
				)
			);

			$this->_f3->get("log")->addError("Attempt at changing person " . $this->getID() . "'s `" . $field . "` to `" . $value . "`", $error_info);
			$this->_f3->error("Database error.");
		}
	}
	
	public function getID()
	{
		return $this->_getValue("ID");
	}
	
	public function getGender()
	{
		return $this->_getValue("Gender");
	}
	
	public function setGender($gender)
	{
		return $this->_setValue("Gender", $gender);
	}
	
	public function getName()
	{
		return $this->_getValue("Name");
	}
	
	public function setName($name)
	{
		return $this->_setValue("Name", $name);
	}
	
	public function getFirstName()
	{
		return $this->_getValue("FirstName");
	}
	
	public function setFirstName($firstname)
	{
		return $this->_setValue("FirstName", $firstname);
	}
	
	public function getLastName()
	{
		return $this->_getValue("LastName");
	}
	
	public function setLastName($lastname)
	{
		return $this->_setValue("LastName", $lastname);
	}
	
	public function getFacebook()
	{
		return $this->_getValue("Facebook");
	}
	
	public function setFacebook($facebook)
	{
		return $this->_setValue("Facebook", $facebook);
	}
	
	public function getTwitter()
	{
		return $this->_getValue("Twitter");
	}
	
	public function setTwitter($twitter)
	{
		return $this->_setValue("Twitter", $twitter);
	}
	
	public function getTwitch()
	{
		return $this->_getValue("Twitch");
	}
	
	public function setTwitch($twitch)
	{
		return $this->_setValue("Twitch", $twitch);
	}
	
	public function getGooglePlus()
	{
		return $this->_getValue("GooglePlus");
	}
	
	public function setGooglePlus($googleplus)
	{
		return $this->_setValue("GooglePlus", $googleplus);
	}
	
	public function getYouTube()
	{
		return $this->_getValue("YouTube");
	}
	
	public function setYouTube($youtube)
	{
		return $this->_setValue("YouTube", $youtube);
	}
	
	public function getReddit()
	{
		return $this->_getValue("Reddit");
	}
	
	public function setReddit($reddit)
	{
		return $this->_setValue("Reddit", $reddit);
	}
	
	public function getURL()
	{
		return $this->_getValue("URL");
	}
	
	public function setURL($url)
	{
		return $this->_setValue("URL", $url);
	}
	
	public function getOverview()
	{
		return $this->_getValue("Overview");
	}
	
	public function setOverview($overview)
	{
		return $this->_setValue("Overview", $overview);
	}
	
	public function getSocialLinks()
	{
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
	
	public function getRecentYouTubeVideos()
	{
		if ($this->getYouTube() != "") {
			$youtube_results = @file_get_contents("https://gdata.youtube.com/feeds/users/" . $this->getYouTube() . "/uploads?alt=json&max-results=6");
			
			if ($youtube_results === false) {
				return array();
			} else {
				$youtube_json = json_decode($youtube_results, true);
				
				if (isset($youtube_json["feed"]["entry"])) {
					$youtube_videos = array();
					foreach ($youtube_json["feed"]["entry"] as $video_result) {
						$youtube_video = array();
						$youtube_video["Title"] = $video_result["title"]["\$t"];
						$youtube_video["Link"] = $video_result["link"][0]["href"];
						
						if (isset($video_result["gd\$comments"])) {
							$youtube_video["Comments"] = $video_result["gd\$comments"]["gd\$feedLink"]["countHint"];
						} else {
							$youtube_video["Comments"] = "0";
						}
						
						$youtube_video["Thumbnail"] = $video_result["media\$group"]["media\$thumbnail"][0]["url"];
						$youtube_video["Duration"] = Utilities::convertToHMS($video_result["media\$group"]["yt\$duration"]["seconds"]);
						
						$youtube_videos[] = $youtube_video;
					}
					
					return $youtube_videos;
				} else {
					return array();
				}
			}
		} else {
			return array();
		}
	}
	
	public function __toString()
	{
		return $this->getName();
	}
}