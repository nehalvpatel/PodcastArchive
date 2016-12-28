<?php

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

session_start();
require_once("src/Podcast.php");
require_once("src/Episode.php");
require_once("src/Person.php");
require_once("src/Author.php");
require_once("src/Timestamp.php");
require_once("src/Utilities.php");
require_once("src/Log.php");

// Setting up the core
$con = new PDO('mysql:host=' . $_SERVER["DB_HOST"] . ';dbname=' . $_SERVER["DB_NAME"] . ';charset=utf8', $_SERVER["DB_USER"], $_SERVER["DB_PASS"]);
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$Log = new Log($con);
$Podcast = new Podcast($con);
$Utilities = new Utilities();

// Some settings
$Podcast->setName("Painkiller Already");
$Podcast->setDescription("Commonly referred to as PKA, the podcast discusses current events, news, relives comedic stories and gives their perspective on life while throwing in their comedic twist to all discussions.");
$Podcast->setPrefix("PKA");
$base_domain = $Utilities->getBaseDomain();
$domain = $Utilities->getDomain();

// Get modified time to refresh CSS and JS if necessary
$css_modified_time = filemtime("css/main.css");
$js_modified_time = filemtime("js/main.js");

// Loading data for the pages
$home = false;
$feed = "http://painkilleralready.podbean.com/feed/";
$description = $Podcast->getDescription();

// Some meta data
$gplus = "107397414095793132493";
$twitter = "PKA_Archive";
$creator = "nehalvpatel";