<?php

	header("Content-type: text/plain");
	
	require_once("config.php");

?>
User-agent: *
Disallow: /cdn-cgi/

Sitemap: <?php echo $domain; ?>sitemap.xml