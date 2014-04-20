		<aside class="sidebar">
			<nav id="sidebar">
				<div class="search-form"><input class="search-field" type="search" id="search-field" name="search" placeholder="Search"></div>
				<h3><span>Episodes</span><a title="Random Episode" href="<?php echo $domain; ?>episode/random" class="random-button timelined"><i class="icon-random"></i></a></h3>
				<div id="search-error" class="error">
					<p>There was an error with the search engine.<br><br>Please message /u/nehalvpatel on reddit.</p>
				</div>
				<ul data-current="<?php echo (isset($current_episode)) ? $current_episode->getIdentifier() : null; ?>">
<?php
	$timelined_episodes = $Podcast->getTimelinedEpisodes();
	foreach ($Podcast->getEpisodes() as $episode) {
?>
					<li data-episode="<?php echo $episode["Identifier"]; ?>"<?php if ((isset($_GET["episode"])) && ($episode["Number"] == $_GET["episode"])) { echo ' id="active"'; } ?>>
						<a href="<?php echo $domain; ?>episode/<?php echo $episode["Number"]; ?>" class="<?php if(isset($highlighted_episodes)){if ($highlighted_episodes[$episode["Identifier"]]) { echo "highlighted-episode"; }} ?>"><span>#<?php echo $episode["Number"]; ?><?php if(in_array($episode["Identifier"], $timelined_episodes)){ ?></span><span class="timelined"></span><?php } ?></a>
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
				<h1><?php echo $Podcast->getTitle(); ?></h1>
			</header>
			<div id="container">