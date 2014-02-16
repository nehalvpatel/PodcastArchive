<?php

	require_once("config.php");
	
	$search_results = array();
	if ((isset($_GET["query"])) && (!empty($_GET["query"]))) {
		$search_query = $con->prepare("SELECT DISTINCT `Episode`, `Timestamp`, `Value` FROM `timestamps` WHERE REPLACE(`Value`, :Replace, '') LIKE :Value");
		$search_query->execute(
			array(
				":Replace" => "'",
				":Value" => "%" . str_replace("'", "", trim($_GET["query"])) . "%"
			)
		);
		
		foreach ($search_query->fetchAll() as $result) {
			$timestamp_data = array();
			$timestamp_data["Timestamp"] = $result["Timestamp"];
			$timestamp_data["Value"] = $result["Value"];
			
			$search_results[$result["Episode"]] = $timestamp_data;
		}
	} else {
		$search_query = $con->prepare("SELECT * FROM `episodes`");
		$search_query->execute();
		
		foreach ($search_query->fetchAll() as $result) {
			$search_results[] = $result["Identifier"];
		}
	}
	
	echo json_encode($search_results);

?>