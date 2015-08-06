<?php

namespace PainkillerAlready;

class Utilities
{
	public static function getBaseDomain()
	{
		return rtrim($_SERVER["HTTP_HOST"] . str_replace(basename($_SERVER["PHP_SELF"]), "", $_SERVER["PHP_SELF"]), "/");
	}
	
	public static function getDomain()
	{
		return "http://" . self::getBaseDomain() . "/";
	}
	
	public static function getCommitCount()
	{
		ob_start();
		passthru("git rev-list --count HEAD");
		$commit_count = trim(ob_get_contents());
		ob_end_clean();
		
		if ($commit_count == "") {
			$commit_count = "0";
		}
		
		return $commit_count;
	}
	
	public static function trimEpisodeNumber($episode)
	{
		$episode = ltrim($episode, "0");
		
		if ($episode == "") {
			return "0";
		} else {
			return ltrim($episode, "0");
		}
	}
	
	public static function padEpisodeNumber($episode)
	{
		$episode = self::trimEpisodeNumber($episode);
		
		if ($episode == "0") {
			return "000";
		} else {
			if (is_numeric($episode) && floor($episode) != $episode) {
				$episode = str_pad($episode, 4, "0", STR_PAD_LEFT);
			} else {
				$episode = str_pad($episode, 3, "0", STR_PAD_LEFT);
			}
			
			return $episode;
		}
	}
	
	public static function validateTimestamp($timestamp)
	{
		$pattern = "/^(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d)$/";
		preg_match($pattern, substr($timestamp, 3), $matches, PREG_OFFSET_CAPTURE);
		
		if (count($matches) == 0) {
			return false;
		} else {
			return true;
		}
	}
	
	public static function convertTimestamp($timestamp)
	{
		sscanf($timestamp, "%d:%d:%d", $hours, $minutes, $seconds);
		return isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
	}
	
	public static function convertToHMS($timestamp)
	{
		$hours = floor($timestamp / 3600);
		$minutes = floor(($timestamp / 60) % 60);
		$seconds = $timestamp % 60;
		
		return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
	}
}