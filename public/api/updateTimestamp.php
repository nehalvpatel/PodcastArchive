<?php

require_once("../../config.php");

$output = array();
$errors = array();
$successes = array();

if (isset($_SESSION["username"])) {
    if (empty($_POST["id"]) || empty($_POST["value"])) {
        $errors[] = "Please ensure all fields have values.";
    } else {
        $timestamp = new Timestamp($con, $_POST["id"]);

        $previous_values = $timestamp->getData();

        $timestamp->setValue($_POST["value"]);
        $timestamp->setURL($_POST["url"]);

        $new_values = $timestamp->getData();

        $Log->Log("updateTimestamp", $_POST["id"], json_encode($new_values), json_encode($previous_values));

        $successes[] = "The timestamp was updated.";

        $episode = $timestamp->getEpisode();

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