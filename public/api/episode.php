<?php

require_once("../../config.php");

$episode = new Episode($con, $_GET["episode"]);

$episode_data = array();
$episode_data["Loaded"] = true;
$episode_data["SearchResults"] = array();
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
    "Author" => array(
        "Name" => "",
        "Link" => ""
    ),
    "Timestamps" => array()
);

if ($episode->getTimelined()) {
    $episode_data["Timelined"] = true;
    $author = $episode->getTimelineAuthor();
    if ($author) {
        $episode_data["Timeline"]["Author"]["Name"] = $author->getDisplayName();
        $episode_data["Timeline"]["Author"]["Link"] = $author->getDisplayLink();
    } else {
        $episode_data["Timeline"]["Author"]["Name"] = "";
        $episode_data["Timeline"]["Author"]["Link"] = "";
    }

    $episode_data["Timeline"]["Timestamps"] = array();
    foreach ($episode->getTimestamps() as $timestamp) {
        $timestamp_data = array();
        $timestamp_data["ID"] = (int)$timestamp->getID();
        $timestamp_data["HMS"] = $timestamp->getTime();
        $timestamp_data["Value"] = $timestamp->getValue();
        $timestamp_data["URL"] = $timestamp->getURL();
        $timestamp_data["Begin"] = (int)$timestamp->getBegin();
        $timestamp_data["End"] = (int)$timestamp->getEnd();
        $timestamp_data["Width"] = $timestamp->getWidth() . "%";
        $timestamp_data["Right"] = $timestamp_data["Begin"] > ($episode->getYouTubeLength() / 2);
        $timestamp_data["Highlighted"] = false;
        
        $episode_data["Timeline"]["Timestamps"][] = $timestamp_data;
    }
} else {
    $episode_data["Timelined"] = false;
}

header('Content-Type: application/json');
echo json_encode($episode_data);