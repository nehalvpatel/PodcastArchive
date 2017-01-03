<?php

require_once("../../config.php");

$episodes_query = $con->prepare("SELECT `Identifier` FROM `episodes` ORDER BY `Identifier` ASC");
$episodes_query->execute();			
$episodes_results = $episodes_query->fetchAll();

$latest = true;
$output = array();
foreach (array_reverse($episodes_results) as $episode_result) {
    $episode = new Episode($con, $episode_result["Identifier"]);

    $episode_data = array();
    $episode_data["Identifier"] = $episode->getIdentifier();
    $episode_data["Number"] = (float)$episode->getNumber();
    $episode_data["YouTube"] = $episode->getYouTube();
    $episode_data["Timelined"] = $episode->getTimelined();

    $output["episodes"][$episode_data["Identifier"]] = $episode_data;

    if ($latest) {
        $output["latest"] = array(
            "Identifier" => $episode_data["Identifier"],
            "Number" => $episode_data["Number"]
        );
        $latest = false;
    }
}

$credits = array(
    "Loaded" => true
);
foreach ($Podcast->getAuthors() as $author) {
    if ($author->getType() == "0") {
        $credits["developers"][] = array(
            "DisplayLink" => $author->getDisplayLink(),
            "DisplayName" => $author->getDisplayName(),
            "Praise" => $author->getPraise()
        );
    } else {
        $credits["contributors"][] = array(
            "DisplayLink" => $author->getDisplayLink(),
            "DisplayName" => $author->getDisplayName(),
            "Praise" => $author->getPraise()
        );
    }
}

file_put_contents("credits.json", json_encode($credits));

$output["people"] = array();
foreach ($Podcast->getPeople() as $person) {
    $output["people"][$person->getID()] = array();
}

header('Content-Type: application/json');
echo json_encode($output);
file_put_contents("episodes/all.json", json_encode($output));