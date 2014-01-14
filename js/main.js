var playerContainer;
$(document).ready(function() {
	// get YT player container
	playerContainer = document.getElementById("player");
	
	// add YT script tag
	var tag = document.createElement("script");
	var firstScriptTag = document.getElementsByTagName("script")[0];
	tag.src = "https://www.youtube.com/player_api";
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
	
	// scroll to active episode
	if (document.getElementById("active")) {
		$("#sidebar").animate({scrollTop:$("#active").position().top},1000);
	}
	
	if (document.getElementById("comments")) {
		$.get("http://www.reddit.com/comments/" + document.getElementById("comments").getAttribute("data-reddit") + ".json", function (data) {
			$("#comments").html(data[0]["data"]["children"][0]["data"]["num_comments"] + " Comments");
		});
	}
	
	// capture timestamp click events
	$(".timelink").click(function() {
		seekYT($(this).data("timestamp"));
		return false;
	});
});

// load YT player
var player;
function onYouTubePlayerAPIReady() {
	player = new YT.Player("player", {
		height: "400",
		width: "650",
		videoId: playerContainer.getAttribute("data-youtube")
	});
}

// click timestamp to seek video
function seekYT(timestamp) {
	player.seekTo(timestamp);
	document.getElementById("top").scrollIntoView();
}

// collapsible sidebar
$(function(){
	$(".toggle-menu").click(function(e){
		e.preventDefault();
		$(".sidebar").toggleClass("toggled");
	});
});

// horizontal timeline
function disappear(id){
	document.getElementById(id).style.display = "none";
}
function appear(id){
	document.getElementById(id).style.display = "block";
}