<?php

namespace PainkillerAlready;

class Podcast
{
	// f3
	private $_f3;

	// database
	private $_connection;
	
	// instance
	private $_data;
	private $_episodes_data;
	private $_people_data;
	
	public function __construct($f3)
	{
		$this->_f3 = $f3;
		$this->_connection = $this->_f3->get("DB");
		
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
		$authors_query = "SELECT * FROM `admins` ORDER BY `ID` ASC";
		$authors_results = $this->_connection->exec($authors_query, "", 600);
		
		$authors = array();
		foreach ($authors_results as $author) {
			$authors[] = new Author($author, $this->_f3);
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
			$add_query = "INSERT INTO `episodes` (`Identifier`, `Number`, `Date`, `Hosts`, `Guests`, `Sponsors`, `YouTube Length`, `YouTube`, `Published`, `Reddit`) VALUES (:Identifier, :Number, :Date, :Hosts, :Guests, :Sponsors, :YouTubeLength, :YouTube, :Published, :Reddit)";
			
			$add_parameters = array(
				":Identifier" => $this->getPrefix() . "_" . $number,
				":Number" => $number,
				":Date" => $created,
				":Hosts" => json_encode($hosts_list),
				":Guests" => json_encode($guests_list),
				":Sponsors" => json_encode($sponsors_list),
				":YouTubeLength" => $duration,
				":YouTube" => $youtube,
				":Published" => $published,
				":Reddit" => $reddit,
			);
			
			if ($this->_connection->exec($add_query, $add_parameters) === false) {
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

			$this->_f3->get("log")->addError("Attempt at adding episode", $error_info);
			$this->_f3->error("Database error.");
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
			$episodes_query = "SELECT `Identifier`, `Number`, `YouTube Length`, `Hosts`, `Guests`, `Sponsors` FROM `episodes` ORDER BY `Identifier` ASC";
			$episodes_results = $this->_connection->exec($episodes_query, "", 600);
			
			$timestamps_query = "SELECT `ID`, `Episode` FROM `timestamps` ORDER BY `Timestamp` ASC";
			$timestamps_results = $this->_connection->exec($timestamps_query, "", 600);

			$timelines = array();
			foreach ($timestamps_results as $timestamp) {
				$timelines[$timestamp["Episode"]][] = new Timestamp($timestamp, $this->_f3);
			}
			
			$reviews_query = "SELECT * FROM `reviews` ORDER BY `ID` ASC";
			$reviews_results = $this->_connection->exec($reviews_query, "", 600);
			
			$reviews = array();
			foreach ($reviews_results as $review) {
				$reviews[$review["Episode"]][] = new Review($review, $this->_f3);
			}
			
			$episodes = array();
			foreach ($episodes_results as $episode) {
				$hosts = json_decode($episode["Hosts"], true);
				$episode["Hosts"] = array();
				foreach ($hosts as $host) {
					$episode["Hosts"][] = new Person(array("ID" => $host), $this->_f3);
				}
				
				$guests = json_decode($episode["Guests"], true);
				$episode["Guests"] = array();
				foreach ($guests as $guest) {
					$episode["Guests"][] = new Person(array("ID" => $guest), $this->_f3);
				}
				
				$sponsors = json_decode($episode["Sponsors"], true);
				$episode["Sponsors"] = array();
				foreach ($sponsors as $sponsor) {
					$episode["Sponsors"][] = new Person(array("ID" => $sponsor), $this->_f3);
				}
				
				if (isset($timelines[$episode["Identifier"]])) {
					$episode["Timestamps"] = $timelines[$episode["Identifier"]];
				} else {
					$episode["Timestamps"] = array();
				}
				
				if (isset($reviews[$episode["Identifier"]])) {
					$episode["Reviews"] = $reviews[$episode["Identifier"]];
				} else {
					$episode["Reviews"] = array();
				}
				
				$episodes[] = new Episode($episode, $this->_f3);
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
			$people_query = "SELECT `ID` FROM `people` ORDER BY `ID` ASC";
			$people_results = $this->_connection->exec($people_query, "", 600);
			
			$people = array();
			foreach ($people_results as $person) {
				$people[] = new Person(array("ID" => $person["ID"]), $this->_f3);
			}
			
			return $people;
		}
	}
	
	public function getSearchResults($query)
	{
		$search_results = array();
		if (!empty($query)) {
			$search_query = "SELECT `Episode`, `Timestamp`, `Value` FROM `timestamps` WHERE REPLACE(`Value`, :Replace, '') LIKE :Value";
			$search_query_parameters = array(
				":Replace" => "'",
				":Value" => "%" . str_replace("'", "", trim($query) . "%")
			);
			$search_query_results = $this->_connection->exec($search_query, $search_query_parameters, 600);

			foreach ($search_query_results as $result) {
				$timestamp_data = array();
				$timestamp_data["Timestamp"] = $result["Timestamp"];
				$timestamp_data["Value"] = $result["Value"];
				$timestamp_data["HMS"] = Utilities::convertToHMS($result["Timestamp"]);
				
				$search_results[$result["Episode"]][] = $timestamp_data;
			}
		} else {
			$search_query = "SELECT * FROM `episodes`";
			$search_query_results = $this->_connection->exec($search_query, "", 600);

			foreach ($search_query_results as $result) {
				$search_results[] = $result["Identifier"];
			}
		}

		return $search_results;
	}
}