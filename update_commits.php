<?php

// Get commit count using git
ob_start();
passthru("git --git-dir " . __DIR__ . "/.git rev-parse HEAD");
$commit_count = trim(ob_get_contents());
ob_end_clean();

if ($commit_count == "") {
	$commit_count = "0";
}

// Write it to file
$fh = fopen(__DIR__ . "/commits.txt", "w");
fwrite($fh, $commit_count);
fclose($fh);