<?php

require_once("../../config.php");

$episodes_query = $con->prepare("SELECT `Identifier` FROM `episodes` ORDER BY `Identifier` ASC");
$episodes_query->execute();			
$episodes_results = $episodes_query->fetchAll();

$latest = true;
$episodes = array();
foreach (array_reverse($episodes_results) as $episode_result) {
    $episode = new Episode($con, $episode_result["Identifier"]);

    $episode_data = array();
    $episode_data["Loaded"] = false;
    $episode_data["Highlighted"] = false;
    $episode_data["Identifier"] = $episode->getIdentifier();
    $episode_data["Number"] = (float)$episode->getNumber();
    $episode_data["DateTime"] = $episode->getDate();
    $episode_data["Date"] = date("F d, Y", strtotime($episode->getDate()));
    $episode_data["Reddit"] = $episode->getReddit();
    $episode_data["YouTube"] = $episode->getYouTube();

    foreach ($episode->getHosts() as $host) {
        $host_data = array();
        $host_data["ID"] = (int)$host->getID();
        $host_data["Name"] = $host->getName();
        $host_data["URL"] = $host->getURL();
        
        $episode_data["People"]["Hosts"][] = $host_data;
    }

    foreach ($episode->getGuests() as $guest) {
        $guest_data = array();
        $guest_data["ID"] = (int)$guest->getID();
        $guest_data["Name"] = $guest->getName();
        $guest_data["URL"] = $guest->getURL();
        
        $episode_data["People"]["Guests"][] = $guest_data;
    }

    foreach ($episode->getSponsors() as $sponsor) {
        $sponsor_data = array();
        $sponsor_data["ID"] = (int)$sponsor->getID();
        $sponsor_data["Name"] = $sponsor->getName();
        $sponsor_data["URL"] = $sponsor->getURL();
        
        $episode_data["People"]["Sponsors"][] = $sponsor_data;
    }

    $episode_data["Timeline"] = array(
        "Timestamps" => array()
    );
    $episode_data["Timelined"] = $episode->getTimelined();

    $episodes["episodes"][$episode_data["Identifier"]] = $episode_data;
    $episodes["map"][$episode->getNumber()] = $episode_data["Identifier"];

    if ($latest) {
        $episodes["latest"] = array(
            "Identifier" => $episode_data["Identifier"],
            "Number" => $episode_data["Number"]
        );
        $latest = false;
    }
    file_get_contents("http://localhost/api/episode.php?episode=" . $episode_data["Number"]);
}

header('Content-Type: application/json');
echo json_encode($episodes);
file_put_contents("json/episodes.json", json_encode($episodes));