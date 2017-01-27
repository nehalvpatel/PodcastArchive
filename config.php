<?php

session_start();

require_once("src/backend/Podcast.php");
require_once("src/backend/Utilities.php");
require_once("src/backend/Log.php");

require_once("src/backend/Episode.php");
require_once("src/backend/Person.php");
require_once("src/backend/Author.php");
require_once("src/backend/Timestamp.php");

$con = new PDO("mysql:host=" . $_SERVER["DB_HOST"] . ";dbname=" . $_SERVER["DB_NAME"] . ";charset=utf8mb4", $_SERVER["DB_USER"], $_SERVER["DB_PASS"]);
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$Podcast = new Podcast($con);
$Log = new Log($con);