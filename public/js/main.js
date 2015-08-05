// remove parameters from URLs
function cleanURL(url) {
	if (url.indexOf("?") > -1) {
		url = url.slice(0, url.indexOf("?"));
	}
	
	return url;
}

// delete element if it exists
function tryDelete(selector) {
	var $delete_this = $(selector);
	if ($delete_this.length) {
		$delete_this.remove();
	}
}

function hideAllEpisodes() {
	$("#sidebar ul li").each(function(id, li){
		resetSearchResults(li);
		$(li).hide();
	});
}

function showAllEpisodes() {
	$("#sidebar ul li").each(function(id, li){
		resetSearchResults(li);
		$(li).show();
	});
}

// reset search results
function resetSearchResults(li) {
	$(li).removeAttr("data-begin");
	$(li).show();
	
	$(li).children().slice(1).remove();
	
	tryDelete(".search-result");
}

// get variables from URL
function getQueryVariable(variable, query_url) {
	query_url = typeof query_url !== 'undefined' ? query_url : window.location;
	
	var query_link = document.createElement("a");
	query_link.href = query_url;
	var query = query_link.search.substring(1);
	var vars = query.split("&");
	for (var i=0;i<vars.length;i++) {
		var pair = vars[i].split("=");
		if(pair[0] == variable){return pair[1];}
	}
	return(false);
}

// download data for new episode or use cache if possible
function loadContent(url, back_forward) {
	$("#loader").show();
	
	var search_timestamp = getQueryVariable("timestamp", url);
	
	if (cached_data.hasOwnProperty(url) == 1) {
		updateContent(cached_data[url], search_timestamp, back_forward);
	} else {
		var is_random = false;
		var extension = "";
		
		if (url.replace(domain, "").indexOf("random") > -1) {
			is_random = true;
			extension = "content/random";
		} else {
			extension = "content";
		}
		
		$.ajax({
			url: domain + extension,
			dataType: "json",
			data: {id: cleanURL(url)},
			async: true,
			success: function(content) {
				if (is_random) {
					cached_data[domain + "episode/" + $.parseJSON(content.Number)] = content;
				} else {
					cached_data[url] = content;
				}
				
				updateContent(content, search_timestamp, back_forward);
			},
			error: function(xhr, textStatus, error) {
				window.location.href = url;
			}
		});
	}
}

