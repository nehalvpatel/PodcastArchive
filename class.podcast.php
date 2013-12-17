<?php

	function simplexml_insert_after(SimpleXMLElement $insert, SimpleXMLElement $target) {
		$target_dom = dom_import_simplexml($target);
		$insert_dom = $target_dom->ownerDocument->importNode(dom_import_simplexml($insert), true);
		if ($target_dom->nextSibling) {
			return $target_dom->parentNode->insertBefore($insert_dom, $target_dom->nextSibling);
		} else {
			return $target_dom->parentNode->appendChild($insert_dom);
		}
	}
	
	class Podcast {
		// database
		private $con;
		private $table;
		
		// services
		private $blubrry;
		private $feedburner;
		private $subreddit;
		private $itunes;
		
		// archive.org
		private $collection;
		private $creator;
		private $subject;
		
		// meta
		private $prefix;
		private $name;
		private $description;
		
		// author
		private $authorname;
		private $authoremail;
		
		public function __construct($con) {
			$this->con = $con;
		}
		
		// database
		public function getTable() {
			return $this->table;
		}
		
		public function setTable($table) {
			$this->table = $table;
		}
		
		// services
		public function getBlubrry() {
			return $this->blubrry;
		}
		
		public function setBlubrry($blubrry) {
			$this->blubrry = $blubrry;
		}
		
		public function getFeedburner() {
			return $this->feedburner;
		}
		
		public function setFeedburner($feedburner) {
			$this->feedburner = $feedburner;
		}
		
		public function getSubreddit() {
			return $this->subreddit;
		}
		
		public function setSubreddit($subreddit) {
			$this->subreddit = $subreddit;
		}
		
		public function getItunes() {
			return $this->itunes;
		}
		
		public function setItunes($itunes) {
			$this->itunes = $itunes;
		}
		
		// archive.org
		public function getCollection() {
			return $this->collection;
		}
		
		public function setCollection($collection) {
			$this->collection = $collection;
		}
		
		public function getCreator() {
			return $this->creator;
		}
		
		public function setCreator($creator) {
			$this->creator = $creator;
		}
		
		public function getSubject() {
			return $this->subject;
		}
		
		public function setSubject($subject) {
			$this->subject = $subject;
		}
		
		// meta
		public function getPrefix() {
			return $this->prefix;
		}
		
		public function setPrefix($prefix) {
			$this->prefix = $prefix;
		}
		
		public function getName() {
			return $this->name;	
		}
		
		public function setName($name) {
			$this->name = $name;
		}
		
		public function getDescription() {
			return $this->description;
		}
		
		public function setDescription($description) {
			$this->description = $description;
		}
		
		// author
		public function getAuthorName() {
			return $this->authorname;
		}
		
		public function setAuthorName($authorname) {
			$this->authorname = $authorname;
		}
		
		public function getAuthorEmail() {
			return $this->authoremail;
		}
		
		public function setAuthorEmail($authoremail) {
			$this->authoremail = $authoremail;
		}
		
		// settings
		public function getSettings() {
			$settings_query = $this->con->prepare("SELECT * FROM `Settings`");
			$settings_query->execute();
			$settings_results = $settings_query->fetchAll();
			
			$settings = array(
				"Domain" => $settings_results[0]["Value"]
			);
			return $settings;
		}
		
		public function getLink() {
			if ($this->getCollection() == "") {
				$search = "https://archive.org/search.php?query=" . rawurlencode('creator:"' . $this->getCreator() . '" AND subject:"' . $this->getSubject() . '"&sort=-date');
				$search = str_replace("%3D", "=", $search);
				$search = str_replace("%26", "&", $search);
			} else {
				$search = "https://archive.org/details/" . $this->getCollection();
			}
			return $search;
		}
		
		public function trimEpisodeNumber($episode) {
			$episode = ltrim($episode, "0");
			
			if ($episode == "") {
				return "0";
			} else {
				return ltrim($episode, "0");
			}
		}
		
		public function padEpisodeNumber($episode) {
			$episode = $this->trimEpisodeNumber($episode);
			
			if ($episode == "0") {
				return "000";
			} else {
				if (is_numeric($episode) && floor($episode) != $episode) {
					$episode = str_pad($episode, 4, "0", STR_PAD_LEFT);
				} else {
					$episode = str_pad($episode, 3, "0", STR_PAD_LEFT);
				}
				
				return $episode;
			}
		}
		
		public function addEpisode($episode) {
			if (is_numeric($episode) && floor($episode) != $episode) {
				$episode = str_pad($episode, 3, "0");
			} else {
				$episode = str_pad($episode, 4, "0");
			}
			
			$json = json_decode(@file_get_contents("https://archive.org/details/" . $this->getPrefix() . "_" . $episode . "?output=json"), true);
			
			if (!is_null($json)) {
				if (json_last_error() == JSON_ERROR_NONE) {
					$title = $json["metadata"]["title"][0];
					$description = $json["metadata"]["description"][0];
					$identifier = $json["metadata"]["identifier"][0];
					$mp3 = str_replace("_", "-", strtolower($identifier)) . ".mp3";
					$length = $json["files"]["/" . $mp3]["length"];
					$size = $json["files"]["/" . $mp3]["size"];
					
					$hosts = array();
					$guests = array();
					foreach ($json["metadata"]["subject"] as $subject) {
						if ($subject != "Video Games") {
							if (($subject == "WoodysGamertag") || ($subject == "WingsofRedemption") || ($subject == "FPSKyle") || ($subject == "LeftyOX")) {
								$hosts[] = $subject;
							} else {
								$guests[] = $subject;
							}
						}
					}
					
					try {
						$insert_query = $this->con->prepare("INSERT INTO `" . $this->getTable() . "` (`Name`, `Title`, `Hosts`, `Guests`, `Length`, `Bytes`) VALUES (:Name, :Title, :Hosts, :Guests, :Length, :Bytes) ON DUPLICATE KEY UPDATE `Title` = :Title, `Hosts` = :Hosts, `Guests` = :Guests, `Length` = :Length, `Bytes` = :Bytes");
						$insert_query->execute(array(
							":Name" => $this->getPrefix() . "_" . $episode,
							":Title" => $title,
							":Hosts" => join($hosts, ","),
							":Guests" => join($guests, ","),
							":Length" => $length,
							":Bytes" => $size
						));
						
						return $length . " seconds, " . $size . " bytes";
					} catch (PDOException $e) {
						return "SQL_ERROR";
					}
				} else {
					return "JSON_ERROR";
				}
			} else {
				return "INVALID_EPISODE";
			}
		}
		
		public function getFeed() {
			if ($this->getCollection() == "") {
				$search = 'creator:"' . $this->getCreator() . '" AND subject:"' . $this->getSubject() . '"';
			} else {
				$search = "collection:" . $this->getCollection();
			}
			
			$search = urlencode($search);
			
			$feed = file_get_contents("https://archive.org/advancedsearch.php?q=" . $search . "&fl%5B%5D=identifier&sort%5B%5D=&sort%5B%5D=&sort%5B%5D=&rows=1000&page=1&callback=callback&save=yes&output=rss");
			$feed = str_replace(".&lt;/p&gt;&lt;p&gt;This item has files of the following types: Archive BitTorrent, Metadata, Ogg Vorbis, VBR MP3&lt;/p&gt;", "", $feed);
			$feed = str_replace(".&lt;/p&gt;&lt;p&gt;This item has files of the following types: Archive BitTorrent, Metadata, VBR MP3&lt;/p&gt;", "", $feed);
			$feed = str_replace(".&lt;/p&gt;&lt;p&gt;This item has files of the following types: Metadata, Ogg Vorbis, Unknown, VBR MP3&lt;/p&gt;", "", $feed);
			
			return $feed;
		}
		
		public function getLatestEpisode() {
			$latest_query = $this->con->prepare("SELECT `Name` FROM `" . $this->getTable() . "` ORDER BY `Name` DESC LIMIT 1");
			$latest_query->execute();
			$latest_results = $latest_query->fetchAll();
			
			return $latest_results[0]["Name"];
		}
		
		public function getEpisodes() {
			$info_query = $this->con->prepare("SELECT * FROM `" . $this->getTable() . "` ORDER BY `Name` DESC");
			$info_query->execute();
			$info_results = $info_query->fetchAll();
			
			$episodes = array();
			foreach ($info_results as $info) {
				$episodes[$info["Name"]] = array(
					"Title" => $info["Title"],
					"Hosts" => $info["Hosts"],
					"Guests" => $info["Guests"],
					"Length" => gmdate("H:i:s", $info["Length"]),
					"Byte" => $info["Bytes"],
					"YouTube" => $info["YouTube"]
				);
				
			}
			
			return $episodes;
		}
		
		public function parseFeed($feed) {
			$xml = simplexml_load_string($feed);
			
			$xml->channel->title = $this->getName();
			$xml->channel->link = $this->getLink();;
			$xml->channel->description = $this->getDescription();;
			
			unset($xml->channel->image[0]);
			unset($xml->channel->webMaster[0]);
			
			simplexml_insert_after(new SimpleXMLElement("<language>en-us</language>"), current($xml->xpath('//pubDate[last()]')));
			simplexml_insert_after(new SimpleXMLElement('<itunes:explicit xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">yes</itunes:explicit>'), current($xml->xpath('//pubDate[last()]')));
			simplexml_insert_after(new SimpleXMLElement('<itunes:owner xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"><itunes:email xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">' . $this->getAuthorEmail() . '</itunes:email><itunes:name xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">' . $this->getAuthorName() . '</itunes:name></itunes:owner>'), current($xml->xpath('//pubDate[last()]')));
			
			$episodes = $this->getEpisodes();
			
			foreach ($xml->channel->item as $podcast) {
				$episode_number = str_replace("https://archive.org/details/" . $this->getPrefix() . "_", "", $podcast->link);
				
				if (!array_key_exists($this->getPrefix() . "_" . $episode_number, $episodes)) {
					$this->addEpisode($episode_number);
					$episodes = $this->getEpisodes();
				}
				
				$podcast->addChild("itunes:duration", $episodes[$this->getPrefix() . "_" . $episode_number]["Length"], "http://www.itunes.com/dtds/podcast-1.0.dtd");
				
				$namespaces = $xml->getNamespaces(true);
				$mediacontent = $podcast->children($namespaces['media'])->content;
				$dom2 = dom_import_simplexml($mediacontent);
				$dom2->parentNode->removeChild($dom2);
				
				$guests_explosion = explode("Guests", $podcast->description);
				$podcast->description = "Guests" . $guests_explosion[1];
				foreach ($podcast->enclosure as $enclosure) {
					if ($enclosure["type"] == "application/x-bittorrent") {
						$dom = dom_import_simplexml($enclosure);
						$dom->parentNode->removeChild($dom);
					} else {
						$base_url = str_replace("/format=VBR+MP3&ignore=x.mp3", "", $enclosure["url"]);
						$episode_identifier = str_replace("https://archive.org/download/", "", $base_url);
						$mp3_identifier = strtolower(str_replace("_", "-", $episode_identifier));
						
						$mp3_url = "http://media.blubrry.com/" . $this->getBlubrry() . "/" . str_replace("https://", "", $base_url) . "/" . $mp3_identifier . ".mp3";
						$enclosure["url"] = $mp3_url;
						$enclosure["length"] = $episodes[$episode_identifier]["Byte"];
					}
				}
			}
			
			return $xml->saveXML();
		}
	}

?>