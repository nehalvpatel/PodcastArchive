<?php

	header("Content-type: xml");

	require_once("config.php");
	
	echo '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
	
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
	<url>
		<loc><?php echo $domain; ?></loc>
		<changefreq>weekly</changefreq>
	</url>
<?php
	foreach ($Podcast->getEpisodes() as $episode) {
?>
	<url>
		<loc><?php echo $domain; ?>episode/<?php echo $episode["Number"]; ?></loc>
		<changefreq>monthly</changefreq>
	</url>
<?php
	}
?>
<?php
	foreach ($Podcast->getPeople() as $person) {
?>
	<url>
		<loc><?php echo $domain; ?>person/<?php echo $person["ID"]; ?></loc>
		<changefreq>monthly</changefreq>
	</url>
<?php
	}
?>
</urlset>