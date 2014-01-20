<?php

	require_once("config.php");
	
	$search_results = array();
	if ((isset($_GET["query"])) && (!empty($_GET["query"]))) {
		$search_query = $con->prepare("SELECT DISTINCT `Episode` from `timestamps` WHERE MATCH(`Value`) AGAINST (:Value)");
		$search_query->execute(
			array(
				":Value" => trim($_GET["query"])
			)
		);
		
		foreach ($search_query->fetchAll() as $result) {
			$search_results[] = $result["Episode"];
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