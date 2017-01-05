<?php

require_once("../../config.php");

$output = array();
$errors = array();
$successes = array();

if (isset($_SESSION["username"])) {
    if(empty($_POST["identifier"]) || empty($_POST["time"]) || empty($_POST["event"])){
        $errors[] = "Please ensure both a time and an event are submitted.";
    } else {
        if(count(explode(":", $_POST["time"])) == 3){
            $time = explode(":", $_POST["time"])[0]*3600 + explode(":", $_POST["time"])[1]*60 + explode(":", $_POST["time"])[2];
        } else if(count(explode(":", $_POST["time"])) == 2){
            $time = explode(":", $_POST["time"])[0]*60 + explode(":", $_POST["time"])[1];
        } else {
            $errors[] = "There was an error with the formatting of the timeline.";
        }

        if(empty($errors)){
            $episode = new Episode($con, $_POST["identifier"]);

            if(isset($_POST["url"])){
                $episode->addTimestamp($time, $_POST["event"], $_POST["url"]);			
                $Log->Log("addTimestamp", $episode->getIdentifier(), json_encode(array($_POST["time"], $_POST["event"], $_POST["url"])));				
            } else {
                $episode->addTimestamp($time, $_POST["event"]);
                $Log->Log("addTimestamp", $episode->getIdentifier(), json_encode(array($_POST["time"], $_POST["event"])));
            }

            $successes[] = "Timeline row has been successfully added.";

            $payload = array();
            $author = $episode->getTimelineAuthor();
            if ($author) {
                $payload["Timeline"]["Author"]["Name"] = $author->getDisplayName();
                $payload["Timeline"]["Author"]["Link"] = $author->getDisplayLink();
            } else {
                $payload["Timeline"]["Author"]["Name"] = "";
                $payload["Timeline"]["Author"]["Link"] = "";
            }

            $payload["Timelined"] = $episode->getTimelined();
            $payload["Timeline"]["Timestamps"] = array();
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
                
                $payload["Timeline"]["Timestamps"][] = $timestamp_data;
            }

            $output["payload"] = $payload;
        }
    }
} else {
    $errors[] = "You are not authenticated, please log in.";
}

if (count($errors) > 0) {
    $output["type"] = "error";
    $output["data"] = $errors;
} else {
    $output["type"] = "success";
    $output["data"] = $successes;
}

header('Content-Type: application/json');
echo json_encode($output);