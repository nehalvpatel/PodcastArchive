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
	
	// load comment count on load
	if (document.getElementById("comments")) {
		setCommentCount($("#comments"), document.getElementById("comments").getAttribute("data-reddit"));
	}
	
	// capture timestamp click events
	$("body").on("click", "a.timelink", function() {
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

// setup pushState
var cached_data = [];
$(function() {
	// check if available
	var hasPushstate = !!(window.history && history.pushState);
	
	// episode click interceptor
	$("nav a").click(function(e) {
		if (hasPushstate) {
			e.preventDefault();
			
			href = $(this).attr("href");
			history.pushState(null, null, href);
			loadContent(href);
		}
	});
	
	// add popstate listener to catch back and forward navigation
	if (hasPushstate) {
		window.addEventListener("load", function() {
			setTimeout(function() {
				window.addEventListener("popstate", function() {
					loadContent(location.href);
				});
			}, 0);
		});
	}
});

// download data for new episode or use cache if possible
function loadContent(url) {
	if (cached_data.hasOwnProperty(url) == 1) {
		updateContent(cached_data[url]);
	} else {
		$.ajax({
			url: domain + "content.php",
			dataType: "json",
			data: {id: url},
			async: true,
			success: function(json) {
				cached_data[url] = json;
				updateContent(json);
			},
			error: function(xhr, textStatus, error) {
				window.location.href = url;
			}
		});
	}
}

// replace old episode data with current episode data
function updateContent(episode_data) {
	document.title = "Episode #" + episode_data["Number"] + " \u00B7 Painkiller Already Archive";
	
	// update video
	player.loadVideoById(episode_data["YouTube"]);
	
	// change header
	$("#container h2").text("Painkiller Already #" + episode_data["Number"]);
	
	// change date published
	$(".published time").attr("datetime", episode_data["DateTime"]);
	$(".published time").text(episode_data["Date"]);
	
	// change author name if timestamp is available
	var $author = $(".author");
	if ($author.length) {
		$author.remove();
	}
	
	if (((episode_data["Timeline"]).hasOwnProperty("Author") == 1) && (episode_data["Timeline"]["Author"] != "")) {
		var $link = $("<a>", {class: "author", title: "Timeline Author", href: "http://www.reddit.com/user/" + episode_data["Timeline"]["Author"]});
		var $icon = $("<i>", {class: "icon-user"});
		var $author_text = $("<small>");
		$author_text.text(episode_data["Timeline"]["Author"]);
		
		$link.append($icon);
		$link.append($author_text);
		
		$(".info").append($link);
	}
	
	// get comment count if possible
	var $comments = $(".comments");
	if ($comments.length) {
		$comments.remove();
	}
	
	if (episode_data["Reddit"] != "") {
		var $link = $("<a>", {class: "comments", title: "Discussion Comments", href: "http://www.reddit.com/comments/" + episode_data["Reddit"]});
		var $icon = $("<i>", {class: "icon-comments"});
		var $comment_text = $("<small>", {"data-reddit": episode_data["Reddit"]});
		$comment_text.text("Comments");
		
		$link.append($icon);
		$link.append($comment_text);
		
		$(".published").after($link);
		
		setCommentCount($comment_text, episode_data["Reddit"]);
	}
	
	// update horizontal timeline if possible
	var $horizontal_timeline = $("#timeline-horizontal");
	if ($horizontal_timeline.length) {
		$horizontal_timeline.remove();
	}
	
	if ((episode_data["Timeline"]).hasOwnProperty("Timestamps") == 1) {
		var $timeline = $("<div>", {id: "timeline-horizontal"});
		$timeline.append($("<h4>").text("Timeline"));
		
		var $line = $("<div>", {id: "line"});
		$.each(episode_data["Timeline"]["Timestamps"], function(i, timestamp_data) {
			var $timelink = $("<a>", {class: "timelink", href: "https://www.youtube.com/watch?v=" + episode_data["YouTube"] + "#t=" + timestamp_data["Seconds"], "data-timestamp": timestamp_data["Seconds"]});
			var $topic = $("<div>", {class: "topic", style: "width:" + timestamp_data["Width"] + "%", "onmouseover": "appear('" + timestamp_data["ID"] + "');", "onmouseout": "disappear('" + timestamp_data["ID"] + "');"});
			var $tooltip = $("<div>", {class: "tooltip", id: timestamp_data["ID"], style: "display: none"});
			var $triangle = $("<div>", {class: "triangle"});
			var $span = $("<span>").text(timestamp_data["Value"]);
			$tooltip.append($triangle);
			$tooltip.append($span);
			$topic.append($tooltip);
			$timelink.append($topic);
			$line.append($timelink);
		});
		
		$timeline.append($line);
		
		$("#timeline-clear").after($timeline);
	}
	
	// update timestamp table if possible
	var $vertical_timeline = $("#timeline-vertical");
	if ($vertical_timeline.length) {
		$vertical_timeline.remove();
	}
	
	if ((episode_data["Timeline"]).hasOwnProperty("Timestamps") == 1) {
		var $timeline = $("<div>", {id: "timeline-vertical"});
		var $table = $("<table>", {id: "timeline-table"});
		var $thead = $("<thead>");
		var $head_row = $("<tr>");
		var $time_column = $("<th>").text("Time");
		var $event_column = $("<th>").text("Event");
		$head_row.append($time_column);
		$head_row.append($event_column);
		$thead.append($head_row);
		$table.append($thead);
		
		var $tbody = $("<tbody>");
		$.each(episode_data["Timeline"]["Timestamps"], function(i, timestamp_data) {
			var $body_row = $("<tr>");
			var $timestamp = $("<td>", {class: "timestamp"});
			var $timelink = $("<a>", {class: "timelink", href: "https://www.youtube.com/watch?v=" + episode_data["YouTube"] + "#t=" + timestamp_data["Seconds"], "data-timestamp": timestamp_data["Seconds"]}).text(timestamp_data["HMS"]);
			var $event = $("<td>", {class: "event"}).text(timestamp_data["Value"]);
			$timestamp.append($timelink);
			$body_row.append($timestamp);
			$body_row.append($event);
			$tbody.append($body_row);
		});
		
		$table.append($tbody);
		$timeline.append($table);
		
		$("#container").append($timeline);
	}
	
	// update active episode on the sidebar
	$("nav li").removeAttr("id");
	$("[data-episode='" + episode_data["Identifier"] + "']").attr("id", "active");
	
	// update hosts box
	$("#video").after(generatePeople("hosts", "Hosts", episode_data["People"]));
	
	// update guests box
	$("#hosts").after(generatePeople("guests", "Guests", episode_data["People"]));
	
	// update sponsors box
	$("#timeline-clear").before(generatePeople("sponsors", "Sponsors", episode_data["People"]));
}

// fetch reddit comment count
function setCommentCount(element, reddit_id) {
	$.ajax({
		url: "http://www.reddit.com/comments/" + reddit_id + ".json",
		dataType: "json",
		async: true,
		success: function(data) {
			element.text(data[0]["data"]["children"][0]["data"]["num_comments"] + " Comments");
		},
		error: function(xhr, textStatus, error) {
			element.text("Comments");
		}
	});
}

// generate people containers
function generatePeople(id, name, data) {
	var $people = $("#" + id);
	if ($people.length) {
		$people.remove();
	}
	
	if (data.hasOwnProperty(name) == 1) {
		var $people = $("<div>", {"id": id, class: "people"});
		var $header = $("<h4>").text(name);
		
		$people.append($header);
		
		$.each(data[name], function(i, person_data) {
			$people.append(generatePerson(person_data["Name"], person_data["Image"], person_data["URL"]));
		});
		
		return $people
	}
}

// generate person image
function generatePerson(name, image, url) {
	var $link = $("<a>", {target: "_blank", href: url, title: name});
	var $person = $("<div>", {class: "person"});
	var $avatar = $("<img>", {alt: name, src: domain + image});
	
	$person.append($avatar);
	$link.append($person);
	
	return $link;
}

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