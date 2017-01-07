<?php

require_once("../../config.php");

$output = array();
$errors = array();
$successes = array();

if (isset($_SESSION["username"])) {
    if (!empty($_POST["timeline"]) && !empty($_POST["identifier"])) {
        $_POST["timeline"] = trim($_POST["timeline"]);
        
        $timeline_array = explode("\n", $_POST["timeline"]);
        $detailed_timeline_array = array();
        $timeline_error = false;

        foreach ($timeline_array as $key => $timestamp) {
            // $detailed_timestamp is to be an array of [0] => HMS timestamp [1] => value [2] => url
            // example: [0] => "01:32:54" [1] => "The hosts talk about a topic" [2] => "http://www.relevanturl.com"
            $detailed_timestamp = explode(" ", $timestamp, 2); // Splits the line in two pieces by the first space

            if (count($detailed_timestamp) > 1) {
                if (strpos($detailed_timestamp[1], "http://") !== FALSE) { // Check for the existance of a URL (http)
                    $detailed_timestamp[2] = strstr($detailed_timestamp[1], "http://");
                    $detailed_timestamp[1] = strstr($detailed_timestamp[1], "http://", TRUE); // Removes url from timestamp value
                }
                if (strpos($detailed_timestamp[1], "https://") !== FALSE) { // Check for the existance of a URL (https)
                    $detailed_timestamp[2] = strstr($detailed_timestamp[1], "https://");
                    $detailed_timestamp[1] = strstr($detailed_timestamp[1], "https://", TRUE); // Removes url from timestamp value
                }

                // Convert timestamps from HMS form 01:32:54 to seconds only timestamp form 5574.
                if (count(explode(":", $detailed_timestamp[0])) == 3) {
                    $detailed_timeline_array[$key][0] = explode(":", $detailed_timestamp[0])[0]*3600 + explode(":", $detailed_timestamp[0])[1]*60 + explode(":", $detailed_timestamp[0])[2];
                } else if (count(explode(":", $detailed_timestamp[0])) == 2) {
                    $detailed_timeline_array[$key][0] = explode(":", $detailed_timestamp[0])[0]*60 + explode(":", $detailed_timestamp[0])[1];
                } else {
                    $errors[] = "There was an error with the formatting of the timeline.";
                }

                // Remove whitespace from value and url fields.
                $detailed_timeline_array[$key][1] = trim($detailed_timestamp[1]);
                if (isset($detailed_timestamp[2])) {
                    $detailed_timeline_array[$key][2] = trim($detailed_timestamp[2]);
                }
            } else {
                $timeline_error = true;
            }
        }

        if ($timeline_error) {
            $errors[] = "Please ensure all timestamps have a time, a topic, and optionally, a relevant url.";
        }

        // Submit the timeline data to the database.
        if (empty($errors)) {
            $episode = new Episode($con, $_POST["identifier"]);
            foreach($detailed_timeline_array as $timestamp) {
                if (isset($timestamp[2])) {
                    $episode->addTimestamp($timestamp[0], $timestamp[1], $timestamp[2]);							
                } else {
                    $episode->addTimestamp($timestamp[0], $timestamp[1]);
                }
            }

            $successes[] = "Timeline has been successfully added.";

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
                
                $payload["Timeline"]["Timestamps"][] = $timestamp_data;
            }

            $output["payload"] = $payload;

            // Get ID of current episode from Identifier
            //$stmt = $con->prepare("SELECT `id` FROM `episodes` WHERE `Identifier` = :identifier");
            //$stmt->execute(array(":identifier" => $episode->getIdentifier()));
            //$episode_id = $stmt->fetchAll()[0];
            //$Log->Log($_POST["form"], $episode_id, json_encode($detailed_timeline_array));
            $Log->Log("addTimeline", $episode->getIdentifier(), json_encode($detailed_timeline_array));
        }
    } else {
        $errors[] = "Please enter a timeline.";
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