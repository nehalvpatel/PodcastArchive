<?php

require_once("../../config.php");

if ($_GET["key"] == $_SERVER["PKA_IFTTT_PW"]) {
    $episode_number = trim(str_replace("Painkiller Already #", "", $_GET["title"]));

    $current_episode = null;
    foreach ($Podcast->getEpisodes() as $episode) {
        if ($episode_number == $episode->getNumber()) {
            $current_episode = $episode;
        }
    }
    
    if ($current_episode !== null) {
        $description = $_GET["content"];
        $description = str_replace("<<<", "", $description);
        $description = str_replace(">>>", "", $description);
        $description = strip_tags($description);
        $description = iconv("UTF-8", "ASCII//TRANSLIT", $description);
        $description = trim($description);
        
        $old_description = $current_episode->getDescription();
        $current_episode->setDescription($description);

        $Log->Log("descriptionUpdate", $current_episode->getIdentifier(), $description, $old_description);
    } else {
        $Log->Log("descriptionUpdateError", $_GET["title"], json_encode($_GET), "Invalid episode number.");
    }
} else {
    $Log->Log("descriptionUpdateError", $_GET["title"], json_encode($_GET), "Invalid password.");
}