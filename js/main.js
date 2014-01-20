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
	
	// load comment count
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
	
	// live search
	var search_timer;
	var previous_search;
	$("#search-field").on("input", function() {
		clearTimeout(search_timer);
		var search_value = this.value;
		
		search_timer = setTimeout(function() {
			if ($.trim(search_value) != "") {
				if (previous_search != search_value) {
					previous_search = search_value;
					
					$.getJSON(domain + "search.php?query=" + search_value, function(results_json){
						// hide all episodes
						$("#sidebar ul li").each(function(id, li){
							$(li).css("display", "none");
						});
						
						if (results_json.length > 0) {
							$.each(results_json, function(result_id) {
								// show only returned episodes
								var episode = "li[data-episode='" + results_json[result_id] + "']";
								$(episode).css("display", "block");
							});
						}
					});
				}
			} else {
				// reset episode list
				previous_search = "";
				
				$("#sidebar ul li").each(function(id, li){
					$(li).css("display", "block");
				});
			}
		}, 200);
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
	document.getElementsByTagName("header")[0].scrollIntoView();
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