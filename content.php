<?php

	require_once("config.php");
	
	if (isset($_GET["id"])) {
		$id = trim($_GET["id"]);
		
		if (!empty($id)) {
			$id = urldecode($id);
			
			if (strpos($id, $domain) !== FALSE) {
				$id = str_replace($domain . "episode/", "", $id);
				$id = $Podcast->getPrefix() . "_" . $Podcast->padEpisodeNumber($id);
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
				$episode_data["Timeline"]["Author"] = $episode->getTimelineAuthor();
				
				$timeline_array = array();
				// If the first timestamp is far into the episode, add an intro timestamp.
				if($timestamps[0]["Timestamp"] > 20){
					$timeline_array[] = array(0, "Intro");
				}
				$i = 0;
				foreach ($timestamps as $timestamp) {
					// Only allow text timestamps in the horizontal timeline.
					if($timestamp["Type"] == "Text"){
						$timeline_array[] = array($timestamp["Timestamp"], $timestamp["Value"]);
					}
					// Set the previous array element's finishing time to the currents starting time.
					if (isset($timeline_array[count($timeline_array) - 2])) {
						$timeline_array[count($timeline_array) - 2][2] = $timestamp["Timestamp"];
					}
					$last_timestamp = $timestamp["Timestamp"];
				}
				// The last topic ends when the episode ends.
				$timeline_array[count($timeline_array) - 1][2] = $episode->getLength();
				
				// We now start printing the timeline.
				$toggler = true;
				foreach ($timeline_array as $id => $timeline_element) {
					// Find size of timeline element.
					$timeline_element_size = $timeline_element[2] - $timeline_element[0];
					
					// Express the timeline size as a quotent of the full current episode size.
					$timeline_element_quotent = $timeline_element_size / $episode->getLength();
					
					// Multiply by 100 to express in percentage form.
					$timeline_element_percentage = $timeline_element_quotent * 100;
					
					$init = $timeline_element[0];
					$hours = floor($init / 3600);
					$minutes = floor(($init / 60) % 60);
					$seconds = $init % 60;
					
					$timestamp_data = array();
					$timestamp_data["ID"] = $id;
					$timestamp_data["Seconds"] = $timeline_element[0];
					$timestamp_data["HMS"] = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
					$timestamp_data["Value"] = $timeline_element[1];
					$timestamp_data["Width"] = $timeline_element_percentage;
					$episode_data["Timeline"]["Timestamps"][] = $timestamp_data;
				}
			}
			
			echo json_encode($episode_data);
		}
	}
	
	function get_person_data($id) {
		global $con;
		$person = new Person($con);
		$person->initWithID($id);
		
		$person_data = array();
		$person_data["Name"] = $person->getName();
		$person_data["Image"] = $person->getImage();
		$person_data["URL"] = $person->getURL();
		
		return $person_data;
	}

?>