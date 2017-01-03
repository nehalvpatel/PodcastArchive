<?php

require_once("../../config.php");

$errors = array();
$successes = array();

if (empty($_POST["username"] || empty($_POST["password"]))) {
    $errors[] = "Please enter both a username and a password.";
} else {	
    $stmt = $con->prepare("SELECT * FROM admins WHERE Username = :username");
    $stmt->execute(array(
        ":username" => $_POST["username"],
    ));

    $result = $stmt->fetchAll();

    if (count($result) > 0) {
        if (password_verify($_POST["password"], $result[0]["Password"])) {
            $_SESSION["username"] = $result[0]["ID"];
        } else {
            $errors[] = "The entered username or password is incorrect.";
        }
    } else {
        $errors[] = "The entered username or password is incorrect.";
    }
}

$output = array();
if (count($errors) > 0) {
    $output["type"] = "error";
    $output["data"] = $errors;
} else {
    $output["type"] = "success";
    $output["data"] = $successes;
}

header('Content-Type: application/json');
echo json_encode($output);