<?php

require_once("../vendor/autoload.php");

// Initialize framework
$f3 = \Base::instance();
$f3->set("CACHE", true);
$f3->set("DEBUG", 0);

// Setting up the core
$f3->set("DB", new \DB\SQL("mysql:host=" . apache_getenv("DB_HOST") . ";dbname=" . apache_getenv("DB_NAME") . ";charset=utf8", apache_getenv("DB_USER"), apache_getenv("DB_PASS")));
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
$f3->set("feed", "http://painkilleralready.podbean.com/feed/");
$f3->set("description", $f3->get("Core")->getDescription());
$f3->set("base_domain", $f3->get("Utilities")->getBaseDomain());
$f3->set("domain", $f3->get("Utilities")->getDomain());
$f3->set("episodes", $f3->get("Core")->getEpisodes());
$f3->set("people", $f3->get("Core")->getPeople());

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
		$f3->set("type", "episode");
		$f3->set("current_episode", $f3->get("episodes")[count($f3->get("episodes")) - 1]);
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
		$f3->reroute("/episode/" . $f3->get("episodes")[array_rand($f3->get("episodes"))]->getNumber());
	}
, 0);

$f3->route("GET /episode/@number",
	function ($f3, $params) {
		$f3->set("type", "episode");
		
		if (!is_numeric($params["number"])) {
			$f3->error(404);
		} else {
			foreach ($f3->get("episodes") as $episode) {
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
		$f3->reroute("/person/" . $f3->get("people")[array_rand($f3->get("people"))]->getID());
	}
, 0);

$f3->route("GET /person/@number",
	function ($f3, $params) {
		$f3->set("type", "person");
		
		if (!is_numeric($params["number"])) {
			$f3->error(404);
		} else {
			foreach ($f3->get("people") as $person) {
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
		foreach ($f3->get("episodes") as $episode) {
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
		$f3->reroute("/content?id=" . $f3->get("domain") . "episode/" . $f3->get("episodes")[array_rand($f3->get("episodes"))]->getNumber());
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
				foreach ($f3->get("episodes") as $current_episode) {
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
				$feedback_query = $f3->get("DB")->prepare("INSERT INTO `feedback` (`issue`, `explanation`) VALUES (:issue, :explanation)");
				$feedback_query->bindValue(":issue", $_POST["issue"]);
				$feedback_query->bindValue(":explanation", $_POST["explanation"]);
				$feedback_result = $feedback_query->execute();
				
				if ($feedback_result) {
					$f3->set("success", "Thank you, your feedback has been received and our administrators will now work to solve the problem shortly.");
				} else {
					$errors[] = "There was a MySQL error, please try again.";
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

$f3->route("GET /admin",
	function ($f3) {
		session_start();
		$f3->set("page", "Login");
		$f3->set("title", "Admin Panel");
		if (isset($_SESSION["admin"], $_SESSION["id"]) && $_SESSION["admin"] != null && (int)$_SESSION["id"] <= 0) {
			$f3->set("loggedIn", true);
			header("Location: /admin/home");
		}
		else {
			$f3->set("loggedIn", false);
		}
		$errors = array();
		
		$f3->set("errors", $errors);
		$template = new Template;
		echo $template->render("../views/admin/base.tpl");
	}
, 600);

$f3->route("GET /admin/home",
	function ($f3) {
		session_start();
		$f3->set("page", "Home");
		$f3->set("title", "Admin Panel");		
		$errors = array();
		
		if (isset($_SESSION["admin"], $_SESSION["id"]) && $_SESSION["admin"] != null && (int)$_SESSION["id"] > 0) {
			$f3->set("loggedIn", true);
			
			$f3->set("type", "home");
			$f3->set("username", $_SESSION["admin"]);
		}
		else {
			$f3->set("loggedIn", false);
			header("Location: /admin/login");
		}
		
		$f3->set("errors", $errors);
		$template = new Template;
		echo $template->render("../views/admin/base.tpl");
	}
, 600);

$f3->route("GET /admin/logout",
	function ($f3) {
		session_start();
		$f3->set("page", "Logout");
		$f3->set("title", "Admin Panel");
		$errors = array();
		
		$f3->set("loggedIn", false);
		if (isset($_SESSION["admin"], $_SESSION["id"]) && $_SESSION["admin"] != null && (int)$_SESSION["id"] > 0) {
			$_SESSION = array();
			session_destroy();
			$f3->set("success", "You have been logged out.");
		}
		else {
			$errors[] = "You are not logged in.";
		}
		
		$f3->set("errors", $errors);
		$template = new Template;
		echo $template->render("../views/admin/base.tpl");
	}
, 600);

$f3->route(
	array(
		"GET /admin/accounts",
		"POST /admin/accounts"
	),
	function ($f3) {
		session_start();
		$f3->set("page", "Accounts");
		$f3->set("title", "Admin Panel");		
		$errors = array();
		
		if (isset($_SESSION["admin"], $_SESSION["id"]) && $_SESSION["admin"] != null && (int)$_SESSION["id"] > 0) {
			$f3->set("loggedIn", true);			
			$f3->set("type", "accounts");
			$f3->set("adminType", $_SESSION["type"]);
			$f3->set("username", $_SESSION["admin"]);
			
			if (isset($_POST["form"]) && in_array($_POST["form"], array("add", "change"))) {
				if ($_POST["form"] == "add") {
					if ($_SESSION["type"] == 0) {
						if (isset($_POST["username"], $_POST["password"], $_POST["type"]) && !empty($_POST["username"]) && !empty($_POST["password"])) {
							$username = trim($_POST["username"]);
							$password = $_POST["password"];
							$type = (int)$_POST["type"];
							
							if (empty($username)) {
								$errors[] = "You cannot leave the username blank.";
							}
							
							if (empty($password)) {
								$errors[] = "You cannot leave the password blank.";
							}
							
							if ($type != 0 && $type != 1) {
								$errors[] = "Please choose a valid account type.";
							}
							
							if (count($errors) == 0) {
								$checkQuery = $f3->get("DB")->prepare("SELECT `Username` FROM `admins` WHERE `Username`=:user");
								$checkQuery->bindValue(":user", $username);
								$checkQuery->execute();
								
								if (count($checkQuery->fetchAll()) > 0) {
									$errors[] = "That username is already in use.";
								}
								else {
									$addQuery = $f3->get("DB")->prepare("INSERT INTO `admins` (`ID`,`Type`,`Username`,`Password`) VALUES (NULL, :type, :user, :pass)");
									$addQuery->bindValue(":type", $type);
									$addQuery->bindValue(":user", $username);
									$addQuery->bindValue(":pass", password_hash($password, PASSWORD_BCRYPT));
									$addQuery->execute();
									
									if ($addQuery) {
										$f3->set("success", "New account was added.");
									}
									else {
										$errors[] = "There was a MySQL error, please try again.";
									}
								}
							}
						}
						else {
							$errors[] = "Please make sure to fill out all the fields.";
						}
					}
					else {
						$errors[] = "You are not allowed to do that.";
					}
				}
				elseif ($_POST["form"] == "change") {
					if ($_SESSION["type"] == 0) {
						if (isset($_POST["username"], $_POST["password"]) && !empty($_POST["username"]) && !empty($_POST["password"])) {
							$username = trim($_POST["username"]);
							$password = password_hash($_POST["password"], PASSWORD_BCRYPT);
							
							$updateQuery = $f3->get("DB")->prepare("UPDATE `admins` SET `Password`=:pass WHERE `Username`=:user");
							$updateQuery->bindValue(":pass", $password);
							$updateQuery->bindValue(":user", $username);
							$updateQuery->execute();
							
							if ($updateQuery->rowCount() > 0) {
								$f3->set("success", "Password updated for specified user.");
							}
							else {
								$errors[] = "Couldn't find the specified user.";
							}
						}
						else {
							$errors[] = "Please make sure to fill out both fields.";
						}
					}
					else {
						if (isset($_POST["oldpass"], $_POST["newpass"]) && !empty($_POST["oldpass"]) && !empty($_POST["newpass"])) {
							$currentPass = $_POST["oldpass"];
							$newPass = password_hash($_POST["newpass"], PASSWORD_BCRYPT);
							
							$curQuery = $f3->get("DB")->prepare("SELECT `Password` FROM `admins` WHERE `ID`=:id");
							$curQuery->bindValue(":id", $_SESSION["id"]);
							$curQuery->execute();
							$queryResults = $curQuery->fetchAll();
							if (password_verify($currentPass, $queryResults[0]["Password"])) {
								$updateQuery = $f3->get("DB")->prepare("UPDATE `admins` SET `Password`=:pass WHERE `ID`=:id");
								$updateQuery->bindValue(":pass", $newPass);
								$updateQuery->bindValue(":id", $_SESSION["id"]);
								$updateQuery->execute();
								
								if ($updateQuery) {
									$f3->set("success", "Your password was changed.");
								}
								else {
									$errors[] = "There was a MySQL error, please try again.";
								}
							}
							else {
								$errors[] = "Your current password is incorrect.";
							}
						}
						else {
							$errors[] = "Please make sure to fill out both fields.";
						}
					}
				}
			}
		}
		else {
			$f3->set("loggedIn", false);
			header("Location: /admin/login");
		}
		
		$f3->set("errors", $errors);
		$template = new Template;
		echo $template->render("../views/admin/base.tpl");
	}
);

$f3->route(
	array(
		"POST /admin/login",
		"GET /admin/login"
	),
	function ($f3) {
		session_start();
		$f3->set("page", "Login");
		$f3->set("title", "Admin Panel");
		$errors = array();
		
		if (isset($_SESSION["admin"], $_SESSION["id"]) && $_SESSION["admin"] != null && (int)$_SESSION["id"] > 0) {
			$f3->set("loggedIn", true);
			$errors[] = "You are already logged in.";
		}
		else {
			$f3->set("loggedIn", false);			
			if (isset($_POST["username"], $_POST["password"]) && !empty($_POST["username"]) && !empty($_POST["password"])) {
				$username = trim($_POST["username"]);
				$password = $_POST["password"];
				
				$loginQuery = $f3->get("DB")->prepare("SELECT `ID`,`Type`,`Username`,`Password` FROM `admins` WHERE `Username`=:user");
				$loginQuery->bindValue(":user", $username);
				$loginQuery->execute();
				$loginData = $loginQuery->fetchAll();
				if (count($loginData) <= 0) {
					$errors[] = "Invalid username or password.";
				}
				else {
					if (password_verify($password, $loginData[0]["Password"])) {
						$_SESSION["admin"] = $loginData[0]["Username"];
						$_SESSION["id"] = $loginData[0]["ID"];
						$_SESSION["type"] = $loginData[0]["Type"];
						if ($_SESSION["type"] === null) {
							$_SESSION["type"] = 1;
						}
						header("Location: /admin/home");
					}
					else {
						$errors[] = "Invalid username or password.";
					}
				}
			}
			else {
				if (count($_POST) > 0) {
					$errors[] = "Please fill out both your username and password.";
				}
			}
		}
		
		$f3->set("errors", $errors);
		$template = new Template;
		echo $template->render("../views/admin/base.tpl");
	}
);

$f3->route("GET /api/episodes/add",
	function ($f3) {
		if ($_GET["key"] == apache_getenv("PKA_API_PW")) {
			$hosts = array(
				new \Tripod\Person(2, $f3->get("DB")),
				new \Tripod\Person(3, $f3->get("DB")),
				new \Tripod\Person(28, $f3->get("DB"))
			);
			
			$f3->get("Core")->addEpisode($_GET["number"], $hosts, array(), array(), $_GET["youtube"], $_GET["reddit"], apache_getenv("YT_API_KEY"));
		} else {
			$f3->error("Invalid password.");
		}
	}
);

$f3->route("GET /api/episodes/edit",
	function ($f3) {
		if ($_GET["key"] == apache_getenv("PKA_IFTTT_PW")) {
			$episode_number = trim(str_replace($f3->get("Core")->getName() . " #", "", $_GET["title"]));
		}
		
		$f3->set("current_episode", null);
		
		foreach ($f3->get("episodes") as $episode) {
			if ($episode_number == $episode->getNumber()) {
				$f3->set("current_episode", $episode);
			}
		}
		
		if ($f3->get("current_episode") !== null) {
			$f3->get("current_episode")->setDescription(trim($_GET["content"]));
		} else {
			$f3->error();
		}
	}
);

$f3->run();