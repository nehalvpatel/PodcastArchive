<?php

class Podcast
{
	// database
	private $_connection;
	
	// instance
	private $_data;
	private $_episodes_data;
	private $_people_data;
	
	public function __construct($connection)
	{
		$this->_connection = $connection;
		
		$this->_data = array();
		$this->_episodes_data = array();
		$this->_people_data = array();
	}
	
	private function _getValue($field)
	{
		if (isset($this->_data[$field])) {
			return $this->_data[$field];
		} else {
			return "";
		}
	}
	
	private function _setValue($field, $value)
	{
		$this->_data[$field] = $value;
	}
	
	public function getPrefix()
	{
		return $this->_getValue("Prefix");
	}
	
	public function setPrefix($prefix)
	{
		$this->_setValue("Prefix", $prefix);
	}
	
	public function getName()
	{
		return $this->_getValue("Name");
	}
	
	public function setName($name)
	{
		$this->_setValue("Name", $name);
	}
	
	public function getDescription()
	{
		return $this->_getValue("Description");
	}
	
	public function setDescription($description)
	{
		$this->_setValue("Description", $description);
	}
	
	public function getAuthors()
	{
		$authors_query = $this->_connection->prepare("SELECT * FROM `admins` ORDER BY `ID` ASC");
		$authors_query->execute();
		$authors_results = $authors_query->fetchAll();
		
		$authors = array();
		foreach ($authors_results as $author) {
			$authors[] = new Author($this->_connection, $author);
		}
		
		return $authors; 
	}
	
	public function addEpisode($number, array $hosts, array $guests, array $sponsors, $youtube, $reddit, $yt_api_key)
	{
		if ($this->getPrefix() == "") {
			throw new \Exception("The prefix must be set before adding an episode.");
		}
		
		$hosts_list = array();
		foreach ($hosts as $host) {
			$hosts_list[] = (int)$host->getID();
		}
		
		$guests_list = array();
		foreach ($guests as $guest) {
			$guests_list[] = (int)$guest->getID();
		}
		
		$sponsors_list = array();
		foreach ($sponsors as $sponsor) {
			$sponsors_list[] = (int)$sponsor->getID();
		}
		
		$youtube_data = file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=$youtube&key=$yt_api_key");
		$youtube_json = json_decode($youtube_data, true);
		
		$published = $youtube_json["items"][0]["snippet"]["publishedAt"];
		$created = date("Y-m-d", strtotime($published));
		
		$youtube_duration = $youtube_json["items"][0]["contentDetails"]["duration"];
		$start = new \DateTime("@0"); // Unix epoch
		$start->add(new \DateInterval($youtube_duration));
		$duration = $start->format("U");
		
		try {
			$add_query = $this->_connection->prepare("INSERT INTO `episodes` (`Identifier`, `Number`, `Date`, `Hosts`, `Guests`, `Sponsors`, `YouTube Length`, `YouTube`, `Published`, `Reddit`) VALUES (:Identifier, :Number, :Date, :Hosts, :Guests, :Sponsors, :YouTubeLength, :YouTube, :Published, :Reddit)");
			
			$add_query->bindValue(":Identifier", $this->getPrefix() . "_" . $number);
			$add_query->bindValue(":Number", $number);
			$add_query->bindValue(":Date", $created);
			$add_query->bindValue(":Hosts", json_encode($hosts_list));
			$add_query->bindValue(":Guests", json_encode($guests_list));
			$add_query->bindValue(":Sponsors", json_encode($sponsors_list));
			$add_query->bindValue(":YouTubeLength", $duration);
			$add_query->bindValue(":YouTube", $youtube);
			$add_query->bindValue(":Published", $published);
			$add_query->bindValue(":Reddit", $reddit);
			
			if ($add_query->execute() === false) {
				return false;
			} else {
				return true;
			}
		} catch (\PDOException $e) {
			$error_info = array(
				"parameters" => $add_parameters,
				"error" => array(
					"mesage" => $e->getMessage(),
					"trace" => $e->getTrace()
				)
			);
		}
	}
	
	public function getLatestEpisode()
	{
		$episodes = $this->getEpisodes();
		
		if (count($episodes) > 0) {
			return $episodes[count($episodes) - 1];
		} else {
			throw new \Exception("No episode found.");
		}
	}
	
