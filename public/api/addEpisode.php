<?php

require_once("../../config.php");

if ($_GET["key"] == $_SERVER["PKA_API_PW"]) {
    $hosts = array(
        new Person($con, 2),
        new Person($con, 3),
        new Person($con, 28)
    );
    
    $Podcast->addEpisode($_GET["number"], $hosts, array(), array(), $_GET["youtube"], $_GET["reddit"], $_SERVER["YT_API_KEY"]);
} else {
    echo "Invalid password.";
}