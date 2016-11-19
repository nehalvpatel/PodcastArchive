<?php

	header("Content-type: xml");

	require_once("config.php");
	
	echo '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
	
?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
	<ShortName><?php echo $Podcast->getTitle(); ?></ShortName>
	<Description>Search <?php echo $Podcast->getName(); ?> episodes</Description>
	<Url type="text/html" method="get" template="<?php echo $domain; ?>?query={searchTerms}" />
	<Image width="16" height="16"><?php echo $domain; ?>favicon.ico</Image>
	<InputEncoding>UTF-8</InputEncoding>
	<SearchForm><?php echo $domain; ?></SearchForm>
</OpenSearchDescription>