<?php

header('Content-type: text/xml');

require_once("../../config.php");

$episodes = $Podcast->getEpisodes();
$people = $Podcast->getPeople();

?>
<?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
	<url>
		<loc>https://www.painkilleralready.com/</loc>
		<changefreq>weekly</changefreq>
	</url>
	<url>
		<loc>https://www.painkilleralready.com/credits</loc>
		<changefreq>monthly</changefreq>
	</url>
	<url>
		<loc>https://www.painkilleralready.com/feedback</loc>
		<changefreq>monthly</changefreq>
	</url>
<?php

    foreach ($episodes as $episode) {
?>
		<url>
			<loc>https://www.painkilleralready.com/episode/<?php echo $episode->getNumber(); ?></loc>
			<changefreq>weekly</changefreq>
		</url>
<?php
    }

    foreach ($people as $person) {
?>
		<url>
            <loc>https://www.painkilleralready.com/person/<?php echo $person->getID(); ?></loc>
			<changefreq>monthly</changefreq>
		</url>
<?php
    }
?>
</urlset>