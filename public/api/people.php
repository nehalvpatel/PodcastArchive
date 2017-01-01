<?php

require_once("../../config.php");

foreach ($Podcast->getPeople() as $CurrentPerson) {
    $output = array();

    $episodes = array();
    $host_count = 0;
    $guest_count = 0;
    $sponsor_count = 0;
    foreach ($Podcast->getEpisodes() as $episode) {
        foreach ($episode->getHosts() as $host) {
            if ($host->getID() == $CurrentPerson->getID()) {
                $episodes[] = $episode->getIdentifier();
                $host_count++;
            }
        }
        
        foreach ($episode->getGuests() as $guest) {
            if ($guest->getID() == $CurrentPerson->getID()) {
                $episodes[] = $episode->getIdentifier();
                $guest_count++;
            }
        }
        
        foreach ($episode->getSponsors() as $sponsor) {
            if ($sponsor->getID() == $CurrentPerson->getID()) {
                $episodes[] = $episode->getIdentifier();
                $sponsor_count++;
            }
        }
    }

    $output["ID"] = $CurrentPerson->getID();
    $output["Loaded"] = true;
    $output["Name"] = $CurrentPerson->getName();
    $output["Gender"] = $CurrentPerson->getGender();
    $output["Overview"] = $CurrentPerson->getOverview();
    $output["HostCount"] = $host_count;
    $output["GuestCount"] = $guest_count;
    $output["SponsorCount"] = $sponsor_count;
    $output["SocialLinks"] = $CurrentPerson->getSocialLinks();
    $output["Episodes"] = $episodes;

    file_put_contents("people/" . $output["ID"] . ".json", json_encode($output));
}