// replace old episode data with current episode data
function updateContent(episode_data, search_timestamp, back_forward) {
	// get current episode
	var $current_episode = $("[data-episode='" + episode_data.Identifier + "']");
	
	// update video
	if (search_timestamp) {
		player.loadVideoById(episode_data.YouTube, search_timestamp);
	} else {
		player.cueVideoById(episode_data.YouTube);
	}
	
	// change header
	$("#container h2").text(site_name + " #" + episode_data.Number);
	
	// update current episode
	$("nav ul").attr("data-current", episode_data.Identifier);
	
	// change date published
	var $published_time = $(".published time");
	$published_time.attr("datetime", episode_data.DateTime);
	$published_time.text(episode_data.Date);
	
	// change author name if timestamp is available
	tryDelete(".author");
	if (((episode_data.Timeline).hasOwnProperty("Author") == 1) && (episode_data.Timeline.Author.Name !== "")) {
		var $link = $("<a>", {"class": "author", "title": "Timeline Author", "href": episode_data.Timeline.Author.Link});
		$link.append($("<i>", {"class": "icon-user"}));
		$link.append($("<small>").text(episode_data.Timeline.Author.Name));
		$(".info").append($link);
	}
	
	// get comment count if possible
	tryDelete(".comments");
	if (episode_data.Reddit !== "") {
		var $comments_link = $("<a>", {"class": "comments", "title": "Discussion Comments", "href": "http://www.reddit.com/comments/" + episode_data.Reddit});
		var $icon = $("<i>", {"class": "icon-comments"});
		var $comment_text = $("<small>", {"data-reddit": episode_data.Reddit});
		$comment_text.text("Comments");
		
		$comments_link.append($icon);
		$comments_link.append($comment_text);
		
		$(".published").after($comments_link);
		
		setCommentCount($comment_text, episode_data.Reddit);
	}
	
	// update horizontal timeline if possible
	tryDelete("#timeline-horizontal");
	if ((episode_data.Timeline).hasOwnProperty("Timestamps") == 1) {
		var $timeline_horizontal = $("<div>", {"id": "timeline-horizontal", "class": "section"});
		$timeline_horizontal.append($("<h4>").addClass("section-header").text("Timeline"));
		
		var $line = $("<div>", {"class": "timeline"});
		$.each(episode_data.Timeline.Timestamps, function(timestamp_id, timestamp_data) {
			var $timelink = $("<a>", {"class": "timelink", "href": episode_data.Link + "?timestamp=" + timestamp_data.Begin, "data-begin": timestamp_data.Begin, "data-end": timestamp_data.End});
			var $topic = $("<div>", {"class": "topic"}).css({"width": timestamp_data.Width + "%"});
			var $tooltip = $("<div>", {"class": "tooltip", "id": timestamp_id});
			if (timestamp_data.Begin > (episode_data.YouTubeLength / 2)) {
				$tooltip.addClass("right");
			}
			var $triangle = $("<div>", {"class": "triangle"});
			var $span = $("<span>").text(timestamp_data.Value);
			$tooltip.append($triangle);
			$tooltip.append($span);
			$topic.append($tooltip);
			$timelink.append($topic);
			$line.append($timelink);
		});
		
		$timeline_horizontal.append($line);
		
		$("#timeline-clear").after($timeline_horizontal);
	}
	
	// update timestamp table if possible
	tryDelete("#timeline-vertical");
	if ((episode_data.Timeline).hasOwnProperty("Timestamps") == 1) {
		var $timeline_vertical = $("<table>", {"id": "timeline-vertical", "class": "section"});
		var $thead = $("<thead>");
		var $head_row = $("<tr>");
		var $time_column = $("<th>").text("Time");
		var $event_column = $("<th>").text("Event");
		$head_row.append($time_column);
		$head_row.append($event_column);
		$thead.append($head_row);
		$timeline_vertical.append($thead);
		
		var $tbody = $("<tbody>");
		$.each(episode_data.Timeline.Timestamps, function(timestamp_id, timestamp_data) {
			var $body_row = $("<tr>");
			var $timestamp = $("<td>", {"class": "timestamp"});
			var $timelink = $("<a>", {"class": "timelink", "href": episode_data.Link + "?timestamp=" + timestamp_data.Begin, "data-begin": timestamp_data.Begin, "data-end": timestamp_data.End}).text(timestamp_data.HMS);
			var $event = $("<td>", {"class": "event"});
			if (timestamp_data.URL !== "") {
				$event.append($("<a>", {"target": "_blank", "href": timestamp_data.URL}).text(timestamp_data.Value));
			} else {
				$event.text(timestamp_data.Value);
			}
			$timestamp.append($timelink);
			$body_row.append($timestamp);
			$body_row.append($event);
			$tbody.append($body_row);
		});
		
		$timeline_vertical.append($tbody);
		
		$("#footer-links").before($timeline_vertical);
	}
	
	// update active episode on the sidebar
	$("nav li").removeAttr("id");
	$current_episode.attr("id", "active");
	
	// update hosts box
	$("#video").after(generatePeople("hosts", "Hosts", episode_data.People));
	
	// update guests box
	$("#hosts").after(generatePeople("guests", "Guests", episode_data.People));
	
	// update sponsors box
	$("#timeline-clear").before(generatePeople("sponsors", "Sponsors", episode_data.People));
	
	// close sidebar if open
	var $sidebar = $(".sidebar");
	if ($sidebar.hasClass("toggled")) {
		$sidebar.removeClass("toggled");
	}
	
	// add page to history and update page title
	document.title = "Episode #" + episode_data.Number + " \u00B7 " + site_name;
	
	if (!back_forward) {
		if (search_timestamp) {
			history.pushState(null, null, episode_data.Link + "?timestamp=" + search_timestamp);
		} else {
			history.pushState(null, null, episode_data.Link);
		}
	}
	
	// hide loader
	$("#loader").hide();
}

// fetch reddit comment count
function setCommentCount(element, reddit_id) {
	$.ajax({
		url: "http://www.reddit.com/comments/" + reddit_id + ".json",
		dataType: "json",
		async: true,
		success: function(data) {
			element.text(data[0].data.children[0].data.num_comments + " Comments");
			element.parent().attr("href", data[0].data.children[0].data.url);
		},
		error: function(xhr, textStatus, error) {
			element.text("Comments");
		}
	});
}

// generate people containers
function generatePeople(id, name, data) {
	tryDelete("#" + id);
	if (data.hasOwnProperty(name) == 1) {
		var $people = $("<div>", {"id": id, "class": "section items"});
		var $header = $("<h4>").addClass("section-header").text(name);
		
		$people.append($header);
		
		$.each(data[name], function(person_id, person_data) {
			$people.append(generatePerson(person_data.ID, person_data.Name, person_data.URL));
		});
		
		return $people;
	}
}

// generate person image
function generatePerson(id, name, url) {
	var $link = $("<a>", {"class": "item", "target": "_blank", "href": domain + "person/" + id, title: name});
	var $avatar = $("<img>", {"class": "person-image", "alt": name, "src": domain + "img/people/" + id + ".png"});
	
	$link.append($avatar);
	
	return $link;
}

// load YT player
var player;
function onYouTubePlayerAPIReady() {
	player = new YT.Player("player", {
		events: {
			"onReady": onPlayerReady,
			"onStateChange": onPlayerStateChange
		}
	});
}

