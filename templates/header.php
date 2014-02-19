		<aside class="sidebar">
			<nav id="sidebar">
				<div class="search-form"><input class="search-field" type="search" id="search-field" name="search" placeholder="Search"></div>
				<h3>Episodes</h3>
				<div id="search-error" class="error">
					<p>There was an error with the search engine.<br><br>Please message /u/nehalvpatel on reddit.</p>
				</div>
				<ul data-current="<?php echo (isset($current_episode)) ? $current_episode->getIdentifier() : null; ?>">
<?php
	foreach ($Podcast->getEpisodes() as $episode) {
?>
					<li data-episode="<?php echo $episode["Identifier"]; ?>"<?php if ((isset($_GET["episode"])) && ($episode["Number"] == $_GET["episode"])) { echo ' id="active"'; } ?>>
						<a href="<?php echo $domain; ?>episode/<?php echo $episode["Number"]; ?>" class="<?php if(isset($highlighted_episodes)){if ($highlighted_episodes[$episode["Identifier"]]) { echo "highlighted-episode"; }} ?>">#<?php echo $episode["Number"]; ?></a>
					</li>
<?php
	}
?>
				</ul>
			</nav>
		</aside>
		<section class="main">
			<header>
				<a href="#" class="toggle-menu icon-reorder"></a>
				<h1>Painkiller Already Archive</h1>
			</header>