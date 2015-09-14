<?php

require_once("../vendor/autoload.php");

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Initialize framework
$f3 = \Base::instance();
$f3->set("CACHE", true);
$f3->set("DEBUG", 0);

// Setting up the core
$f3->set("log", new Logger("PKA"));
$f3->get("log")->pushHandler(new StreamHandler($_SERVER["LOG_LOCATION"]));
$f3->set("DB", new \DB\SQL("mysql:host=" . $_SERVER["DB_HOST"] . ";dbname=" . $_SERVER["DB_NAME"] . ";charset=utf8", $_SERVER["DB_USER"], $_SERVER["DB_PASS"]));
$f3->get("DB")->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
$f3->set("Core", new \PainkillerAlready\Podcast($f3));
$f3->set("Utilities", new \PainkillerAlready\Utilities());

// Some settings
$f3->get("Core")->setName("Painkiller Already");
$f3->get("Core")->setDescription("Commonly referred to as PKA, the podcast discusses current events, news, relives comedic stories and gives their perspective on life while throwing in their comedic twist to all discussions.");
$f3->get("Core")->setPrefix("PKA");

// Get modified time to refresh CSS and JS if necessary
$f3->set("css_modified_time", filemtime("css/main.css"), 0);
$f3->set("js_modified_time", filemtime("js/main.js"), 0);

// Loading data for the pages
$f3->set("home", false);
$f3->set("feed", "http://painkilleralready.podbean.com/feed/");
$f3->set("description", $f3->get("Core")->getDescription());
$f3->set("base_domain", $f3->get("Utilities")->getBaseDomain());
$f3->set("domain", $f3->get("Utilities")->getDomain());

// Some meta data
$f3->set("gplus", "107397414095793132493");
$f3->set("twitter", "PKA_Archive");
$f3->set("creator", "nehalvpatel");

$f3->set("ONERROR",
	function ($f3) {
		$f3->set("type", "error");
		$f3->set("canonical", $f3->get("domain") . "error");
		$f3->set("title", $f3->get("ERROR.code") . " · " . $f3->get("Core")->getName());
		
		$template = new Template;
		echo $template->render("../views/base.tpl");
	}
);

$f3->route("GET /",
	function ($f3) {
		$f3->set("home", true);
		$f3->set("type", "episode");
		$f3->set("current_episode", $f3->get("Core")->getLatestEpisode());
		$f3->set("canonical", $f3->get("domain") . "episode/" . $f3->get("current_episode")->getNumber());
		$f3->set("title", $f3->get("Core")->getName());
		$f3->set("source", "latest");
		
		if ($f3->get("current_episode")->getTimelined() === true) {
			$f3->set("timeline_author", $f3->get("current_episode")->getTimelineAuthor());
		}
		
		$template = new Template;
		echo $template->render("../views/base.tpl");
	}
, 60);

$f3->route("GET /episode/random",
	function ($f3) {
		$f3->reroute("/episode/" . $f3->get("Core")->getRandomEpisode()->getNumber());
	}
, 0);

$f3->route("GET /episode/@number",
	function ($f3, $params) {
		$f3->set("type", "episode");
		
		if (!is_numeric($params["number"])) {
			$f3->error(404);
		} else {
			foreach ($f3->get("Core")->getEpisodes() as $episode) {
				if ($params["number"] == $episode->getNumber()) {
					$f3->set("current_episode", $episode);
				}
			}
		}
		
		if (!$f3->exists("current_episode")) {
			$f3->error(404);
		}
		
		$guests = $f3->get("current_episode")->getGuests();
		if (count($guests) == 0) {
			$f3->set("guests_list", "Nobody");
		} else {
			if (count($guests) > 2) {
				$guests[count($guests) - 1] = "and " . strval($guests[count($guests) - 1]);
				$f3->set("guests_list", join(", ", array_map("strval", $guests)));
			} else {
				$f3->set("guests_list", join(" and ", array_map("strval", $guests)));
			}
		}
		
		if ($f3->get("current_episode")->getTimelined() === true) {
			$f3->set("timeline_author", $f3->get("current_episode")->getTimelineAuthor());
		}
		
		if ($f3->get("current_episode")->getDescription() != "") {
			$f3->set("description", $f3->get("current_episode")->getDescription());
		} else {
			$f3->set("description", "Guests: " . $f3->get("guests_list"));
		}
		
		$f3->set("canonical", $f3->get("domain") . "episode/" . $f3->get("current_episode")->getNumber());
		$f3->set("title", "Episode #" . $f3->get("current_episode")->getNumber() . " · " . $f3->get("Core")->getName());
		$f3->set("source", "get");
		
		$template = new Template;
		echo $template->render("../views/base.tpl");
	}
, 600);

