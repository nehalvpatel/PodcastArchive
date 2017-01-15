<?php

require_once("../../config.php");

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

header('Content-Type: application/json');
echo json_encode($credits);