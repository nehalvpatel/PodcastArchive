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
		$("#sidebar").animate({scrollTop:$('#active').position().top},1000);
	}
});

// load YT player
var player;
function onYouTubePlayerAPIReady() {
	console.log("ready");
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
	$('.toggle-menu').click(function(e){
		e.preventDefault();
		$('.sidebar').toggleClass('toggled');
	});
});