$f3->route("GET /person/random",
	function ($f3) {
		$f3->reroute("/person/" . $f3->get("Core")->getRandomPerson()->getID());
	}
, 0);

$f3->route("GET /person/@number",
	function ($f3, $params) {
		$f3->set("type", "person");
		
		if (!is_numeric($params["number"])) {
			$f3->error(404);
		} else {
			foreach ($f3->get("Core")->getPeople() as $person) {
				if ($params["number"] == $person->getID()) {
					$f3->set("current_person", $person);
				}
			}
		}
		
		if (!$f3->exists("current_person")) {
			$f3->error(404);
		}
		
		$host_count = 0;
		$guest_count = 0;
		$sponsor_count = 0;
		foreach ($f3->get("Core")->getEpisodes() as $episode) {
			foreach ($episode->getHosts() as $host) {
				if ($host->getID() == $f3->get("current_person")->getID()) {
					$episode->setHighlighted(true);
					$host_count++;
				}
			}
			
			foreach ($episode->getGuests() as $guest) {
				if ($guest->getID() == $f3->get("current_person")->getID()) {
					$episode->setHighlighted(true);
					$guest_count++;
				}
			}
			
			foreach ($episode->getSponsors() as $sponsor) {
				if ($sponsor->getID() == $f3->get("current_person")->getID()) {
					$episode->setHighlighted(true);
					$sponsor_count++;
				}
			}
		}
		
		$f3->set("host_count", $host_count);
		$f3->set("guest_count", $guest_count);
		$f3->set("sponsor_count", $sponsor_count);
		$f3->set("recent_videos", $f3->get("current_person")->getRecentYouTubeVideos());
		$f3->set("social_links", $f3->get("current_person")->getSocialLinks());
		
		$f3->set("description", $f3->get("current_person")->getOverview());
		$f3->set("canonical", $f3->get("domain") . "episode/" . $f3->get("current_person")->getID());
		$f3->set("title", $f3->get("current_person")->getName() . " · " . $f3->get("Core")->getName());
		
		$template = new Template;
		echo $template->render("../views/base.tpl");
	}
, 600);

$f3->route("GET /content/random",
	function ($f3) {
		$f3->reroute("/content?id=" . $f3->get("domain") . "episode/" . $f3->get("Core")->getRandomEpisode()->getIdentifier());
	}
, 0);

