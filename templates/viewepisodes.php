<?php
	require_once("config.php");
	$query = $con->query("SELECT `Identifier`, `Number`, `YouTube` FROM `episodes` ORDER BY `Identifier` DESC");
	$query->execute();
	$episodes = $query->fetchAll();
	
	$query = $con->query("SELECT DISTINCT `episode` FROM `timestamps`");
	$query->execute();
	$timelines = $query->fetchAll(PDO::FETCH_ASSOC);
	$timelined_episodes = array();
	foreach($timelines as $timeline){
		$timelined_episodes[] = $timeline["episode"];
	}
	
?>
<h2>View Episodes</h2>
<table>
	<tr>
		<th>Identifier</th>
		<th>Link to Archive</th>
		<th>Link to YouTube</th>
		<th>Add / Edit Timeline</th>
	</tr>
<?php
	foreach($episodes as $episode){
?>
	<tr>
		<td><?php echo $episode["Identifier"]; ?></td>
		<td><a href="<?php echo $domain; ?>episode/<?php echo $episode["Number"]; ?>">Archive Link</a></td>
		<td><a href="https://www.youtube.com/watch?v=<?php echo $episode["YouTube"]; ?>">YouTube Link</a></td>
<?php
	if(in_array($episode["Identifier"], $timelined_episodes)){
?>
		<td><a href="admin.php?module=edittimeline&episode=<?php echo $episode["Number"]; ?>">Edit Timeline</a></td>
<?php
	} else {
?>
		<td><a href="admin.php?module=addtimeline&episode=<?php echo $episode["Number"]; ?>">Add Timeline</a></td>
<?php
	}
?>
	</tr>
<?php
	}
?>
</table>