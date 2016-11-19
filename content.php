<?php

	require_once("config.php");
	
	if (isset($_GET["id"])) {
		$id = trim($_GET["id"]);
		
		if (!empty($id)) {
			$id = urldecode($id);
			if (strpos($id, $domain) !== FALSE) {
				$id = str_replace($domain . "episode/", "", $id);
				
				if (strpos($Podcast->getPrefix(), $id) === FALSE) {
					if (is_numeric($id)) {
						$id = $Podcast->getPrefix() . "_" . $Podcast->padEpisodeNumber($id);
					}
				} else {
					$id = $Podcast->getPrefix() . "_" . $Podcast->padEpisodeNumber($id);
				}
			}
			
			$cache = true;
			if ($id == "random") {
				$id = $Podcast->getRandomEpisode()["Identifier"];
				$cache = false;
			}
			
			$episode = new Episode($con);
			$episode->initWithIdentifier($id);
			
			$episode_data = array();
			
			$episode_data["Identifier"] = $episode->getIdentifier();
			$episode_data["Number"] = $episode->getNumber();
			$episode_data["DateTime"] = $episode->getDate();
			$episode_data["Date"] = date("F d, Y", strtotime($episode->getDate()));
			$episode_data["Reddit"] = $episode->getReddit();
			$episode_data["YouTube"] = $episode->getYouTube();
			$episode_data["YouTubeLength"] = $episode->getYouTubeLength();
			$episode_data["Cache"] = $cache;
			$episode_data["Link"] = $domain . "episode/" . $episode->getNumber();
			
			$hosts = json_decode($episode->getHosts(), true);
			foreach ($hosts as $host_id) {
				$episode_data["People"]["Hosts"][] = get_person_data($host_id);
			}
			
			$guests = json_decode($episode->getGuests(), true);
			foreach ($guests as $guest_id) {
				$episode_data["People"]["Guests"][] = get_person_data($guest_id);
			}
			
			$sponsors = json_decode($episode->getSponsors(), true);
			foreach ($sponsors as $sponsor_id) {
				$episode_data["People"]["Sponsors"][] = get_person_data($sponsor_id);
			}
			
			$episode_data["Timeline"] = array();
			$timestamps = $episode->getTimestamps();
			if (count($timestamps) > 0) {
				$author = new Author($con, $episode->getTimelineAuthor());
				
				if ($author) {
					$episode_data["Timeline"]["Author"]["Name"] = $author->getDisplayName();
					$episode_data["Timeline"]["Author"]["Link"] = $author->getDisplayLink();
				} else {
					$episode_data["Timeline"]["Author"]["Name"] = "";
					$episode_data["Timeline"]["Author"]["Link"] = "";
				}
				
				$episode_data["Timeline"]["Author"]["Name"] = $author->getDisplayName();
				$episode_data["Timeline"]["Author"]["Link"] = $author->getDisplayLink();
				$episode_data["Timeline"]["Timestamps"] = $timestamps;
			}
			
			echo json_encode($episode_data);
		}
	}
	
	function get_person_data($id) {
		global $con;
		$person = new Person($con);
		$person->initWithID($id);
		
		$person_data = array();
		$person_data["ID"] = $person->getID();
		$person_data["Name"] = $person->getName();
		$person_data["URL"] = $person->getURL();
		
		return $person_data;
	}

?>