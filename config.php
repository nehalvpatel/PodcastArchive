<?php

	require_once("mysql.php");
	require_once("class.podcast.php");
	require_once("class.episode.php");
	require_once("class.person.php");
	
	$Podcast = new Podcast($con);
	
	$Podcast->setName("Painkiller Already");
	$Podcast->setDescription("Four gamers discuss games, current events, and tell a few stories.");
	$Podcast->setPrefix("PKA");
	
	$Podcast->setTable("episodes");
	$Podcast->setBlubrry("painkilleralready");
	$Podcast->setFeedburner("Painkiller_Already");
	$Podcast->setSubreddit("PKA");
	$Podcast->setItunes("692564838");
	
	$Podcast->setCollection("painkilleralready");
	
	$Podcast->setAuthorName("Nehal Patel");
	$Podcast->setAuthorEmail("nehal@itspatel.com");

?>