$f3->route("GET /content",
	function ($f3) {
		if (isset($_GET["id"])) {
			$id = trim($_GET["id"]);

			if (!empty($id)) {
				$id = urldecode($id);

				if (strpos($id, $f3->get("domain")) !== FALSE) {
					$id = str_replace($f3->get("domain") . "episode/", "", $id);
					
					if (strpos($f3->get("Core")->getPrefix(), $id) === FALSE) {
						if (is_numeric($id)) {
							$id = $f3->get("Core")->getPrefix() . "_" . $f3->get("Utilities")->padEpisodeNumber($id);
						}
					} else {
						$id = $f3->get("Core")->getPrefix() . "_" . $f3->get("Utilities")->padEpisodeNumber($id);
					}
				}
				
				$episode = null;
				foreach ($f3->get("Core")->getEpisodes() as $current_episode) {
					if ($current_episode->getIdentifier() == $id) {
						$episode = $current_episode;
					}
				}
				
				$episode_data = array();
				$episode_data["Identifier"] = $episode->getIdentifier();
				$episode_data["Number"] = $episode->getNumber();
				$episode_data["DateTime"] = $episode->getDate();
				$episode_data["Date"] = date("F d, Y", strtotime($episode->getDate()));
				$episode_data["Reddit"] = $episode->getReddit();
				$episode_data["YouTube"] = $episode->getYouTube();
				$episode_data["YouTubeLength"] = $episode->getYouTubeLength();
				$episode_data["Link"] = $f3->get("domain") . "episode/" . $episode->getNumber();
				
				foreach ($episode->getHosts() as $host) {
					$host_data = array();
					$host_data["ID"] = $host->getID();
					$host_data["Name"] = $host->getName();
					$host_data["URL"] = $host->getURL();
					
					$episode_data["People"]["Hosts"][] = $host_data;
				}
				
				foreach ($episode->getGuests() as $guest) {
					$guest_data = array();
					$guest_data["ID"] = $guest->getID();
					$guest_data["Name"] = $guest->getName();
					$guest_data["URL"] = $guest->getURL();
					
					$episode_data["People"]["Guests"][] = $guest_data;
				}
				
				foreach ($episode->getSponsors() as $sponsor) {
					$sponsor_data = array();
					$sponsor_data["ID"] = $sponsor->getID();
					$sponsor_data["Name"] = $sponsor->getName();
					$sponsor_data["URL"] = $sponsor->getURL();
					
					$episode_data["People"]["Sponsors"][] = $sponsor_data;
				}

				$episode_data["Timeline"] = array();
				if ($episode->getTimelined()) {
					$author = $episode->getTimelineAuthor();
					$episode_data["Timeline"]["Author"]["Name"] = $author->getDisplayName();
					$episode_data["Timeline"]["Author"]["Link"] = $author->getDisplayLink();
					
					$episode_data["Timeline"]["Timestamps"] = array();
					foreach ($episode->getTimestamps() as $timestamp) {
						$timestamp_data = array();
						$timestamp_data["HMS"] = $timestamp->getTime();
						$timestamp_data["Value"] = $timestamp->getValue();
						$timestamp_data["URL"] = $timestamp->getURL();
						$timestamp_data["Begin"] = $timestamp->getBegin();
						$timestamp_data["End"] = $timestamp->getEnd();
						$timestamp_data["Width"] = $timestamp->getWidth();
						
						$episode_data["Timeline"]["Timestamps"][] = $timestamp_data;
					}
				}

				echo json_encode($episode_data);
			}
		}
	}
, 600);

$f3->route("GET /search",
	function ($f3) {
		if (!isset($_GET["query"])) {
			echo json_encode($f3->get("Core")->getSearchResults(""));
		} else {
			echo json_encode($f3->get("Core")->getSearchResults($_GET["query"]));
		}
	}
, 600);

$f3->route("GET /credits",
	function ($f3) {
		$f3->set("type", "credits");
		$f3->set("canonical", $f3->get("domain") . "credits");
		$f3->set("title", "Developers and Contributors · " . $f3->get("Core")->getName());
		$f3->set("description", "");

		$developers = array();
		$contributors = array();
		foreach ($f3->get("Core")->getAuthors() as $author) {
			if ($author->getType() == "0") {
				$developers[] = $author;
			} else {
				$contributors[] = $author;
			}
		}
		$f3->set("developers", $developers);
		$f3->set("contributors", $contributors);
		
		$template = new Template;
		echo $template->render("../views/base.tpl");
	}
, 600);

$f3->route("GET /feedback",
	function ($f3) {
		$f3->set("type", "feedback");
		$f3->set("canonical", $f3->get("domain") . "feedback");
		$f3->set("title", "Feedback · " . $f3->get("Core")->getName());
		$f3->set("description", "");

		$template = new Template;
		echo $template->render("../views/base.tpl");
	}
, 600);

