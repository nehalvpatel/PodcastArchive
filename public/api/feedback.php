<?php

require_once("../../config.php");

$output = array();
$errors = array();
$successes = array();

if (isset($_POST["issue"], $_POST["explanation"]) && !empty($_POST["issue"]) && !empty($_POST["explanation"])) {		
    $issueTypes = array(
        "timeline_typo",
        "browser_rendering",
        "website_content",
        "other"
    );
    
    if (!in_array($_POST["issue"], $issueTypes)) {
        $errors[] = "Please select a valid issue type.";
    }
    
    if (strlen($_POST["explanation"]) > 3000) {
        $errors[] = "Please make sure that your explanation isn't too long.";
    }
    
    if (empty($errors)) {
        try {
            $feedback_query = $con->prepare("INSERT INTO `feedback` (`issue`, `explanation`) VALUES (:issue, :explanation)");
            $feedback_query->bindValue(":issue", $_POST["issue"]);
            $feedback_query->bindValue(":explanation", $_POST["explanation"]);
            
            if ($feedback_query->execute()) {
                $output["type"] = "success";
                $successes[] = "New feedback added.";
            }
        } catch (\PDOException $e) {
            $errors[] = "A database error occured while submitting the feedback.";
        }
    }					
} else {
    $output["type"] = "error";
    $errors[] = "Please make sure you selected an issue and filled out the explanation.";
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