// setup pushState, handle search, and seek to timestamp
var cached_data = [];
function onPlayerReady() {
	// handle search query
	var search_query = getQueryVariable("query");
	if (search_query) {
		$("#search-field").val(search_query).trigger("input");
	}
	
	// seek to timestamp
	var search_timestamp = getQueryVariable("timestamp");
	if (search_timestamp) {
		// track in analytics
		if (typeof _gaq !== "undefined") {
			_gaq.push(["_trackEvent", "Timeline", "Seek", $("nav ul").attr("data-current"), parseInt(search_timestamp, 10)]);
		}
	}
	
	if ($("body").attr("data-type") == "episode") {
		var first_load = true;
		
		// check if pushState is available
		if (!!(window.history && history.pushState)) {
			// episode click interceptor
			$(document).on("click", "nav a", function(e) {
				// cancel navigation
				e.preventDefault();
				
				href = $(this).attr("href");
				
				// track page view
				if (typeof _gaq !== "undefined") {
					_gaq.push(["_trackPageview", "/" + href.replace(domain, "")]);
				}
				
				loadContent(href, false);
				
				// change the fact that this is not the original page load
				first_load = false;
			});
			
			// add popstate listener to catch back and forward navigation
			window.addEventListener("popstate", function() {
				// only do something if it's not the first page load
				if (!first_load) {
					loadContent(location.href, true);
				}
			});
		}
	}
}

// toggle timestamp checker
function onPlayerStateChange() {
	if (player.getPlayerState() === 1) {
		time_updater = setInterval(updateTime, 1000);
	} else {
		time_updater = null;
	}
}

// repeatedly check timestamp
var video_time = 0;
var time_updater = null;
function updateTime() {
	var old_time = video_time;
	if (player && player.getCurrentTime) {
		video_time = player.getCurrentTime();
	}
	if (video_time !== old_time) {
		onProgress(video_time);
	}
}

// highlight active timestamp
function onProgress(currentTime) {
	$(".timelink").each(function(timelink_id, timelink){
		if ((currentTime > $(timelink).attr("data-begin")) && (currentTime < $(timelink).attr("data-end"))) {
			if ($(timelink).parent().prop("tagName").toLowerCase() == "td") {
				$(timelink).parent().parent().addClass("active-timestamp-vertical");
			} else {
				$(timelink).children(":first").addClass("active-timestamp-horizontal");
			}
		} else {
			if ($(timelink).parent().prop("tagName").toLowerCase() == "td") {
				$(timelink).parent().parent().removeClass("active-timestamp-vertical");
			} else {
				$(timelink).children(":first").removeClass("active-timestamp-horizontal");
			}
		}
	});
}

$(document).ready(function() {
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
	
	// collapsible sidebar
	$(".toggle-menu").click(function(e){
		e.preventDefault();
		$(".sidebar").toggleClass("toggled");
	});
	
	// capture timestamp click events
	$(document).on("click", "a.timelink", function() {
		// seek to timestamp
		player.seekTo($(this).attr("data-begin"));
		document.getElementsByTagName("header")[0].scrollIntoView();
		
		// add URL to history
		history.pushState(null, null, window.location.protocol + "//" + window.location.hostname + window.location.pathname + "?timestamp=" + $(this).attr("data-begin"));
		
		// track in analytics
		if (typeof _gaq !== "undefined") {
			_gaq.push(["_trackEvent", "Timeline", "Seek", $("nav ul").attr("data-current"), parseInt($(this).attr("data-begin"), 10)]);
		}
		
		return false;
	});
	
	// live search
	var search_timer;
	var previous_search;
	$("#search-field").on("propertychange input", function() {
		clearTimeout(search_timer);
		var search_value = this.value;
		
		search_timer = setTimeout(function() {
			if ($.trim(search_value) !== "") {
				if (previous_search != search_value) {
					// track search in analytics
					if (typeof _gaq !== "undefined") {
						_gaq.push(["_trackEvent", "Search", "Search", search_value]);
					}
					
					$("#search-error").hide();
					
					$.ajax({
						url: domain + "search",
						dataType: "json",
						data: {"query": search_value},
						async: true,
						success: function(results_json) {
							previous_search = search_value;
							
							// hide all episodes
							hideAllEpisodes();
							
							if (!jQuery.isEmptyObject(results_json)) {
								$.each(results_json, function(episode_identifier, episode_timestamps) {
									// show only returned episodes
									var $episode = $("li[data-episode='" + episode_identifier + "']");
									
									$.each(episode_timestamps, function(timestamp_id, episode_timestamp) {
										var $result_link = $episode.children(":first");
										
										var $search_result = $("<a>").attr("href", "#");
										$search_result.attr("data-begin", episode_timestamp.Timestamp);
										$search_result.attr("href", $result_link.attr("href") + "?timestamp=" + episode_timestamp.Timestamp);
										
										var $search_text = $("<span>").addClass("search-result").attr("title", episode_timestamp.Value).html("<strong>" + episode_timestamp.HMS + "</strong> - <span>" + episode_timestamp.Value + "</span>");
										$search_result.append($search_text);
										
										$episode.append($search_result);
									});
									
									$episode.show();
								});
							}
						},
						error: function(xhr, textStatus, error) {
							$("#search-error").show();
							hideAllEpisodes();
						}
					});
				}
			} else {
				// reset episode list
				previous_search = "";
				
				$("#search-error").hide();
				showAllEpisodes();
			}
		}, 200);
	});
});