$f3->route("POST /feedback",
	function ($f3) {
		$f3->set("type", "feedback");
		$f3->set("canonical", $f3->get("domain") . "feedback");
		$f3->set("title", "Feedback · " . $f3->get("Core")->getName());
		
		if (isset($_POST["issue"], $_POST["explanation"]) && !empty($_POST["issue"]) && !empty($_POST["explanation"])) {		
			$issueTypes = array(
				"timeline_typo",
				"browser_rendering",
				"website_content",
				"other"
			);
			
			if (!in_array($_POST["issue"], $issueTypes)) {
				$errors[] = "Please select a valid issue type.";
			}
			
			if (strlen($_POST["explanation"]) > 3000) {
				$errors[] = "Please make sure that your explanation isn't too long.";
			}
			
			if (empty($errors)) {
				try {
					$feedback_query = "INSERT INTO `feedback` (`issue`, `explanation`) VALUES (:issue, :explanation)";
					$feedback_paramaters = array(
						":issue" => $_POST["issue"],
						":explanation" => $_POST["explanation"]
					);
					$feedback_result = $f3->get("DB")->exec($feedback_query, $feedback_paramaters);
					
					if ($feedback_result !== false) {
						$f3->get("log")->addInfo("New feedback added.");
						$f3->set("success", "Thank you, your feedback has been received and our administrators will now work to solve the problem shortly.");
					}
				} catch (\PDOException $e) {
					$error_info = array(
						"parameters" => $feedback_paramaters,
						"error" => array(
							"mesage" => $e->getMessage(),
							"trace" => $e->getTrace()
						)
					);

					$f3->get("log")->addError("Attempt at adding feedback", $error_info);
					$f3->error("Database error.");
				}
			}					
		}
		else {
			$errors[] = "Please make sure you selected an issue and filled out the explanation.";
		}
				
		if (!empty($errors)) {
			$f3->set("errors", $errors);
		}
			
		$template = new Template;
		echo $template->render("../views/base.tpl");
	}
);

$f3->route("GET /opensearchdescription.xml",
	function ($f3) {
		$template = new Template;
		echo $template->render("../views/opensearchdescription.tpl", "application/xml");
	}
, 600);

$f3->route("GET /robots.txt",
	function ($f3) {
		$template = new Template;
		echo $template->render("../views/robots.tpl", "text/plain");
	}
, 600);

$f3->route("GET /sitemap.xml",
	function ($f3) {
		$template = new Template;
		echo $template->render("../views/sitemap.tpl", "application/xml");
	}
, 600);

$f3->route("GET /admin/episodes/timeline",
	function ($f3) {
		$template = new Template;
		echo $template->render("../views/admin/episodes/timeline.tpl");
	}
);

