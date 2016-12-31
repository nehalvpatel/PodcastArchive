<?php

require_once("../../config.php");

$results = array();
if (!isset($_GET["query"])) {
    $results = $Podcast->getSearchResults("");
} else {
    $results = $Podcast->getSearchResults($_GET["query"]);
}

$output = array();
$output["count"] = count($results);
$output["results"] = $results;

echo json_encode($output);