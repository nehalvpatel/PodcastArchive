<?php

require_once("../../config.php");

$output = array();
$errors = array();
$successes = array();

if (isset($_SESSION["username"])) {
    if (empty($_POST["id"])) {
        $errors[] = "Please ensure the relevant timestamp id is submitted.";
    } else {
        $stmt = $con->prepare("UPDATE `timestamps` SET `Deleted` = 1 WHERE `id` = :id");

        $result = $stmt->execute(array(
            ":id" => $_POST["id"]
        ));

        $Log->Log("deleteTimestamp", $_POST["id"]);

        if ($result) {
            $successes[] = "The timeline row was successfully removed.";
            $ts = new Timestamp($con, $_POST["id"]);
            $episode = $ts->getEpisode();

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
        } else {
            $errors[] = "There has been a MySQL error.";
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