$f3->route("POST /admin/episodes/timeline",
	function ($f3) {
		if ($_POST["password"] != $_SERVER["TIMELINE_API_PW"])
		{
			$error_info = array(
				"parameters" => $_POST,
				"error" => array(
					"mesage" => "Invalid password",
				)
			);

			$f3->get("log")->addError("Attempt at adding episode timeline", $error_info);
			$f3->error("Invalid password.");
		}

		$f3->set("current_episode", null);

		foreach ($f3->get("Core")->getEpisodes() as $episode)
		{
			if ($_POST["episode"] == $episode->getNumber()) {
				$f3->set("current_episode", $episode);
			}
		}

		if ($f3->get("current_episode") === null)
		{
			$error_info = array(
				"parameters" => $_POST,
				"error" => array(
					"mesage" => "Invalid episode number",
				)
			);

			$f3->get("log")->addError("Attempt at adding episode timeline", $error_info);
			$f3->error("Invalid episode number.");
		}

		preg_match_all("/(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d) (.*)/", $_POST["timeline"], $output_array);

		$timestamp_count = count($output_array[4]);
		$submitted_count = 0;
		foreach ($output_array[4] as $timestamp_key => $timestamp_text)
		{
			$hour = $output_array[1][$timestamp_key] ?: "0";
			$minute = $output_array[2][$timestamp_key];
			$second = $output_array[3][$timestamp_key];
			$text = $output_array[4][$timestamp_key];
			$link = "";

			preg_match("/\[(.*)\]/", $text, $text_output);

			if (isset($text_output[1]))
			{
				$link = $text_output[1];
				$text = str_replace("[" . $link . "]", "", $text);
				$text = trim($text);
			}

			$timestamp = $second + (60 * $minute) + (3600 * $hour);

			if ($f3->get("current_episode")->addTimestamp($timestamp, $text, $link) === true)
			{
				$submitted_count++;
			}
		}

		if ($submitted_count > 0)
		{
			$author = new \PainkillerAlready\Author(24, $f3);
			$f3->get("current_episode")->setTimelineAuthor($author);
		}

		$f3->clear("CACHE");

		$f3->get("log")->addInfo("Timeline added for episode #" . $f3->get("current_episode")->getNumber() . "(" . $submitted_count . " of " . $timestamp_count . ")", array("parameters" => $_POST));
		echo $submitted_count . " out of " . $timestamp_count . " timestamps added to episode #" . $f3->get("current_episode")->getNumber();
	}
);

$f3->route("GET /api/episodes/add",
	function ($f3) {
		if ($_GET["key"] == $_SERVER["PKA_API_PW"]) {
			$hosts = array(
				new \PainkillerAlready\Person(2, $f3),
				new \PainkillerAlready\Person(3, $f3),
				new \PainkillerAlready\Person(28, $f3)
			);
			
			if ($f3->get("Core")->addEpisode($_GET["number"], $hosts, array(), array(), $_GET["youtube"], $_GET["reddit"], $_SERVER["YT_API_KEY"])) {
				$f3->get("log")->addInfo("New episode added (#" . $_GET["number"] . ")", array("parameters" => $_GET));
			}
		} else {
			$error_info = array(
				"parameters" => $_GET,
				"error" => array(
					"mesage" => "Invalid password",
				)
			);

			$f3->get("log")->addError("Attempt at adding episode", $error_info);
			$f3->error("Invalid password.");
		}
	}
);

$f3->route("GET /api/episodes/edit",
	function ($f3) {
		if ($_GET["key"] == $_SERVER["PKA_IFTTT_PW"]) {
			$episode_number = trim(str_replace($f3->get("Core")->getName() . " #", "", $_GET["title"]));
			$f3->set("current_episode", null);
			
			foreach ($f3->get("Core")->getEpisodes() as $episode) {
				if ($episode_number == $episode->getNumber()) {
					$f3->set("current_episode", $episode);
				}
			}
			
			if ($f3->get("current_episode") !== null) {
				$description = $_GET["content"];
				$description = str_replace("<<<", "", $description);
				$description = str_replace(">>>", "", $description);
				$description = strip_tags($description);
				$description = iconv("UTF-8", "ASCII//TRANSLIT", $description);
				$description = trim($description);
				
				if ($f3->get("current_episode")->setDescription($description)) {
					$f3->get("log")->addInfo("Description edited for episode #" . $f3->get("current_episode")->getNumber(), array("parameters" => $_GET));
				}
			} else {
				$error_info = array(
					"parameters" => $_GET,
					"error" => array(
						"mesage" => "Invalid episode number",
					)
				);

				$f3->get("log")->addError("Attempt at editing episode description", $error_info);
				$f3->error("Invalid episode number.");
			}
		} else {
			$error_info = array(
				"parameters" => $_GET,
				"error" => array(
					"mesage" => "Invalid password",
				)
			);

			$f3->get("log")->addError("Attempt at editing episode description", $error_info);
			$f3->error("Invalid password.");
		}
	}
);

$f3->run();