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
						<a href="<?php echo $domain; ?>episode/<?php echo $episode["Number"]; ?>" class="<?php if(isset($highlighted_episodes)){if ($highlighted_episodes[$episode["Identifier"]]) { echo "highlighted-episode"; }} ?>"><span>#<?php echo $episode["Number"]; ?></span><?php if(in_array($episode["Identifier"], $timelined_episodes)){ ?><span class="timelined"></span><?php } ?></a>
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
<?php
	if(!isset($_SESSION["username"])){
?>
				<button id="login">Log In</button>
<?php
	} else {
?>
				<form action="<?php echo $domain; ?>" method="post">
					<input type="hidden" name="form" value="logout" />
					<input type="submit" value="Log Out" />
				</form>
<?php
	}
?>
				<form action="<?php echo $domain; ?>" method="post" id="loginform">
					<input type="text" name="username" placeholder="Username" />
					<input type="password" name="password" placeholder="Password" />
					<input type="hidden" name="form" value="login" />
					<input type="submit" value="Log In" />
				</form>
			</header>
			<div id="container">