	public function getRandomEpisode()
	{
		$episodes = $this->getEpisodes();
		
		if (count($episodes) > 0) {
			return $episodes[array_rand($episodes)];
		} else {
			throw new \Exception("No episode found.");
		}
	}
	
	public function getEpisodes()
	{
		if (count($this->_episodes_data) > 0) {
			return $this->_episodes_data;
		} else {
			$episodes_query = $this->_connection->prepare("SELECT `Identifier`, `Number`, `YouTube Length`, `Hosts`, `Guests`, `Sponsors` FROM `episodes` ORDER BY `Identifier` ASC");
			$episodes_query->execute();			
			$episodes_results = $episodes_query->fetchAll();
			
			$timestamps_query = $this->_connection->prepare("SELECT `ID`, `Episode` FROM `timestamps` ORDER BY `Timestamp` ASC");
			$timestamps_query->execute();
			$timestamps_results = $timestamps_query->fetchAll();

			$timelines = array();
			foreach ($timestamps_results as $timestamp) {
				$timelines[$timestamp["Episode"]][] = new Timestamp($this->_connection, $timestamp);
			}
			
			$episodes = array();
			foreach ($episodes_results as $episode) {
				$hosts = json_decode($episode["Hosts"], true);
				$episode["Hosts"] = array();
				foreach ($hosts as $host) {
					$episode["Hosts"][] = new Person($this->_connection, array("ID" => $host));
				}
				
				$guests = json_decode($episode["Guests"], true);
				$episode["Guests"] = array();
				foreach ($guests as $guest) {
					$episode["Guests"][] = new Person($this->_connection, array("ID" => $guest));
				}
				
				$sponsors = json_decode($episode["Sponsors"], true);
				$episode["Sponsors"] = array();
				foreach ($sponsors as $sponsor) {
					$episode["Sponsors"][] = new Person($this->_connection, array("ID" => $sponsor));
				}
				
				if (isset($timelines[$episode["Identifier"]])) {
					$episode["Timestamps"] = $timelines[$episode["Identifier"]];
				} else {
					$episode["Timestamps"] = array();
				}
				
				$episodes[] = new Episode($this->_connection, $episode);
			}
			
			return $episodes;
		}
	}
	
	public function getRandomPerson()
	{
		$people = $this->getPeople();
		
		if (count($people) > 0) {
			return $people[array_rand($people)];
		} else {
			throw new \Exception("No person found.");
		}
	}
	
	public function getPeople()
	{
		if (count($this->_people_data) > 0) {
			return $this->_people_data;
		} else {
			$people_query = $this->_connection->prepare("SELECT `ID` FROM `people` ORDER BY `ID` ASC");
			$people_query->execute();
			$people_results = $people_query->fetchAll();
			
			$people = array();
			foreach ($people_results as $person) {
				$people[] = new Person($this->_connection, array("ID" => $person["ID"]));
			}
			
			return $people;
		}
	}
	
	public function getSearchResults($query)
	{
		$search_results = array();
		if (!empty($query)) {
			$search_query = $this->_connection->prepare("SELECT `Episode`, `Timestamp`, `Value` FROM `timestamps` WHERE REPLACE(`Value`, :Replace, '') LIKE :Value");
			$search_query->bindValue(":Replace", "'");
			$search_query->bindValue(":Value", "%" . str_replace("'", "", trim($query) . "%"));
			$search_query->execute();
			$search_query_results = $search_query->fetchAll();

			foreach ($search_query_results as $result) {
				$timestamp_data = array();
				$timestamp_data["Timestamp"] = $result["Timestamp"];
				$timestamp_data["Value"] = $result["Value"];
				$timestamp_data["HMS"] = Utilities::convertToHMS($result["Timestamp"]);
				
				$search_results[$result["Episode"]][] = $timestamp_data;
			}
		} else {
			$search_query = $this->_connection->prepare("SELECT * FROM `episodes`");
			$search_query->execute();
			$search_query_results = $search_query->fetchAll();

			foreach ($search_query_results as $result) {
				$search_results[] = $result["Identifier"];
			}
		}

		return $search_results;
	}
}