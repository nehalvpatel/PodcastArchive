<?php

session_start();

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

require_once("src/backend/Podcast.php");
require_once("src/backend/Episode.php");
require_once("src/backend/Person.php");
require_once("src/backend/Author.php");
require_once("src/backend/Timestamp.php");
require_once("src/backend/Utilities.php");
require_once("src/backend/Log.php");

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

// Loading data for the pages
$home = false;
$feed = "http://painkilleralready.podbean.com/feed/";
$description = $Podcast->getDescription();

// Some meta data
$gplus = "107397414095793132493";
$twitter = "PKA_Archive";
$creator = "nehalvpatel";